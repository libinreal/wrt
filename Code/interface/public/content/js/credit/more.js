define(function(require) {
    var $ = require('jquery'),
    	template = require('template'),
    	config = require('./config'),
    	Ajax = require('../base/ajax');

    require('../base/common');
    require('scrollable');

    //获取了解更多
    Ajax.detail({
    	url: config.getintrinfo
    },function(response){
    	if(response.code != 0){
    		return;
    	}

    	$('#zj-content').html(response.body || '');
    });

    //获取广告
    //config.getAd(config.ADENUM.LJGD);

});