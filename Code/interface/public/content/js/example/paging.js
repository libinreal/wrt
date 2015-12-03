define(function(require) {
    var $ = require('jquery'),
    	config = require('./config'),
        Tools = require('../base/tools'),
    	Ajax = require('../base/ajax');

    require('../base/common');

    //分页请求
    config.paging = function(){
	    Ajax.paging({
	    	url: config.list,
	    });
    };

    config.paging();

    $('.alert').click(function(e){
        e.preventDefault();
        Tools.showAlert('提交完成');
    });

    $('.alert1').click(function(e){
        e.preventDefault();
        Tools.showAlert({
            message: '感谢您的支持，您的订单已提交成功，<br/>请记住您的订单号: <span class="c-green">05468956</span>',
            tick: 5000,
            showTips: true,
            yesCallBack: function(){
                log('alert ok click');
            }
        });
    });

    $('.confirm').click(function(e){
        e.preventDefault();
        Tools.showConfirm('正确了！',function(){
            log('confirm ok click');
        },function(){
            log('confirm cancel click');
        });
    });
});
