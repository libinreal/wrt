define(function(require) {
    var $ = require('jquery'),
    	template = require('template'),
    	config = require('./config'),
    	Ajax = require('../base/ajax'),
    	Tools = require('../base/tools');

    require('../base/common');

    var time = Tools.getQueryValue('time');////d3:3天到期，d5:5天到期，d15:15天到期，inm:一个月内到期，outm:一个月后到期

    //打开时间筛选
    $('.bill_select').hover(function(){
    	$(this).children().show();
    },function(){
    	$(this).children().hide();
    });

    $('.bill_select').on('click','a',function(e){
    	e.preventDefault();
    	var time = $(this).attr('data-time');
    	getBills(time);
    	$(this).parent().hide();
    });

    getBills();

    //获取到期通知数据
    function getBills (time) {
		Ajax.custom({
			url: config.getbillnotice,
			data: {
				time: time
			}
		},function(response){
			if(response.code != 0){
				log(response.message);
				return;
			}

			$('#zj-notice').text(response.body.noticelab);
			if(response.body.noticeList.length == 0){
				$('#zj-bill').html(config.nodata);
				return;
			}

			var result = template.render('zj-bill-tmpl',{
				'list': response.body.noticeList || []
			});
			$('#zj-bill').html(result);

		});
	}

});