define(function(require) {
    var $ = require('jquery'),
        config = require('./config'),
        Ajax = require('../base/ajax'),
        Tools = require('../base/tools');

    require('../base/common');
    require('../module/validator/local/zh_CN');

    var addressId = Tools.getQueryValue('adrId'),
        contractNo = Tools.getQueryValue('contractNo'), //合同编号
        hasGodds = false;//购物车是否有商品

    $('input[name="addressId"]').val(addressId);

    //获取购物车数据
    Ajax.custom({
        url: config.goodsCartList
    }, function(response) {
        if (response.code != 0) {
            return;
        }
        var result = template.render('zj-cart-tmpl', {
            'list': response.body || []
        });
        $('#zj-cart').html(result);
        // contractNo = response.body.length; //回写合同编号
        $('input[name="contractSn"]').val(contractNo).trigger('change');

        calculate(response.body);
    });

    //日期选择器
    $('.date').click(function() {
        var that = $(this),
            options = {
                el: that[0],
                dateFmt: 'yyyy-MM-dd',
                readOnly: true
            };

        if(that.hasClass('time')){
            options.dateFmt = 'yyyy-MM-dd HH:mm:ss';
        }

        //#F{$dp.$D(\'d4312\')
        //#F{$dp.$D(\'d4312\')||\'2020-10-01\'}
        //#F{$dp.$D(\'d4322\',{d:-3});}
        if (that.hasClass('start')) {
            var other = that.parent().find('.end');
            if (other) {
                options.maxDate = getDateStr(other);
            } else {
                options.maxDate = '%y-%M-%d';
            }
        } else if (that.hasClass('end')) {
            var other = that.parent().find('.start');
            options.minDate = getDateStr(other);
            //options.maxDate = '%y-%M-%d';
        }
        log(options);

        WdatePicker(options);
    });

    function getDateStr(dom) {
        return dom.val();
    }


    //获取默认发票信息
    Ajax.custom({
        url: config.defInvoice
    }, function(response) {
        if (response.code != 0) {
            return;
        }
        var data = response.body || {};
        if(data.invType == '0'){
            $('#invtype1').addClass('active');
            $('#invtype2').removeClass('active');
            $('#invtype1 input').attr('checked',true);
            $('#invtype2 input').removeAttr('checked');
        }else{
            $('#invtype2').addClass('active');
            $('#invtype1').removeClass('active');
            //这里的顺序需要先添加attr在移除，否则firefox中不能设置上checked属性
            $('#invtype2 input').attr('checked',true);
            $('#invtype1 input').removeAttr('checked');
        }
        $('input[name="invPayee"]').val(data.invPayee);
        $('input[name="invAddress"]').val(data.invAddress);
    });

    //提交订单
    $('#order-form').validator({
        focusCleanup: true,
        msgWrapper: 'div',
        msgMaker: function(opt) {
            return '<span class="' + opt.type + '">' + opt.msg + '</span>';
        },
        fields: {
            "invPayee": {
                rule: "发票抬头: required;"
            },
            "invAddress": {
                rule: "收票地址: required;"
            }
        },
        //验证成功
        valid: function(form) {
            contractNo = $('input[name="contractSn"]').val();
            if (!contractNo) {
                Tools.showAlert('请选择一个合同');
                return;
            }

            if(!hasGodds){
                Tools.showAlert('您的购物车没有数据，请重新添加');
                return;
            }

            Ajax.submit({
                url: config.addorder,
                data: $('#order-form'),
            }, function(response) {
                if (!response || response.code != 0) {
                    if(response.code == -4){
                        //库存不足
                        showStock(response.body);
                        Tools.showAlert(response.message || '库存不足');
                        return;
                    }
                	if(response.code == -7){
                        Tools.showConfirm({
                            message: '你的采购额度不足，可取消下单或追加申请额度后下单',
                            okText: '返回商城',
                            cancelText: '取消下单',
                            yesCallback: function() {
                                location.href = 'index.html';
                            },
                            noCallback: function(){
                                location.href = 'cart.html';
                            }
                        });
                		return;
                	}
                    Tools.showAlert(response.message);
                    return;
                }

                Tools.showAlert({
                    message: '感谢您的支持，您的订单已提交成功，<br/>请记住您的订单号: <span class="c-green">' + response.body.orderNo + '</span>',
                    okText: '返回商城',
                    tick: 5000,
                    showTips: true,
                    yesCallback: function() {
                        location.href = 'index.html';
                    },
                    tipsCallback: function(){
                        location.href = '../personal/my-order.html';
                    }
                });
            });
        }
    });

    //获取合同编号
    config.getContracts('', '', function() {
        $('input[name="contractSn"]').val(contractNo).trigger('change');
    });

    //改变合同
    $('#zj-contracts').change(function() {
        contractNo = $(this).val();
    });

    //定义计算总价方法
    function calculate(data) {
        var totalPrice = 0;
        for (var i = 0; i < data.length; i++) {
            totalPrice += (parseInt(data[i].nums) * parseInt(data[i].price));
        }
        hasGodds = totalPrice != 0;
        $('#total-price').text(totalPrice.toFixed(2));
    }

    //显示库存不足提示
    function showStock(data){
        for(var i in data){
            $('#goods-' + data[i].id).find('.stock').show();
        }
    }

});
