define(function(require) {
    var $ = require('jquery'),
        msg = require('./messages'),
        Ajax = require('../base/Ajax'),
        Tools = require('../base/tools'),
        Storage = require('../base/storage');

    require('../base/common');

    $('#login-form').submit(function(e) {
        e.preventDefault();

        var phone = $('input[name="phone"]').val(),
            password = $('input[name="password"]').val();
        if (phone.isEmpty()) {
            Tools.showTip('爷的手机号码不能为空', 5000);
            return;
        }
        if (!phone.isPhone()) {
            Tools.showTip('爷的手机号码格式不正确', 5000);
            return;
        }
        if (password.isEmpty()) {
            Tools.showTip('爷的密码不能为空', 5000);
            return;
        }
        if (!password.isValidPwd()) {
            Tools.showTip('爷的密码格式不正确', 5000);
            return;
        }

        Ajax.submitForm({
            url: config.login,
            data: $(this)
        }, function(data) {
            if (data.code != 'OK') {
                if (data.message == '账号不存在！') {
                    Tools.showTip('爷的手机号码还未注册', 5000);
                } else {
                    Tools.showTip('爷的密码错误', 5000);
                }
                return;
            }
            if (!data.result) {
                Tools.showTip('爷，服务器异常，请稍后再试～', 5000);
                return;
            }

            //记住我，若记住则记录用户手机号以便下次登录
            if ($('input[name="remember"]:checked')) {
                Storage.set(Storage.REMEMBER, phone);
            } else {
                Storage.remove(Storage.REMEMBER);
            }

            Storage.set(Storage.AUTH, data.result.id);
            Storage.set(Storage.ACCOUNT, data.result);

            var from = Tools.getQueryValue('from');
            if (from) {
                location.href = decodeURIComponent(from);
            } else {
                location.href = "hui/index";
            }
        });

    });

});
