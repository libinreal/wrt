define(function(require, exports, module) {
    var $ = require('jquery'),
        config = require('./config');

    var options = {
        paging: {
            type: 'GET', //请求类型
            renderFor: 'zj-list-tmpl', //渲染模版
            renderEle: '#zj-list', //渲染容器
            callback:function(){}, //请求成功后执行的回调方法
            callbackDone: function(){} //请求成功后最后执行的方法
        },
        detail: {
            type: 'GET', //请求类型
            renderFor: 'zj-detail-tmpl', //渲染模版
            renderEle: '#zj-detail', //渲染容器
            callback:function(){}, //请求成功后执行的回调方法
            callbackDone: function(){} //请求成功后最后执行的方法
        },
        submit: {
            type: 'POST', //请求类型
            callback:function(){}, //请求成功后执行的回调方法
            callbackDone: function(){}, //请求成功后最后执行的方法
            callbackError:function(){} //请求失败后执行的方法
        }
    },timeout = 7000;

    function Ajax(options) {
        this.options = options || {};
        //extends(options,this.options);
    }

    module.exports = Ajax;

    Ajax.prototype._init = function() {
        var spinnings = this.spinnings;

        return this;
    }

    /**
     * 分页查询
     */
    Ajax.prototype.paging = function() {
        var that = this,
            renderFor = data.renderFor || 'wu-list-tmpl',
                renderEle = data.renderEle || '#wu-list';

        var next = $('.wlist_next'),
            nextStr = '<a class="wlist_next" href="#">下一页</a>';
        if (next.length == 0) {
            $(that.options.paging.renderEle).after(nextStr);
            next = $('.wlist_next');
        }
        if (config.begin == 0) {
            //查第一页数据一定清空当前容器
            $(that.options.paging.renderEle).html('');
        }
        next.show().addClass('loading').text(config.tips.loading);

        $.ajax({
            url: that.options.paging.url,
            data: that.options.paging.data,
            type: that.options.paging.type,
            timeout: 7000,
            //cache: false
        }).then(function(response, textStatus, jqXHR) {
            next.removeClass('loading');
            if (response.code != 'OK') {
                next.text(config.tips.server);
                return;
            }

            if ($('#' + that.options.paging.renderFor).length) {
                var result = template.render(that.options.paging.renderFor, {
                    'list': response.result
                });
                $(that.options.paging.renderEle).append(result);
            }

            if (response.result.length == 0) {
                //数据没有结果显示无数据提示
                if (config.begin == 0) {
                    next.html(config.tips.nodata);
                } else {
                    next.text(config.tips.nomoredata);
                }
            } else {
                config.begin += config.pagesize;
                next.text('更多');
            }

            if (response.countData > 0) {
                if (response.countData <= response.begin + response.count) {
                    //能预判断无下一页时显示无数据提示
                    //next.text(config.tips.nodata);
                    next.hide();
                } else {}
            }

            if ($.isFunction(that.options.paging.callback)) {
                that.options.paging.callback(response);
            }
        }).done(function(xhr, b) {
            if ($.isFunction(that.options.paging.callbackDone)) {
                that.options.paging.callbackDone();
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            if (textStatus == 'timeout') {
                next.removeClass('loading').addClass('error').text(config.tips.timeout);
            } else {
                next.removeClass('loading').addClass('error').text(config.tips.server);
            }
        });
    };


    /**
     * 详情查询
     */
    Ajax.prototype.detail = function() {
        var that = this;

        $.ajax({
            url: that.options.detail.url,
            data: that.options.detail.data,
            type: that.options.detail.type,
            timeout: 7000,
            //cache: false
        }).then(function(response) {
            if ($('#' + that.options.detail.renderFor).length && response.result) {
                var result = template.render(that.options.detail.renderFor, response.result);
                $(that.options.detail.renderEle).html(result);
            }
            if ($.isFunction(that.options.detail.callback)) {
                that.options.detail.callback(response);
            }
        }).done(function() {
            if ($.isFunction(that.options.detail.callbackDone)) {
                that.options.detail.callbackDone();
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            if (textStatus == 'timeout') {
                $(that.options.detail.renderEle).addClass('error').text(config.tips.timeout);
            } else {
                $(that.options.detail.renderEle).addClass('error').text(config.tips.server);
            }
            if ($.isFunction(that.options.detail.callbackError)) {
                that.options.detail.callbackError();
            }
        });
    };
    /**
     * 表单提交
     */
    Ajax.prototype.submit = function(data) {
        var formData;

        var isForm = !!data.data.length;
        if (isForm) {
            formData = data.data.serializeArray();
            data.data.find('input[type="submit"]').attr('disabled', true);
        } else {
            formData = data.data;
        }

        $.ajax({
            url: data.options.submit.url,
            data: formData,
            type: data.options.submit.type,
            timeout: 7000
        }).then(function(response) {
            if ($.isFunction(data.options.submit.callback)) {
                data.options.submit.callback(response);
            }
        }).done(function() {
            if (isForm) {
                data.data.find('input[type="submit"]').removeAttr('disabled');
            }
            if ($.isFunction(data.options.submit.callbackDone)) {
                data.options.submit.callbackDone();
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            if (isForm) {
                data.data.find('input[type="submit"]').removeAttr('disabled');
                Tools.showTip(config.tips.server, 5000);
            }
        });
    };
    /**
     * 自定义查询
     *
     * @param data-封装请求url，请求数据，请求类型
     * @param callback-请求成功后执行的回调方法
     * @param callbackDone-请求成功后最后执行的方法
     * @param callbackError-请求失败后执行的方法
     */
    Ajax.prototype.custom = function(data, callback, callbackDone, callbackError) {
        $.ajax({
            url: data.url,
            data: data.data,
            type: data.type || 'GET',
            timeout: 7000,
            //cache: false
        }).then(function(response) {
            if ($.isFunction(callback)) {
                callback(response);
            }
        }).done(function() {
            if ($.isFunction(callbackDone)) {
                callbackDone();
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            if ($.isFunction(callbackError)) {
                callbackError();
            }
        });
    };

});
