define(function(require) {
    var $ = require('jquery'),
        config = require('./config'),
        Ajax = require('../base/ajax'),
        Tools = require('../base/tools');

    require('../base/common');

    // 模板帮助方法，确认订单状态
    template.helper('$getStatus', function(content) {
        if (!content)
            return '--';
        return config.ORDERSTATUS[content] || '--';
    });
    // 模板帮助方法，确认订单的发票类型
    template.helper('$getInvType', function(content) {
        if (!content)
            return '--';
        return config.INVTYPE[content] || '--';
    });
    // 模板帮助方法，确认订单的支付机构
    template.helper('$getPayOrg', function(content) {
        if (!content)
            return '--';
        return config.PAYENUM[content] || '--';
    });
    // 模板帮助方法，确认订单的状态
    template.helper('$checkStatus', function(cur, content) {
        if (!content)
            return '';
        var k = parseInt(content);
        return cur <= k ? 'active' : '';
    });

    // 模板帮助方法，获取当前商品
    template.helper('$getCurGoodsId', function() {
        return goodsId;
    });

    var orderSn = Tools.getQueryValue('orderSn'),
        goodsId = Tools.getQueryValue('goodsId');

    //获取订单批次
    Ajax.detail({
        url: config.getbatchs,
        data: {
            orderSn: orderSn,
            goodsId: goodsId,
            type: 1
        }
    }, function(response) {
        if (response.code != 0) {
            return;
        }
    });

    //立即支付(取消)
    $('#zj-detail').on('click', '.btn-zhifu__', function(e) {
        e.preventDefault();

        var that = $(this);
        Ajax.custom({
            url: config.orderpay,
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
