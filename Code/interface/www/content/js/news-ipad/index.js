(function(){

	//获取广告数据
	getAd(2, function(response){
		initScroll();
	});

	//获取公告列表数据
	getNotice();

	//获取各个栏目新闻列表
	$.ajax({
	    url: config.newsHome,
	    data: {
	    	size: 7
	    },
	    dataType: 'JSON',
	    type: 'GET'
	}).then(function(response, textStatus, jqXHR) {
	    if(response.code != 0){
	    	log('获取各个栏目新闻列表接口失败');
	    	return;
	    }

	    var result = template.render('zj-news-tmpl', {
	        'list': response.body.medianews || [],
	        'type': '1001'
	    });
	    $('#media-news').html(result);

	    var result = template.render('zj-news-tmpl', {
	        'list': response.body.brandnews || [],
	        'type': '1002'
	    });
	    $('#brand-news').html(result);

	    var result = template.render('zj-news-tmpl', {
	        'list': response.body.marketnews || [],
	        'type': '1003'
	    });
	    $('#market-news').html(result);

	}, function(jqXHR, textStatus, errorThrown) {
		log(textStatus)
	});
})();