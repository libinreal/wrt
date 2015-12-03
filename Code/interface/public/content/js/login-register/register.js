define(function(require) {
   var  $ = require('jquery'),
	    msg = require('./messages'),
	    rules = require('./rules'),
	    Tools = require('../base/tools'),
	    Storage = require('../base/storage'),
	    config = require('./config'),
	    Ajax = require('../base/ajax'),
	    Cookie = require('../base/cookie');

    require('../base/common');
    require('../module/validator/local/zh_CN');

    var phone = $('#mobile'),
        registerForm = $('#register-form').validator().data('validator');

    //提交注册表单
    $('#register-form').validator({
    	focusCleanup: true,
    	msgWrapper: 'div',
    	msgMaker: function(opt) {
    	    return '<span class="' + opt.type + '">' + opt.msg + '</span>';
    	},
        rules: {
            unValid: function(element) {
                return $.ajax({
                    url: config.iVerifyUname,
                    data: 'name=' + element.value,
                    type: 'GET'
                });
            },
            phoneValid: function(element) {
                return $.ajax({
                    url: config.iVerifyMobile,
                    data: 'mobile=' + element.value,
                    type: 'GET',
                    success: function(data) {
                        // console.log(data);
                    }
                });
            }
        },
        fields: {
            "account": {
                rule: "用户名: required;username;unValid;"
            },
            "pwd_one": {
                rule: "密码: required;password;"
            },
            "password": {
                rule: "确认密码: required;match(pwd_one);"
            },
            "email": {
                rule: "email;"
            },
            "contacts": {
                rule: "联系人: required;length[1~10]"
            },
            "telephone": {
                rule: "联系人手机号码: required;mobile;phoneValid;"
            },
            "vcode": {
                rule: "验证码: required;"
            },
            "secondContacts": {
                rule: "length[1~10]"
            },
            "secondPhone": {
                rule: "mobile"
            },
            "qq": {
                rule: "QQ: qq"
            },
            "weixin": {
                rule: "微信: length[1~20];"
            },
            "companyName": {
                rule: "单位名称: required;length[1~40]"
            },
            "companyAddress": {
                rule: "单位地址: required;length[1~150]"
            },
            "officePhone": {
                rule: "办公电话: required;"
            },
            "fax": {
                rule: "办公传真:  required;"
            },
            "position": {
                rule: "职位: required;length[1~20]"
            },
            "department": {
                rule: "所在部门: required;length[1~20]"
            }
        },
        //验证成功
        valid: function(form) {
            Ajax.submit({
                url: config.iRegister,
                data: $('#register-form'),
            }, function(data) {
                if (!data || data.code != 0) {
                    Tools.showAlert(data.message);
                    return;
                }

                var from = Tools.getQueryValue('from');
                if (from) {
                    location.href = decodeURIComponent(from);
                } else {
                    location.href = config.index;
                }
            });
        }
    });

    //短信发送
    $('#sendMsg').click(function(e) {
        e.preventDefault();
        var _this = $(this);
        if (_this.hasClass('disable-btn')) {
            return;
        }        
        phone.isValid(function(v) {
            if (!v) {
                return;
            }
            _this.addClass('disable-btn');
            Ajax.custom({
                url: config.iSendMsg,
                data: {
                    mobile: phone.val()
                },
                type: 'POST'
            }, function(data) {
                if (data.code != 0) {
                    _this.removeClass('disable-btn');
                    Tools.showAlert(data.message);
                    return;
                }
                changeBtnState(_this);
            }, function(jqxhr, textStatus, errorThrown) {
                log('找回密码第一步失败 ' + textStatus);
                _this.removeClass('disable-btn');
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
                obj.removeClass('disable-btn');
                clearInterval(timer);
            }
        }, 1000);
    }

});
