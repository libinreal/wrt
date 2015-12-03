define(function(require) {
    var $ = require('jquery'),
    	config = require('./config'),
    	Ajax = require('../base/ajax'),
    	Tools = require('../base/tools');

    require('../base/common');

    //获取广告数据
    //config.getAd();

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
	        'type': 'media'
	    });
	    $('#media-news').html(result || config.nodata);

	    var result = template.render('zj-news-tmpl', {
	        'list': response.body.brandnews || [],
	        'type': 'brand'
	    });
	    $('#brand-news').html(result || config.nodata);

	    var result = template.render('zj-news-tmpl', {
	        'list': response.body.marketnews || [],
	        'type': 'market'
	    });
	    $('#market-news').html(result || config.nodata);
    });

});