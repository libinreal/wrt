define(function(require) {
    var $ = require('jquery'),
        config = require('./config'),
        Ajax = require('../base/ajax'),
        Tools = require('../base/tools');

    require('../base/common');

    var id = Tools.getQueryValue('id'),
        addressId = Tools.getQueryValue('adrId');

    //获取订单
    Ajax.detail({
        url: config.getDetail,
        data: {
            id: id
        }
    });

    //提交订单
    $('#zj-detail').on('click', 'input[type="submit"]', function(e) {
        e.preventDefault();

        Ajax.submit({
            url: config.exchange,
            data: {
                id: id,
                addressId: addressId
            }
        }, function(response) {
            if (response.code != 0) {
                Tools.showAlert(response.message || '接口错误');
                return;
            }

            Tools.showAlert({
                message: '感谢您的支持，您的订单已提交成功，<br/>请记住您的订单号: <span class="c-green">' + response.body.orderCode + '</span>',
                okText: '返回商城',
                tick: 5000,
                showTips: true,
                yesCallback: function() {
                    location.href = 'index.html';
                },
                tipsCallback: function(){
                    location.href = '../personal/my-jifen.html';
                }
            });
        });
    });

});