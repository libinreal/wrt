define(function(require) {
    var $ = require('jquery'),
    	config = require('./config'),
    	Ajax = require('../base/ajax'),
    	Tools = require('../base/tools'),
        Storage = require('../base/storage');

    require('../base/common');
    require('../module/validator/local/zh_CN');
    require('../module/jQuery-File-Upload-9.5.7/jquery.fileupload');

    var applyForm = $('#apply-form');

    //回填用户相关数据
    config.afterCheckAccount = function(account){
        $('#account').text(account.account);
        $('#contacts').text(account.contacts);
        $('#telephone').text(account.telephone);
        $('#secondContacts').text(account.secondContacts || '--');
        $('#secondPhone').text(account.secondPhone || '--');
        $('#companyName').text(account.companyName || '--');
        $('#companyAddress').text(account.companyAddress || '--');
    }

    //radio
    $('input[name="nature1"]').click(function(){
        var sel = $('input[name="nature1"]:checked').val();
        $('input[name="nature"]').val(sel);
        applyForm.validator('hideMsg', 'input[name="nature"]');
    })

    //图片上传接口
    $('input[type="file"]').fileupload({
        url : config.upload,
        formData: {flag: 'PUB'},
        dataType : "JSON",
        acceptFileTypes : /(\.|\/)(jpe?g|png)$/i,
        maxFileSize : 5000000,
        done : function(e, data) {
        	if(data.result.code != 0){
        		log(data.result.message || data.result.body);
        		return;
        	}
            $(this).next().attr("src", config.absImg(data.result.body[0]));
            $(this).parent().next().val(data.result.body[0]);
            applyForm.validator('hideMsg', $(this).parent().next());
        },
        process: function(e,data){
            for(var i=0,l=data.processQueue.length;i<l;i++){
                if(data.processQueue[i].action == 'validate'){
                    data.messages.acceptFileTypes = '上传文件格式不支持.';
                }
            }
            data.messages.maxFileSize = '上传文件太大，限制'+data.maxFileSize/1000+'K以内.';
        },
        processalways: function(e,data){
            var index = data.index,
                file = data.files[index];
            if (file.error) {
                alert(file.error);
            }
        },
        fail: function(e){
        }
    });

    //提交申请
    applyForm.validator({
        
        focusCleanup: true,
        msgWrapper: 'div',
        msgMaker: function(opt) {
            return '<span class="' + opt.type + '">' + opt.msg + '</span>';
        },
        fields: {
            "money": {
                rule: "公司注册资本: required;"
            },
            "foundedDate": {
                rule: "公司成立时间: required;"
            },
            "nature": {
                rule: "公司性质: required;"
            },
            "amountLimit": {
                rule: "拟授信额度: required;integer;"
            },
            "use": {
                rule: "拟授信用途附加说明: required;"
            },
            "businessCode": {
                rule: "营业执照编码: required;"
            },
            "taxcode": {
                rule: "税务登记证编码: required;"
            },
            "orgcode": {
                rule: "组织机构代码: required;"
            },
            "businessLicense": {
                rule: "上传营业执照副本: required;"
            },
            "taxcert": {
                rule: "上传税务登记证副本: required;"
            },
            "orgcert": {
                rule: "上传组织机构代码证副本: required;"
            }
        },
        //验证成功
        valid: function(form) {
            Ajax.submit({
                url: config.createEvaluation,
                data: applyForm,
            }, function(data) {
                if (!data || data.code != 0) {
                    Tools.showAlert(data.message);
                    return;
                }

                Tools.showAlert(data.message,0,function(){
                    applyForm[0].reset();
                    $('.form-image img').attr('src',$('.form-image img').attr('data-def'));
                });
            });
        }
    });
});