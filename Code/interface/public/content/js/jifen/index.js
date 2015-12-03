define(function(require) {
    var $ = require('jquery'),
        config = require('./config'),
        Ajax = require('../base/ajax'),
        Tools = require('../base/tools');

    require('../base/common');
    require('scrollable');

    //模板帮助方法，确定积分商城楼信息
    template.helper('$getFloor', function(content, i) {
        if (!content || typeof content != 'object') {
            return;
        }
        return content[i];
    });

    //回填用户相关数据
    config.afterCheckAccount = function(account){
        $('#credits').text(account.credits || 0);
    }

    //获取下拉菜单
    Ajax.custom({
        url: config.getCategorys
    }, function(response) {
        var category = response.body;
        var result = template.render('zj-category-tmpl', {
            'list': response.body || []
        });
        $('#zj-category').append(result);

        //列表
        Ajax.custom({
            url: config.getList,
            data: {
                size: 7
            }
        }, function(response) {
            var data = response.body;
            var arr = [];
            for (var i in data) {
                arr.push(data[i]);
            }

            var result = template.render('zj-jifen-tmpl', {
                'list': arr,
                'category': category
            });
            $('#zj-jifen').html(result);

        });
    });
    

    //获取公告
    config.getNotice(config.NOTICEENUM.JFSC,4);

    //获取广告
    config.getAd(config.ADENUM.JFSC);

});
