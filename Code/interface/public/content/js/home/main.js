define(function(require) {
	var $ = require('jquery'),
		config = require('./config'),
		Tools = require('../base/tools'),
		Storage = require('../base/storage'),
		Ajax = require('../base/ajax'),
		Cookie = require('../base/cookie');

	require('../base/common');

	//在线申请滚动
	function initAutoScroll() {
	    var item = $('.home-list').find('.items'),
	        inte, top = 0,
	        h = item.height(),
	        run = function() {
	            inte = setInterval(function() {
	                item.animate({
	                    'top': top
	                }, 300, 'linear', function(){
	                	top -= 35;
	                	if (Math.abs(top) > h) {
	                	    top = -35;
	                	    item.css({
	                	        top: 0
	                	    });
	                	}
	                });
	            }, 2000);
	        };

	    run();

	    item.append(item.children().clone());

	    item.hover(function() {
	        clearInterval(inte);
	    }, function() {
	        run();
	    });
	}
	
	//首页的新闻
	Ajax.custom({
		url: config.homeNews,
		data: {
			size: 18
		}
	},function(response){
		var result = template.render('zj-news-tmpl',{
			'list': response.body || []
		});
		$('#home-news').html(result);

		initAutoScroll();
	});
});