/**
 * 自有授信
 */
define(function(require) {
	var $ = require('jquery'),
	    config = require('./config'),
	    Ajax = require('../base/ajax'),
	    Tools = require('../base/tools');
	
	require('../base/common');
	
	//获取列表数据
	config.paging = function() {
		Ajax.paging({
			url  : config.creditList, 
			data : {
				size : config.pageSize
			}, 
			renderFor : 'credit-list-tmpl', 
			renderEle : '#credit-list', 
			timeKey   : 'apply_id'
		});
	};
	config.paging();
});