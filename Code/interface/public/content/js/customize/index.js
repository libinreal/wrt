define(function(require) {
    var $ = require('jquery'),
    	config = require('./config'),
    	Ajax = require('../base/ajax'),
    	Tools = require('../base/tools');

    require('../base/common');
    require('scrollable');

    var categoryNo;//类别编号

    //分页数据
    config.paging = function(){
    	Ajax.paging({
    		url: config.getapplys,
    		data: {
    			categoryNo: categoryNo,
    			createAt: config.beginTime,
    			size: config.pageSize
    		}
    	});
    };
    config.paging();

    //获取分类信息
    Ajax.custom({
        url: config.getsort
    },function(response){
        var result = template.render('zj-sort-tmpl', {
            'list': response.body
        });
        $('#customize-type').append(result);
    });

    //获取公告
    config.getNotice(config.NOTICEENUM.DZZQ,5);

    //获取广告
    config.getAd(config.ADENUM.DZZQ);


    //类别筛选
    $("#customize-type").on('click','a',function(e){
        e.preventDefault();

        categoryNo = $(this).attr("data-v");
        config.beginTime = undefined;
        config.endTime = undefined;
        config.paging();
    });

    $('#btn-submit').click(function(e){
    	e.preventDefault();
    	    //提示信息框
	    Tools.showConfirm({
	        message: '请选择您申请定制的类型',
	        okText: '工程定制',
	        cancelText: '物资定制',
	        yesCallback: function() {
	            location.href = 'apply-list.html';
	        },
	        noCallback: function(){
	            location.href = 'apply.html';
	        }
	    });
		return;

    })
});