define(function(require) {
    var $ = require('jquery'),
        config = require('./config'),
        Ajax = require('../base/ajax'),
        Storage = require('../base/storage'),
        Tools = require('../base/tools'),
        msg = require('../base/messages');

    require('../module/validator/local/zh_CN');
    require('../base/common');
    var isEdit = true;

    // address list
    config.getAddressList = function() {
        Ajax.paging({
            url: config.iMyAddress,
            renderFor: 'myAddressTmpl',
            renderEle: '#myAddressData'
        }, function(result) {
            if (!result.body || !result.body.length) return;
            $('#deleteAddress').show();
            setDefaultAddress($('.default'));
            updateAddress($('.editAddress'));
            deleteAddress($('.delete'));
        });
    }

    if ($('#myAddressTmpl').length) {
        config.getAddressList();
    }

    //set default address
    function setDefaultAddress(dom) {
        dom.on('click', function() {
            var _this = $(this);
            if (_this.hasClass('active')) {
                return;
            }

            dom.removeClass('active');
            Ajax.custom({
                url: config.iSetDefaultAddress,
                data: {
                    id: _this.attr('data-id')
                },
                type: 'POST'
            }, function(result) {
                if (!result || result.code != 0) {
                    return;
                }
                _this.addClass('active');
            });

        });
    }

    //delete address
    $('#deleteAddress').click(function() {

        if (isEdit) {
            $(this).text('返回');
            $('.default').hide();
            $('.delete').css('display', 'inline-block');
            isEdit = false;
        } else {
            $(this).text('编辑');
            $('.default').show();
            $('.delete').hide();
            isEdit = true;
        }
    });

    function deleteAddress(dom) {

            dom.on('click', function() {
                var _this = $(this);
                Tools.showConfirm({
                    message: '确认删除吗？',
                    yesCallback: function() {
                        Ajax.custom({
                            url: config.iDeleteAddress,
                            data: {
                                id: _this.attr('data-id')
                            },
                            type: 'POST'
                        }, function(result) {
                            if (!result || result.code != 0) {
                                Tools.showAlert(msg.server);
                                return;
                            }
                            _this.parents('li').fadeOut('normal', function() {
                                $(this).remove();
                                if ($('#myAddressData').children().length == 0) {
                                    $('#myAddressData').html(config.nodata);
                                    $('#deleteAddress').hide();
                                }
                            });
                        });
                    }
                });
            });
        }
        //update address
    function updateAddress(dom) {
        dom.on('click', function() {
            location.href = 'add-address.html?id=' + $(this).attr('data-id');
        });
    }
    var id = Tools.getQueryValue('id') || '';
    var flag = false;
    if (id) {
        $('#type-name').text('编辑收货地址');
        Ajax.custom({
            url: config.iFindAddressById,
            data: {
                id: id
            }
        }, function(response) {
            var result = response.body;
            $('input[name="id"]').val(result.id || '');
            $('input[name="name"]').val(result.name || '');
            $('input[name="address"]').val(result.address || '');
            $('input[name="phone"]').val(result.phone || '');
            $('input[name="tag"]').val(result.tag || '');
            $('input[name="tag-type"]').prop('checked', false).removeClass('active');
            $('input[name="tag-type"]').each(function() {
                if ($(this).val() == result.tag) {
                    flag = true;
                    $(this).prop('checked', true).parent().addClass('active');
                }
            });
            if (!flag) {
                $('input[name="tag-type"]:eq(3)').prop('checked', true).parent().addClass('active');
                $('.other-input').val(result.tag).prop('readonly', false);
            }
        });
    }

    //save address
    $('input[name="tag-type"]').on('click', function(e) {
        var v = $(this).val();
        $('.other-input').val('');
        $('input[name="tag"]').val($(this).val());
        $('.other-input').prop('readonly', true);
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

    $('#saveAddressForm').validator({
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
                rule: "联系方式: required;[mobile|tel];"
            }
        },
        //验证成功
        valid: function(form) {
            Ajax.submit({
                url: config.iSaveAddress,
                data: $('#saveAddressForm')
            }, function(data) {
                if (!data || data.code != 0) {
                    return;
                }
                //Tools.showAlert('地址保存成功！');
                //$('#saveAddressForm')[0].reset();
                location.href = 'my-address.html'
            });
        }
    });
});
