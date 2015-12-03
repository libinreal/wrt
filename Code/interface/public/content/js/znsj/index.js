define(function(require) {
    var $ = require('jquery'),
        config = require('../base/config');
        msg = require('./messages'),
        rules = require('./rules'),
        Tools = require('../base/tools'),
        Storage = require('../base/storage'),
        config = require('./config'),
        Ajax = require('../base/ajax'),
        Cookie = require('../base/cookie');

    require('../base/common');
    require('scrollable');

    config.getAd(config.ADENUM.ZNSJ);

    var username = $('#znsj-form input[name="account"]'),
        password = $('#znsj-form input[name="password"]'),
        verifyCode = $('#znsj-form input[name="verifyCode"]'),
        loginHistory = {},
        isAppend = false;

    //表单验证
    function validateForm() {
        if (rules.empty.test(username.val())) {
            Tools.showAlert(msg.unEmpty);
            return false;
        }
        if (rules.empty.test(password.val())) {
            Tools.showAlert(msg.pwEmpty);
            return false;
        }
        // if (rules.empty.test(verifyCode.val())) {
        //     Tools.showAlert(msg.vcEmpty);
        //     return false;
        // }
        if (!rules.username.test(username.val())) {
            Tools.showAlert(msg.usernameErr);
            return false;
        }
        if (!rules.password.test(password.val())) {
            Tools.showAlert(msg.pwErr);
            return false;
        }
        // if (!rules.password.test(verifyCode.val())) {
        //     Tools.showAlert(msg.vcodeErr);
        //     return false;
        // }
        return true;
    }

    //登录
    $('#znsj-form').submit(function(e) {
        e.preventDefault();
        if (!validateForm()) {
            return;
        }

        Ajax.submit({
            url: config.login,
            data: $(this),
        }, function(data) {
            if (!data || data.code != 0) {
                Tools.showAlert(data.message);
                return;
            }
            location.href = location.href;
        });
    });

    $('.m_c').click(function(){
        $('.m_c_s').toggle();
    });
    $('.m_c_s li').click(function(){
        $('.m_c').val($(this).text());
        $('.name').text($(this).attr('data-v'));
        $('.m_c_s').hide();
    });
    var inte;
    $('.m_c_s').mouseenter(function(){
        if(inte){
            clearTimeout(inte);
        }
    });
    $('.m_c_s').mouseleave(function(){
        var that = $(this);
        inte = setTimeout(function(){
            that.hide();
        },300);
    });
    $('.m_c').mouseenter(function(){
        if(inte){
            clearTimeout(inte);
        }
    });
    $('.m_c').mouseleave(function(){
        inte = setTimeout(function(){
            $('.m_c_s').hide();
        },300);
    });

});
