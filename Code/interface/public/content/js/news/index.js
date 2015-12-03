define(function(require) {
    //var
     $ = require('jquery'),
    	config = require('./config'),
    	Ajax = require('../base/ajax'),
    	Tools = require('../base/tools');

    require('../base/common');
    require('scrollable');

    //获取广告数据
    config.getAd(config.ADENUM.WRXW);

    //获取公告列表数据
    config.getNotice();
    
    //获取各个栏目新闻列表
    Ajax.custom({
    	url: config.newsHome,
    	data: {
	    	size: 7
	    }
    },function(response){
    	var result = template.render('zj-news-tmpl', {
	        'list': response.body.medianews || [],
	        'type': '1001'
	    });
	    $('#media-news').html(result || config.nodata);

	    var result = template.render('zj-news-tmpl', {
	        'list': response.body.brandnews || [],
	        'type': '1002'
	    });
	    $('#brand-news').html(result || config.nodata);

	    var result = template.render('zj-news-tmpl', {
	        'list': response.body.marketnews || [],
	        'type': '1003'
	    });
	    $('#market-news').html(result || config.nodata);
    });

});