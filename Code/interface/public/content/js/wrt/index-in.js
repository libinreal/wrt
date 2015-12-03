define(function(require) {
    var $ = require('jquery'),
        config = require('../personal/config'),
        Ajax = require('../base/ajax');

    require('../base/common');

    // 模板帮助方法，确认订单状态
    template.helper('$getStatus', function(content) {
        if(!content)
            return '--';
        return content == -1 ? '订单未验签':(content == 0 ? '订单验签中': '订单已验签');
    });

    //回填用户相关数据
    var tempIcon;
    config.afterCheckAccount = function(account) {
        tempIcon = account.icon;
        $('.user-icon').attr('src', config.getUserIcon(account.icon))
    }

    //获取列表数据
    config.paging = function() {
        Ajax.paging({
            url: config.getlist,
            data: {
                size: config.pageSize
            }
        });
    };
    config.paging();

    //通过信用额度登记机构获取用户信息
    Ajax.custom({
        url: config.mycreamt
    }, function(response) {
        if (response.code != 0) {
            return;
        }

        var data = response.body;

        if (tempIcon) {
            data.icon = config.getUserIcon(tempIcon); //用户头像
        }

        var result = template.render('zj-summary-tmpl', data);
        $('#zj-summary').html(result);

    });

});