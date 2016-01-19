define(function(require) {
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
    });
});