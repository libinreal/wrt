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
        	$('#checkApply').show();
        }
    });
	
	//审核通过
	$('#checkApply').on('click', function (){
		checkApply();
	});
	
	function checkApply (){
		Ajax.custom({
            url: config.checkApply,
            data: {
                id: applyId,
            }
        }, function(response) {
            if (response.code != 0) {
                Tools.showToast('审核失败');
                return;
            }
            
        });
	}
});