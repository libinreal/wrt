define(function(require) {
    var $ = require('jquery'),
        config = require('../personal/config'),
        Ajax = require('../base/ajax'),
        Tools = require('../base/tools');

    require('../base/common');

    var id = Tools.getQueryValue('id'),
		orderSn = Tools.getQueryValue('orderSn'),
		goodsId = Tools.getQueryValue('goodsId'),
		price = Tools.getQueryValue('price');

	$('#orderSn').html(orderSn);
	$('#orderPrice').html(price);
	// $('#dirOrder').attr('href','../personal/my-order-sub.html?orderSn='+orderSn +'&goodsId='+goodsId);

    //立即支付
    $('#zj-detail').on('click', '.btn-zhifu', function(e) {
        e.preventDefault();

        var that = $(this);
        if(that.hasClass('disabled')){
        	return;
        }

        Ajax.custom({
            url: config.orderpay,
            data: {
                id: id
            }
        }, function(response) {
            if (response.code != 0) {
                Tools.showToast(response.message || '接口错误');
                return;
            }

            Tools.showToast('支付成功');
            that.addClass('disabled').text('已支付');
        }, function(xhr, textStatus){
            Tools.showToast('接口异常 ' + textStatus);
        });
    });
});