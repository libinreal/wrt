define(function(require) {
    var $ = require('jquery'),
        config = require('./config'),
        Ajax = require('../base/ajax'),
        Tools = require('../base/tools');

    require('../base/common');

    // 模板帮助方法，确认订单状态
    template.helper('$getStatus', function(content) {
        if(!content)
            return '--';
        return config.ORDERSTATUS[content] || '--';
    });
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
    // 模板帮助方法，确认订单的状态
    template.helper('$checkStatus', function(cur, content) {
        if(!content)
            return '';
        var k = parseInt(content);
        return cur <= k ? 'active' : '';
    });

    var orderSn = Tools.getQueryValue('orderSn'),
        goodsId = Tools.getQueryValue('goodsId');

    //获取订单批次
    Ajax.detail({
        url: config.getbatchs,
        data: {
            orderSn: orderSn,
            goodsId: goodsId,
            type: 2
        }
    }, function(response) {
        if (response.code != 0) {
            return;
        }
    });

    //催办订单
    $('#btn-cuiban').click(function(e){
    	e.preventDefault();

    	$(this).addClass('disabled');
    });

    //确认验收
    $('#zj-detail').on('click', '.btn-yanshou', function(e) {
        e.preventDefault();

        var that = $(this);
        Ajax.custom({
            url: config.ordercheck,
            data: {
                id: that.attr('data-id')
            }
        }, function(response) {
            if (response.code != 0) {
                Tools.showToast(response.message || '接口错误');
                return;
            }

            // that.addClass('gray-btn');
            that.parent().hide();
            that.parent().next().show();

        }, function(xhr, textStatus){
            Tools.showToast('接口异常 ' + textStatus);
        });
    });

});
