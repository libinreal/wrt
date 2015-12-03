define(function(require) {
    var $ = require('jquery'),
        config = require('./config'),
        Ajax = require('../base/ajax'),
        Tools = require('../base/tools');

    require('../base/common');
    var constractSn = Tools.getQueryValue('constractSn');

    //获取订单详情
    Ajax.detail({
        url: config.projectDetail,
        data: {
            constractSn: constractSn
        },
        showLoading: true
    }, function(response) {
        if (response.code != 0) {
            return;
        }
    });

    //立即下单
    $('#zj-detail').on('click', '.btn-addcart', function(e) {
        e.preventDefault();

        var that = $(this),
            goods = $(this).parent().next().children(),
            ret = [];

        goods.each(function() {
            ret.push($(this).attr('data-id'));
        });

        Ajax.custom({
            url: config.projectCart,
            data: {
                ids: ret.join(',')
            },
            type: 'POST'
        }, function(response) {
            if (response.code != 0) {
                Tools.showToast(response.message || '下单失败');
                return;
            }

            location.href = '../mall/cart.html?constractSn=' + that.attr('data-id');
        })

    });

});
