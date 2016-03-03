define(function(require, exports, module) {
    var $ = require('jquery'),
        template = require('template'),
        cc = require('./template-config'),
        config = require('./config'),
        msg = require('./messages'),
        Tools = require('../base/tools');

    /**
     * 将form中的值转换为键值对
     *
     * @param form-表单对象
     */
    var formJson = function(form) {
        var o = {};
        var a = $(form).serializeArray();
        $.each(a, function() {
            if (o[this.name] !== undefined) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    };

    var Ajax = {
        /**
         * 分页查询
         *
         * @param data-封装请求url，请求数据，请求类型，渲染容器，渲染模版
         * @param callback-请求成功后执行的回调方法
         * @param callbackError-请求失败后执行的回调方法
         */
        paging: function(data, callback, callbackError) {
            var renderFor = data.renderFor || 'zj-list-tmpl',
                renderEle = data.renderEle || '#zj-list',
                timeKey = data.timeKey || 'createAt',
                isPrev = config.forward == 1,//是否上一页操作
                isFirst = typeof config.forward != 'number';//是否第一次请求

            var next = $('.nextpage'),
                nextStr = '<div class="nextpage" style="display: none;"><a class="zj-prev disabled" href="#">上一页</a><a class="zj-next disabled" href="#">下一页</a></div>';
            if (next.length == 0 && (data.data && data.data.size)) {
                $(renderEle).after(nextStr);
                next = $('.nextpage');
            }

            if (isFirst) {
                //查第一页数据一定清空当前容器
                $(renderEle).html('');
            }
            
            if (data.data && typeof config.forward == 'number') {
                data.data[timeKey] = config.forward ? config.beginTime : config.endTime
                data.data.forward = config.forward;
            }

            //Tools.showLoading();

            $.ajax({
                url: data.url,
                data: data.data,
                type: data.type || 'GET',
                dataType: 'JSON',
                timeout: 7000,cache: false
            }).then(function(response, textStatus, jqXHR) {
                next.removeClass('loading');
                Tools.hideLoading();
                if (response.code != 0) {
                    log('[paging] ' + (response.message || response) + ':' + data.url);
                    next.addClass('error').text(response.message || response.body || msg.errordata);

                    if(response.code == -6){
                        location.href = '../index.html';
                    }
                    return;
                }

                var body = response.body || [];
                if(body && typeof body.length == 'undefined'){
                    //body为对象且非数组
                    body = response.body[data.key];
                }

                if (body.length == 0) {
                    //数据没有结果显示无数据提示
                    if (isFirst) {
                        next.hide();
                        $(renderEle).html(config.nodata);
                    } else {
                        //$(renderEle).html(msg.nomoredata);
                    }
                    if (isPrev) {
                        $('.zj-prev').addClass('disabled');
                    } else {
                        $('.zj-next').addClass('disabled');
                    }
                } else {
                    next.show();
                    if ($('#' + renderFor).length) {
                        var result = template.render(renderFor, {
                            'list': body || []
                        });
                        $(renderEle).html(result);
                    }

                    if (isPrev) {
                        $('.zj-next').removeClass('disabled');
                    } else {
                        if(!isFirst){
                            $('.zj-prev').removeClass('disabled');
                        }
                        if (body.length < config.pageSize) {
                            $('.zj-next').addClass('disabled');
                        }else{
                            $('.zj-next').removeClass('disabled');
                        }
                    }

                    config.beginTime = body[0][timeKey];
                    config.endTime = body[body.length - 1][timeKey];
                }

                if (typeof callback == 'function') {
                    callback(response);
                }
            }, function(jqXHR, textStatus, errorThrown) {
                log('[paging] ' + textStatus + ':' + data.url);
                if (typeof callbackError == 'function') {
                    callbackError(jqXHR, textStatus, errorThrown);
                }
            });
        },
        /**
         * 详情查询
         *
         * @param data-封装请求url，请求数据，请求类型，渲染容器，渲染模版
         * @param callback-请求成功后执行的回调方法
         * @param callbackError-请求失败后执行的回调方法
         */
        detail: function(data, callback, callbackError) {
            var renderFor = data.renderFor || 'zj-detail-tmpl',
                renderEle = data.renderEle || '#zj-detail';

            if(data.showLoading){
                $(renderEle).html('<div class="loading">加载中...</div>');
            }

            $.ajax({
                url: data.url,
                data: data.data,
                type: data.type || 'GET',
                dataType: 'JSON',
                timeout: 7000
            }).then(function(response, textStatus, jqXHR) {
                if (response.code != 0) {
                    log('[detail] ' + (response.message || response) + ':' + data.url);
                    $(renderEle).text(response.message || msg.errordata);
                    
                    if(response.code == -6){
                        location.href = '../index.html';
                    }
                    return;
                }
                if ($('#' + renderFor).length && response.body) {
                    var result = template.render(renderFor, response.body);
                    $(renderEle).html(result);
                }
                if (typeof callback == 'function') {
                    callback(response);
                }
            }, function(jqXHR, textStatus, errorThrown) {
                log('[detail] ' + textStatus + ':' + data.url);
                if (typeof callbackError == 'function') {
                    callbackError(jqXHR, textStatus, errorThrown);
                }
            });
        },
        /**
         * 表单提交
         *
         * @param data-传入的参数
         * @param callback-请求成功后执行的回调方法
         * @param callbackError-请求失败后执行的回调方法
         */
        submit: function(data, callback, callbackError) {
            var formData;

            var isForm = !!data.data.length;
            if (isForm) {
                formData = data.data.serializeArray();
                data.data.find('input[type="submit"]').attr('disabled', true);
            } else {
                formData = data.data;
            }

            $.ajax({
                url: data.url,
                data: formData,
                type: data.type || 'POST',
                dataType: 'JSON',
                timeout: 7000
            }).then(function(response, textStatus, jqXHR) {
                if (!response || response.code != 0) {
                    log('[submit] ' + response.message + ':' + data.url);
                }
                if (isForm) {
                    data.data.find('input[type="submit"]').removeAttr('disabled');
                }
                if (typeof callback == 'function') {
                    callback(response);
                }
            }, function(jqXHR, textStatus, errorThrown) {
                log('[submit] ' + textStatus + ':' + data.url);
                if (isForm) {
                    data.data.find('input[type="submit"]').removeAttr('disabled');
                }
                if (typeof callbackError == 'function') {
                    callbackError(jqXHR, textStatus, errorThrown);
                }
            });
        },
        /**
         * 自定义查询
         *
         * @param data-封装请求url，请求数据，请求类型
         * @param callback-请求成功后执行的回调方法
         * @param callbackError-请求失败后执行的回调方法
         */
        custom: function(data, callback, callbackError) {
            $.ajax({
                url: data.url,
                data: data.data,
                type: data.type || 'GET',
                dataType: 'JSON',
                timeout: 7000
            }).then(function(response, textStatus, jqXHR) {
                if (response.code != 0) {
                    log('[custom] ' + (response.message || response) + ':' + data.url);
                }
                if (typeof callback == 'function') {
                    callback(response);
                }
            }, function(jqXHR, textStatus, errorThrown) {
                log('[custom] ' + textStatus + ':' + data.url);
                if (typeof callbackError == 'function') {
                    callbackError(jqXHR, textStatus, errorThrown);
                }
            });
        },
        test: function() {
            return $('body').length;
        },
        formJson: function(form) {
            var o = {};
            var a = $(form).serializeArray();
            $.each(a, function() {
                if (o[this.name] !== undefined) {
                    if (!o[this.name].push) {
                        o[this.name] = [o[this.name]];
                    }
                    o[this.name].push(this.value || '');
                } else {
                    o[this.name] = this.value || '';
                }
            });
            return o;
        }
    };

    module.exports = Ajax;
});
