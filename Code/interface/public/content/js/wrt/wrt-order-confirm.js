define(function(require) {
    var $ = require('jquery'),
        config = require('../personal/config'),
        Ajax = require('../base/ajax'),
        Tools = require('../base/tools'),
        sign = require('../personal/sign');

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

    var id = Tools.getQueryValue('id');

    //获取订单详情
    Ajax.detail({
        url: config.getdetail,
        data: {
            id: id
        }
    }, function(response) {
        if (response.code != 0) {
            return;
        }
    });

    //合同验签
    $('#zj-detail').on('click', '.btn-primary', function(e) {
        e.preventDefault();

        var that = $(this);
        if(that.hasClass('disabled')){
            return;
        }

        //检测浏览器，当前只在ie可用
        if(!config.isMsie()){
        	Tools.showAlert('验签只能在IE中使用');
        	return;
        }

        //获取订单签名数据
        Ajax.custom({
            url: config.server + '/bank/getSubmitData',
            data: {
                orderId: id//当前订单id
            },
            type: 'POST'
        },function(response){
            tempResponse = response;
            if(tempResponse.code != 0 || !tempResponse.body){
                Tools.showAlert('获取订单签名数据失败');
                return;
            }

            //生成签名数据
            // var signData = config.signPkcs7(tempResponse.body.signRawData);
            var signData = sign.koalSign4submitContract(0, tempResponse.body.signRawData);
            if(!signData.success){
                Tools.showAlert('生成签名数据失败！' + signData.errorInfo);
                return;
            }

            Ajax.custom({
                url: config.server + '/bank/submitContract',
                data: {
                    'orderSn': tempResponse.body.orderSn,
                    'signId': tempResponse.body.signId,
                    'buyerSign': signData.data
                },
                type: 'POST'
            },function(response){
                if (response.code != 0) {
                    Tools.showAlert('签名失败！');
                    return;
                }
                Tools.showAlert('签名成功！');
                that.addClass('disabled').text('订单已确认');
            },function(jqXHR, textStatus, errorThrown){
                Tools.showAlert('签名失败！');
            });
        });
    });

});