define(function(require) {
	var $ = require('jquery'),
	    config = require('./config'),
	    Ajax = require('../base/ajax'),
	    Tools = require('../base/tools');

	require('../base/common');
	var contractId = Tools.getQueryValue('contract_id');
	
	//获取合同详情
    Ajax.detail({
        url: config.contractDetail,
        data: {
        	contract_id: contractId, 
        },
        showLoading: true
    }, function(response) {
        if (response.code != 0) {
            return;
        }
    });
});