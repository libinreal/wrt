define(function(require) {
    var $ = require('jquery'),
        config = require('./config'),
        Ajax = require('../base/ajax'),
        Tools = require('../base/tools'),
        msg = require('../base/messages');

    require('../base/common');

    // 模板帮助方法，确定收藏商品的链接
    template.helper('$getLink', function(code, id) {
        if (!code)
            return '#';

        var key = code.substring(0, 1),
            host, param;

        if (key == '1' || (key == '2' && code.indexOf('2002') != 0)) {
            host = '../mall/list-large.html';
            param = '?code=' + code;
            if(id){
                param += '&id=' + id;
            }
        } else {
            host = '../mall/detail.html';
            param = '?id=' + id;
        }

        return host + param;
    });

    //获取收藏列表
    config.getCollectList = function() {
        Ajax.paging({
            url: config.iSeeCollect,
            renderFor: 'collectListTmpl',
            renderEle: '#collectListData'
        });
    }

    if ($('#collectListTmpl').length) {
        config.getCollectList();
    }

    //取消收藏
    $('#collectListData').on('click', '.removeCollect', function() {
        var that = $(this),
            par = that.parent();

        Tools.showConfirm('确认删除该收藏吗？', function() {

            Ajax.custom({
                url: config.iCancelCollect,
                data: {
                    id: that.attr('data-id')
                },
                type: 'POST'
            }, function(result) {
                if (!result || result.code != 0) {
                    return;
                }
                par.fadeOut('normal', function() {
                    if ($('#collectListData').children().length == 0) {
                        $('#collectListData').html(config.nodata);
                    }
                });
            });
        });
    });

});
