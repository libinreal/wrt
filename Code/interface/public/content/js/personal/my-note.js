define(function(require) {
	var $ = require('jquery'),
    config = require('./config'),
    Ajax = require('../base/ajax'),
    Tools = require('../base/tools');

	require('../base/common');
	
	//获取列表数据
    config.paging = function() {
        Ajax.paging({
            url: config.getNoteList,
            data: {
                size : config.pageSize
            }, 
            timeKey  : 'bill_id'
        });
    };
    config.paging();
});