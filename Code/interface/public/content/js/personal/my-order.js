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

    //取消订单
    $('#zj-list').on('click', 'button', function(e) {
    	e.preventDefault();
    	
        var that = $(this);

        if(that.hasClass('disabled')){
            return;
        }

        if(that.attr('data-status') == '1' && !config.isMsie()){
            Tools.showAlert('当前订单需验签再取消，请在IE中使用');
            return;
        }

        Tools.showConfirm('确认取消订单吗？', function() {
            if(that.attr('data-status') == '1'){
                deleteOrderAfterSign(that);
            }else{
                deleteOrderBeforeSign(that);
            }
        });
    });

    //验签成功前删除，仅删除订单
    function deleteOrderBeforeSign(that){
        Ajax.custom({
            url: config.orderustatus,
            data: {
                id: that.attr('data-id'),
                status: config.ORDERENUM.YQX
            }
        }, function(response) {
            if (response.code != 0) {
                Tools.showToast(response.message || '取消订单失败');
                return;
            }
            
            that.parents('a').fadeOut('normal', function() {
                $(this).remove();
            });

        });
    }

    //合同验签成功后删除，需发起验签再取消
    function deleteOrderAfterSign(that) {
        if(that.hasClass('disabled')){
            return;
        }

        //获取订单签名数据
        Ajax.custom({
            url: config.server + '/bank/getCancelData',
            data: {
                orderId: that.attr('data-id')//当前订单id
            },
            type: 'POST'
        },function(response){
            tempResponse = response;

            if(tempResponse.code != 0 || !tempResponse.body){
                Tools.showAlert('获取订单签名数据失败');
                return;
            }

            //生成签名数据
            var signData = config.signPkcs7(tempResponse.body.signRawData);
            if(!signData.success){
                Tools.showAlert('生成签名数据失败！' + signData.errorInfo);
                return;
            }

            Ajax.custom({
                url: config.server + '/bank/cancelContract',
                data: {
                    'orderSn': tempResponse.body.orderSn,
                    'signId': tempResponse.body.signId,
                    'buyerSign': signData.data
                },
                type: 'POST'
            },function(response){
                if (response.code != 0) {
                    Tools.showAlert('取消失败！');
                    return;
                }

                Tools.showAlert('取消成功！');
                that.addClass('disabled').text('取消中');
            },function(jqXHR, textStatus, errorThrown){
                Tools.showAlert('取消失败！');
            });
        });
    }
});
