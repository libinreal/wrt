(function(){

	var id = getQueryValue('id'),
		type = getQueryValue('type');

	//初始化，并获取数据
	//@param t 新闻分类（媒体声音(1001)、品牌新闻(1002)、市场活动(1003)。）
	//@param i 新闻编号
	config.init = function(t,i) {
	    type = t;
	    id = i;

	    //确认type
	    if (type != '1001' && type != '1002' && type != '1003') {
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

	//获取新闻详情
	function getData(){
		$.ajax({
		    url: config.newsDetail,
		    data: {
		    	id: id
		    },
		    dataType: 'JSON',
		    type: 'GET'
		}).then(function(response, textStatus, jqXHR) {
		    if(response.code != 0){
		    	log('获取新闻详情接口失败');
		    	return;
		    }

		    var result = template.render('zj-detail-tmpl', response.body || {});
		    $('#news-detail').html(result);

		}, function(jqXHR, textStatus, errorThrown) {
			log(textStatus)
		});
	}
})();