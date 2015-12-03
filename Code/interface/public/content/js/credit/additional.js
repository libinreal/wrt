define(function(require) {
    var $ = require('jquery'),
        template = require('template'),
        config = require('./config'),
        Ajax = require('../base/ajax'),
        Tools = require('../base/tools');

    require('../base/common');
    require('../module/validator/local/zh_CN');

    var additionalForm = $('#additional-form');

    //获取合同列表
    config.getContracts();

    //提交追加表单
    additionalForm.validator({

        focusCleanup: true,
        msgWrapper: 'div',
        msgMaker: function(opt) {
            return '<span class="' + opt.type + '">' + opt.msg + '</span>';
        },

        fields: {
            "contractNo": {
                rule: "合同编号: required;"
            },
            "name": {
                rule: "联系人: required;"
            },
            "phone": {
                rule: "联系电话: required;[mobile|tel];"
            },
            "amount": {
                rule: "申请增加信用额度: required;integer;"
            },
            "reason": {
                rule: "申请理由: required;"
            }
        },
        valid: function(form) {
            Ajax.submit({
                url: config.applyxyed,
                data: additionalForm,
            }, function(data) {
                if (!data || data.code != 0) {
                    Tools.showAlert(data.message);
                    return;
                }

                log(data);
                Tools.showAlert('您的申请已经提交成功！', 0, function() {
                    additionalForm[0].reset();
                });
            });
        }
    });
});
