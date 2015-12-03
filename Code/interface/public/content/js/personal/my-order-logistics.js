define(function(require) {
    var $ = require('jquery'),
        config = require('./config'),
        Ajax = require('../base/ajax'),
        Tools = require('../base/tools');

    require('../base/common');
    
    // 模板帮助方法，确认订单的发票类型
    template.helper('$getInvType', function(content) {
        if(!content)
            return '--';
        return config.INVTYPE[content] || '--';
    });
    // 模板帮助方法，确认订单的支付机构
    template.helper('$getPayOrg', function(content) {
        if(!content)
            return '--';
        return config.PAYENUM[content] || '--';
    });
    // 模板帮助方法，确认子订单状态
    template.helper('$getStatus', function(content) {
        if(!content)
            return '--';
        return config.SUBORDERSTATUS[content] || '--';
    });

    var id = Tools.getQueryValue('id');

    //获取订单物流详情
    Ajax.detail({
        url: config.orderlogistics,
        data: {
            id: id
        }
    });

    //催办订单
    $('#zj-detail').on('click', '.btn-cuiban', function(e) {
        e.preventDefault();

        var that = $(this);
        if (that.hasClass('disabled')) {
            return;
        }

        Ajax.custom({
            url: config.orderrequestProc,
            data: {
                id: that.attr('data-id')
            },
            type: 'POST'
        }, function(response) {
            if (response.code != 0) {
                return;
            }
            that.addClass('disabled').text('已催办');
        });
    });

    //我要验收
    $('#zj-detail').on('click', '.btn-yanshou', function(e) {
        e.preventDefault();

        var that = $(this);
        if(that.hasClass('disabled')){
            return;
        }

        Ajax.custom({
            url: config.ordercheck,
            data: {
                id: id
            }
        }, function(response) {
            if (response.code != 0) {
                return;
            }
            that.addClass('disabled').text('已验收');

        }, function(xhr, textStatus){
            Tools.showToast('接口异常 ' + textStatus);
        });
    });

});
