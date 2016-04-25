define(function(require) {
    var $ = require('jquery'),
        config = require('./config'),
        Ajax = require('../base/ajax'),
        Tools = require('../base/tools');

    require('../base/common');
    require('../module/validator/local/zh_CN');

    var id = Tools.getQueryValue('id'),
        contractNo = Tools.getQueryValue('contractNo'),
        addressId,
        tempAddress; //临时地址数据

    //新增按钮
    $('#add-address-btn').click(function(e) {
        e.preventDefault();
        $('.address-list').hide();
        $('.address-add').show();
    });

    //选中按钮
    $('.address-list').on('click', '.address-operate', function(e) {
        e.preventDefault();
        $('.address-operate').removeClass('active');
        $(this).addClass('active');
        addressId = $(this).parent().attr('data-id');
    });

    //下一步
    $('#next-btn').click(function(e) {
        e.preventDefault();
        if (!addressId) {
            Tools.showToast('请选择一个地址');
            return;
        }
        location.href = 'order.html?id=' + id + '&adrId=' + addressId + '&contractNo=' + contractNo;
    });

    //地址标签切换
    $('input[name="tag-type"]').click(function() {
        var v = $(this).val();
        $('input[name="tag-type"]').removeClass('active');
        $('input[name="tag"]').val(v);
        $('.other-input').attr('readonly', true);
        if (v == '') {
            $('.other-input').attr('readonly', false);
            $('input[name="tag"]').val('');
        } else {
            $('.other-input').val('');
        }
    });
    $('.other-input').change(function() {
        $('input[name="tag"]').val($(this).val());
    });

    //编辑地址
    $('.address-list').on('click', '.address-name a', function(e) {
        e.preventDefault();
        addressId = $(this).parents('.address-item').attr('data-id');
        setData();

        $('.address-list').hide();
        $('.address-add').show();
    });

    //获取收获地址
    Ajax.paging({
        url: config.iMyAddress
    }, function(result) {
        if (!result.body || !result.body.length) {
            $('.address-add').show();
        }else{
            tempAddress = result.body;
            $('.address-list').show();
//            setAddressId();
        }
    });

    //保存收获地址
    $('#address-form').validator({
        focusCleanup: true,
        msgWrapper: 'div',
        msgMaker: function(opt) {
            return '<span class="' + opt.type + '">' + opt.msg + '</span>';
        },
        fields: {
            "name": {
                rule: "收货人姓名: required;length[1~30]"
            },
            "address": {
                rule: "收货地址: required;length[1~80]"
            },
            "phone": {
                rule: "联系方式: required;mobile"
            }
        },
        //验证成功
        valid: function(form) {
            Ajax.submit({
                url: config.iSaveAddress,
                data: $('#address-form')
            }, function(data) {
                if (!data || data.code != 0) {
                    Tools.showAlert(data.message);
                    return;
                }
                $('#address-form')[0].reset();
                location.href = 'order.html?id=' + id + '&adrId=' + data.body.addressId + '&contractNo=' + contractNo;
            });

        }
    });

    //设置编辑数据
    function setData() {
        if (!tempAddress || tempAddress.length == 0) {
            return;
        }

        var d, flag = false;
        for (var i = 0; i < tempAddress.length; i++) {
            if (addressId == tempAddress[i].id) {
                d = tempAddress[i];
            }
        }
        $('input[name="id"]').val(d.id);
        $('input[name="name"]').val(d.name);
        $('input[name="address"]').val(d.address);
        $('input[name="phone"]').val(d.phone);
        $('input[name="tag"]').val(d.tag);
        $('input[name="tag-type"]').attr('checked', false).removeClass('active');
        $('input[name="tag-type"]').each(function() {
            if ($(this).val() == d.tag) {
                flag = true;
                $(this).attr('checked', true).parent().addClass('active');
            }
        });
        if (!flag) {
            $('input[name="tag-type"]:eq(3)').attr('checked', true).parent().addClass('active');
            $('.other-input').val(d.tag).attr('readonly', false);
        }
    }

    function setAddressId(){
        for (var i = 0; i < tempAddress.length; i++) {
            if (tempAddress[i].default) {
                addressId = tempAddress[i].id;
            }
        }
    }
});
