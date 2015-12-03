define(function(require) {
    var $ = require('jquery'),
    	template = require('template'),
    	config = require('./config'),
    	Ajax = require('../base/ajax'),
    	Tools = require('../base/tools');

    require('../base/common');
    require('../module/validator/local/zh_CN');

    config.applycged = config.server + '/credit/applycged';

    var applyForm = $('#apply-form');

    //获取合同列表
    config.getContracts();
    
    //提交追加表单
    applyForm.validator({
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
                rule: "申请增加采购额度: required;integer;"
            },
            "reason": {
                rule: "申请理由: required;"
            }
        },
        valid: function(form) {
            Ajax.submit({
                url: config.applycged,
                data: applyForm,
            }, function(data) {
                if (!data || data.code != 0) {
                    Tools.showAlert(data.message);
                    return;
                }

                Tools.showAlert('您的申请已经提交成功！',0,function(){
                	applyForm[0].reset();
                });
            });
        }
    });
});