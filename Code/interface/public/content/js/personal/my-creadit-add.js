define(function(require) 
{
	var $ = require('jquery'),
	    config = require('./config'),
	    Ajax = require('../base/ajax'),
	    Storage = require('../base/storage'),
	    Tools = require('../base/tools'),
	    msg = require('../base/messages');
	
	require('../module/validator/local/zh_CN');
	require('../base/common');
	require('fileupload');
	
	//合同列表
	Ajax.paging({
		url : config.CreditContract, 
		renderFor : 'contract-list-tmpl', 
		renderEle : '#contract_list'
	});
	//upload attachment
    $('#uploadFile').fileupload({
        url: config.applyAttachment,
        dataType: "JSON",
        replaceFileInput : false, 
        acceptFileTypes: /(\.|\/)(jpe?g|png)$/i,
        maxFileSize: 5000000,
        process: function(e, data) {
        	alert('test')
            for (var i = 0, l = data.processQueue.length; i < l; i++) {
                if (data.processQueue[i].action == 'validate') {
                    data.messages.acceptFileTypes = '上传文件格式不支持.';
                }
            }
            data.messages.maxFileSize = '上传文件太大，限制' + data.maxFileSize / 1000 + 'K以内.';
        },
        done: function(e, data) {
        	if (data.result.code == -1) {
        		alert(data.result.message);
        		$(this).val('');
        		$('#apply_img').val('');
        	} else {
        		$('#apply_img').val(data.result.body[0]);
        	}
            
        }
    });
	
	//提交表单
	$('#saveCreditForm').validator({
        focusCleanup: true,
        msgWrapper: 'div',
        msgMaker: function(opt) {
            return '<span class="' + opt.type + '">' + opt.msg + '</span>';
        },
        fields: {
            "apply_amount": {
                rule: "申请额度: required;length[1~30]"
            },
            "contract_id": {
                rule: "用途: required"
            },
            "apply_remark": {
                rule: "备注: required"
            }, 
            "img": {
                rule: "申请附件: required"
            }
        },
        //验证成功
        valid: function(form) {
            Ajax.submit({
                url: config.CreditAdd,
                data: $('#saveCreditForm')
            }, function(data) {
                if (!data || data.code != 0) {
                    return;
                }
                //Tools.showAlert('地址保存成功！');
                $('#saveCreditForm')[0].reset();
                location.href = 'my-credit.html'
            });
        }
    });
});