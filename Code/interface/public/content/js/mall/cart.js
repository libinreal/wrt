define(function(require) {
    var $ = require('jquery'),
        config = require('./config'),
        Ajax = require('../base/ajax'),
        Tools = require('../base/tools'),
        Storage = require('../base/storage');

    require('../base/common');

    var account,
        constractSn = Tools.getQueryValue('constractSn'),
        contractNo, //合同编号
        isSending = false, //是否正在同步购物车的标志
        copyNum = {}; //备份购物车商品数量，以便还原

    //回填用户相关数据
    config.afterCheckAccount = function(account1){
        account = account1;
    }

    //选择订单所属编号
    $('#zj-pact').on('click', 'li', function() {
        $("#zj-pact li").removeClass('checkbg');
        $(this).addClass('checkbg');
        contractNo = $(this).attr('data-code');
    });
    //取消关闭订单编号
    $(".modal-close").click(function() {
        $(".order_project").hide();
        $("#panelBg").hide();
    });
    //点击确定赋值后关闭订单编号
    $("#confirm_pro").click(function() {
        $(".order_project").hide();
        $("#panelBg").hide();
    });
    //文本框值改变时重新计算总价
    $('#zj-cart').on('change', '.numbers', function() {
        var that = $(this),
            cur = $(this).parents('.shopping-item'),
            v = $(this).val(),
            id = cur.attr('data-id');

        if (!v || v == '0') {
            v = 1;
            $(this).val(1);
        }
        syncCart(id, v, function(response) {
            if (response.code != 0) {
                log(copyNum)
                that.val(copyNum[id]);
            }
            calculate();
        });
    });
    //点击+按钮增加数量
    $('#zj-cart').on('click', '.add', function() {
        var cur = $(this).parents('.shopping-item');
        var pre = $(this).prev();
        var numed = pre.val();
        numed = parseInt(numed) + 1;

        syncCart(cur.attr('data-id'), numed, function(response) {
            if (response.code != 0) {
                return;
            }
            pre.val(numed);
        })
    });
    //点击-按钮减少数量
    $('#zj-cart').on('click', '.sub', function() {
        var cur = $(this).parents('.shopping-item');
        var next = $(this).next();
        var nextNum = 0;
        if (parseInt(next.val()) <= 1) {
            return;
        }
        nextNum = parseInt(next.val()) - 1;

        syncCart(cur.attr('data-id'), nextNum, function(response) {
            if (response.code != 0) {
                return;
            }
            next.val(nextNum);
        });
    });
    //删除按钮
    $('#zj-cart').on('click', '.delOrNot', function() {
        var cur = $(this).parents('.shopping-item');

        Tools.showConfirm('确认删除该商品吗？', function() {
            syncCart(cur.attr('data-id'), 0, function() {
                cur.remove();
                if ($('#zj-cart').children().length == 0) {
                    $('.nodata').show();
                    $('.settlement').hide();
                }
            });
        });

    });

    //改变合同
    $('#zj-contracts').change(function() {
        contractNo = $(this).val();
    });

    //初始化合同
    config.getContracts('', '', function(response) {
        if (!response.body || response.body.length == 0) {
            $('#zj-pact').after(config.nodata);
            return;
        }

        $('#zj-contracts').val(constractSn);
        contractNo = constractSn;

    });

    //获取购物车数据
    Ajax.custom({
        url: config.goodsCartList
    }, function(response) {
        if (response.code != 0) {
            return;
        }
        if (response.body.length == 0) {
            $('.nodata').show();
            return;
        }
        var data = response.body || [];

        //初始化备份数据
        for (var d in data) {
            copyNum[data[d].goodsId] = data[d].nums;
        }

        var result = template.render('zj-cart-tmpl', {
            'list': data
        });
        $('#zj-cart').html(result);
        $('.settlement').show();
        $('.add-other').show();
        calculate();
    }, function() {
        $('.add-other').show();
    });

    //提交购物车
    //1判断用户权限，2选择合同编号，3提交购物车
    $('.settlement button').click(function(e) {
        e.preventDefault();

        if (account.customLevel < config.customLevel.ERPORDER) {
            Tools.showAlert({
                message: '您不是交易用户没有下单权限！',
                okText: '返回商城',
                yesCallback: function() {
                    location.href = 'index.html';
                }
            });
            return;
        }

        contractNo = $('input[name="contractSn"]').val();

        if (!contractNo) {
            Tools.showAlert('请选择一个合同');
            return;
        }

        // config.getContracts('', '', function(response) {
        //     if (!response.body || response.body.length == 0) {
        //         $('#zj-pact').after(config.nodata);
        //         return;
        //     }
        //     var result = template.render('zj-pact-tmpl', {
        //         'list': response.body || []
        //     });
        //     $('#zj-pact').html(result);
        // });
        // $('#modal-contracts').show();
        location.href = 'address.html?contractNo=' + contractNo;
    });

    //选择合同编号后
    $('#confirm-pro').click(function() {
        if (!contractNo) {
            Tools.showToast('请选择一个合同');
            return;
        }

        // Ajax.submit({
        //     url: config.goodsCartList
        // },function(response){});

        location.href = 'address.html?contractNo=' + contractNo;
    });

    //同步购物车
    function syncCart(id, num, callback) {
        if (isSending) {
            return;
        }

        isSending = true;
        Ajax.custom({
            url: config.updateCart,
            data: {
                id: id,
                number: num
            },
            type: 'POST'
        }, function(response) {
            isSending = false;
            if (typeof callback == 'function') {
                callback(response);
            }
            if (response.code != 0) {
                Tools.showAlert(response.message);
                return;
            }

            copyNum[id] = num; //更新备份数据
            calculate();
        }, function() {
            isSending = false;
        })
    }

    //定义计算总价方法
    function calculate() {
        var totalPrice = 0;
        $(".numbers").each(function() {
            totalPrice += parseInt($(this).val()) * parseFloat($(this).parent().next().find("span").html());
            $(".totalprice").html(totalPrice.toFixed(2));
        });
    }

});
