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
        return config.KIDORDERSTATUS[content] || '--';
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
        url: config.getchildreninfo,
        data: {
            order_id: id
        }
    }, function(response) {
        if (response.code != 0) {
            return;
        }
    });

    //更改状态
    $('#zj-detail').on('click', '#handle-button .change-status', function(e) {
        Ajax.custom({
            url: config.uchildstatus,
            data: {
                oid: id
            },
            type: 'GET'
        }, function(response) {
            if (response.code != 0) {
                Tools.showToast(response.message || '操作失败');
                return;
            }
            Ajax.detail({
                url: config.getchildreninfo,
                data: {
                    order_id: id
                }
            }, function(response) {
                if (response.code != 0) {
                    return;
                }
            });
        });
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
                id: id
            },
            type: 'POST'
        }, function(response) {
            if (response.code != 0) {
                Tools.showToast(response.message || '操作失败');
                return;
            }
            that.addClass('disabled').text('已催办');
        });
    });

    //取消订单(取消)
    $('#zj-detail').on('click', '.btn-quxiao__', function(e) {
        e.preventDefault();

        var that = $(this);
        Tools.showConfirm('确认取消订单吗？', function() {
            Ajax.custom({
                url: config.orderustatus,
                data: {
                    id: id,
                    status: config.ORDERENUM.YQX
                }
            }, function(response) {
                if (response.code != 0) {
                    Tools.showToast(response.message || '操作失败');
                    return;
                }
                location.href = 'my-order.html';

            });
        });
    });

    //取消订单
    $('#zj-detail').on('click', '.btn-quxiao', function(e) {
        e.preventDefault();
        
        var that = $(this);

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
                id: id,
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
                url: config.server + '/bank/submitContract',
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

    //订单确认完成
    $('#zj-detail').on('click', '.btn-queren', function(e) {
        e.preventDefault();

        var that = $(this);
        if(that.hasClass('disabled')){
            return;
        }

        Ajax.custom({
            url: config.orderustatus,
            data: {
                id: id,
                status: config.ORDERENUM.YSZ
            }
        }, function(response) {
            if (response.code != 0) {
                Tools.showToast(response.message || '操作失败');
                return;
            }
            location.href = location.href;

        });
    });

    //订单完成验收
    $('#zj-detail').on('click', '.btn-yanshou', function(e) {
        e.preventDefault();

        var that = $(this);
        if(that.hasClass('disabled')){
            return;
        }

        Ajax.custom({
            url: config.orderustatus,
            data: {
                id: id,
                status: config.ORDERENUM.DZZ
            }
        }, function(response) {
            if (response.code != 0) {
                Tools.showToast(response.message || '操作失败');
                return;
            }
            location.href = location.href;

        });
    });

    //订单完成对账（取消）
    $('#zj-detail').on('click', '.btn-duizhang__', function(e) {
        e.preventDefault();

        var that = $(this);
        if(that.hasClass('disabled')){
            return;
        }
        
        Ajax.custom({
            url: config.orderustatus,
            data: {
                id: id,
                status: config.ORDERENUM.WC
            }
        }, function(response) {
            if (response.code != 0) {
                Tools.showToast(response.message || '操作失败');
                return;
            }
            location.href = location.href;

        });
    });

    //订单验签
    $('#handle-button').on('click', '.button-receive-check', function(e) {
        e.preventDefault();
        var oid = $(this).attr("data-id");
        var data = {"oid":oid};
        $.get("order/uchildstatus", data, function(object){
            if(object.error == -1){
                console.log('更新失败');
                return false;
            }else{
                location.reload();
                return false;
            }
        });  
    });
    
    $('#handle-button').on('click', '.button-send-check', function(e) {
        e.preventDefault();
        var oid = $(this).attr("data-id");
        var data = {"oid":oid};
        $.get("order/uchildstatus", data, function(object){
            if(object.error == -1){
                console.log('更新失败');
                return false;
            }else{
                location.reload();
                return false;
            }
        });  
    });
});