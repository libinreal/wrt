define(function(require, exports) {
	var $ = require('jquery'),
	    config = require('./config'),
	    Ajax = require('../base/ajax'),
	    Tools = require('../base/tools');
	
	require('../base/common');
	
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
        if (response.body.action) {
        	$('.last-div').append('<a class="button" href="javascript:void(0);" id="checkApply">审核通过</a><div></div>');
        }
    });
	
	//审核通过
	$('#zj-detail').on('click', '#checkApply', function (){
		Ajax.custom({
            url: config.checkApply,
            data: {
            	apply_id: applyId,
            }
        }, function(response) {
            if (response.code != 0) {
                Tools.showAlert('审核失败');
                return;
            }
            Tools.showAlert('审核成功', '', function (){
            	location.reload();
            });
            
        });
	});
	
});