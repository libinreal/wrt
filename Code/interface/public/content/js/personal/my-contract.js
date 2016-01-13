/**
 * 我的合同js
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
            url: config.contractList,
            data: {
                customer_id : 1, 
                size : config.pageSize, 
                timeKey : 'create_time'
            }
        });
    };
    config.paging();
    console.log(config.beginTime);
});