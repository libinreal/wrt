define(function(require) {
	var $ = require('jquery'),
	    config = require('./config'),
	    Ajax = require('../base/ajax'),
	    Tools = require('../base/tools');

	require('../base/common');
	var billId = Tools.getQueryValue('bill_id');
	
	//获取票据详情
    Ajax.detail({
        url: config.noteDetail,
        data: {
            bill_id: billId, 
        },
        showLoading: true
    }, function(response) {
        if (response.code != 0) {
            return;
        }
    });
});