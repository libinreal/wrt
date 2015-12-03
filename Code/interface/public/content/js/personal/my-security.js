define(function(require) {
    var $ = require('jquery'),
        config = require('./config'),
        Ajax = require('../base/ajax'),
        Storage = require('../base/storage'),
        Tools = require('../base/tools');

    require('../base/common');
    require('../module/validator/local/zh_CN');

    //validator config
    $.validator.config({
        focusCleanup: true,
        msgWrapper: 'div',
        msgMaker: function(opt) {
            return '<span class="' + opt.type + '">' + opt.msg + '</span>';
        }
    });

    //修改密码
    $('#modifyPwdForm').validator({
        rules: {
        },
        fields: {
            "oldpwd": {
                rule: "旧密码: required;password;"
            },
            "newpwd_one": {
                rule: "新密码: required;password;"
            },
            "newpwd": {
                rule: "确认新密码: required;match(newpwd_one);"
            }
        },
        //验证成功
        valid: function(form) {
            Ajax.submit({
                url: config.iModifyPwd,
                data: $(form),
            }, function(data) {
                if (!data || data.code != 0) {
                    Tools.showAlert(data.message);
                    return;
                }
                Tools.showAlert('密码修改成功！');
                $(form)[0].reset();
            });
        }
    });

    //短信发送
    var phone = $('#modifyPhoneForm input[name="mobile"]');

    $('#sendMsg1').click(function(e) {
        e.preventDefault();
        var _this = $(this);

        phone.isValid(function(v) {
            if (!v) {
                log(v);
                return;
            }
            _this.addClass('disabled');
            $('input[name="step"]').val(1);
            Ajax.submit({
                url: config.iModifyPhone,
                data: $('#modifyPhoneForm'),
            }, function(data) {
                if (!data || data.code != 0) {
                    Tools.showAlert(data.message);
                    return;
                }
                changeBtnState(_this);
            });
        });

    });

    //短信计时
    function changeBtnState(obj) {
        var second = 60;
        var text = obj.text();
        obj.prop('disabled', true);
        var timer = setInterval(function() {
            obj.text(text + '(' + (second--) + ')');
            if (second < 0) {
                obj.prop('disabled', false);
                obj.text(text);
                obj.removeClass('disabled');
                clearInterval(timer);
            }
        }, 1000);
    }

    //修改手机号
    $('#modifyPhoneForm').validator({
        rules: {
            phoneValid: function(element) {
                return $.ajax({
                    url: config.iVerifyMobile,
                    data: 'mobile=' + element.value,
                    type: 'GET'
                });
            }
        },
        fields: {
            "mobile": {
                rule: "新手机号码: required;mobile;"
            },
            "vcode": {
                rule: "验证码: required;"
            }
        },
        //验证成功
        valid: function(form) {
            $('input[name="step"]').val(2);
            Ajax.submit({
                url: config.iModifyPhone,
                data: $(form),
            }, function(data) {
                if (!data || data.code != 0) {
                    Tools.showAlert(data.message);
                    return;
                }
                Tools.showAlert('手机号变更成功！');
                $(form)[0].reset();
            });
        }
    });
});
