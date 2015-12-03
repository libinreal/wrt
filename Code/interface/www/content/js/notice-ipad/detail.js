(function(){

	var id = getQueryValue('id'),
		type = getQueryValue('type');

	//初始化，并获取数据
	//@param t 公告分类 2002=商城公告,2003=定制专区公告,2004=积分公告
	//@param i 公告编号
	config.init = function(t,i) {
	    type = t;
	    id = i;

	    //确认type
	    if (type != '2002' && type != '2003' && type != '2004') {
			type = localStorage.getItem('NEWSTYPE');
		}else{
	        localStorage.setItem('NEWSTYPE',type);
	    }

	    // 计算菜单状态
	    $('#menu a').removeClass('active');
	    $('#menu a').each(function() {
	        if ($(this).attr('href').indexOf(type) >= 0) {
	            $(this).addClass('active');
	        }
	    });

	    getData();
	};
	config.init(type, id);

	//获取公告详情
	function getData(){
		$.ajax({
		    url: config.noticeDetail,
		    data: {
		    	id: id
		    },
		    dataType: 'JSON',
		    type: 'GET'
		}).then(function(response, textStatus, jqXHR) {
		    if(response.code != 0){
		    	log('获取公告详情接口失败');
		    	return;
		    }

		    var result = template.render('zj-detail-tmpl', response.body || {});
		    $('#news-detail').html(result);

		}, function(jqXHR, textStatus, errorThrown) {
			log(textStatus)
		});
	}

})();