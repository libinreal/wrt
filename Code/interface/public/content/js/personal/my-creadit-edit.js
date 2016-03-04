define(function(require) {
    var $ = require('jquery'),
        config = require('./config'),
        Ajax = require('../base/ajax'),
        Storage = require('../base/storage'),
        Tools = require('../base/tools'),
        msg = require('../base/messages');
    
    require('../module/validator/local/zh_CN');
    require('../base/common');
    require('fileupload');
	
	var applyId = Tools.getQueryValue('apply_id');
	
    //授信详情
    Ajax.detail({
        url: config.getCreditDetail,
        data: {
            apply_id: applyId
        }
    }, function(response) {
        if (response.code != 0) {
            return;
        }
        //upload attachment
        $('#uploadFile').fileupload({
            url: config.applyAttachment,
            dataType: "JSON",
            replaceFileInput : false, 
            acceptFileTypes: /(\.|\/)(jpe?g|png)$/i,
            maxFileSize: 5000000,
            done: function(e, data) {
                $('#apply_img').val(data.result.body[0]);
                $('#apply_img_show').attr("src", "/apply_attachment/"+data.result.body[0])
            },
            process: function(e, data) {
                for (var i = 0, l = data.processQueue.length; i < l; i++) {
                    if (data.processQueue[i].action == 'validate') {
                        data.messages.acceptFileTypes = '上传文件格式不支持.';
                    }
                }
                data.messages.maxFileSize = '上传文件太大，限制' + data.maxFileSize / 1000 + 'K以内.';
            }
        });
    });

    //表单
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
        }
    });

    //保存
    $('#zj-detail').on('click', '#saveCredit', function(e) {
        var formData = Ajax.formJson("#saveCreditForm");
        var data = {};
        var validate = true;
        $.each(formData, function(k, v){
            if(v != ""){
                data[k] = v;
                if($("textarea[name="+k+"]").length){
                    $("textarea[name="+k+"]").css("border","1px solid #cacaca");
                }
                if($("input[name="+k+"]").length){
                    $("input[name="+k+"]").css("border","1px solid #cacaca");
                }
            }else{
                validate = false;
                if($("textarea[name="+k+"]").length){
                    $("textarea[name="+k+"]").css("border","1px solid #F00");
                }
                if($("input[name="+k+"]").length){
                    $("input[name="+k+"]").css("border","1px solid #F00");
                }
            }
        });
        if(validate == false){
            return false;
        }
        Ajax.custom({
            url: config.CreditSave,
            data: formData
        }, function(response) {
            if (response.code != 0) {
                Tools.showToast(response.message || '保存失败');
                return;
            }
            Tools.showToast("保存成功");
        });
    });  
});