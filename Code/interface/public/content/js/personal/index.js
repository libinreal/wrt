define(function(require) {
    var $ = require('jquery'),
        config = require('./config'),
        Ajax = require('../base/ajax');

    require('../base/common');

    //回填用户相关数据
    var tempIcon;
    config.afterCheckAccount = function(account) {
        tempIcon = account.icon;
        $('.user-icon').attr('src', config.getUserIcon(account.icon))
    }

    //通过信用额度登记机构获取用户信息
    Ajax.custom({
        url: config.mycreamt
    }, function(response) {
        if (response.code != 0) {
            return;
        }

        var data = response.body;

        if (tempIcon) {
            data.icon = config.getUserIcon(tempIcon); //用户头像
        }

        var result = template.render('zj-summary-tmpl', data);
        $('#zj-summary').html(result);

    });

    //历史浏览记录
    Ajax.custom({
        url: config.historylist,
        data: {
            size: 6
        }
    }, function(response) {
        var result = template.render('zj-list-tmpl', {
            'list': response.body
        });
        $('#zj-list').append(result || config.nodata);

	});

	//收藏
	$('#zj-list').on('click', '.operate-shoucang', function(e) {
	    e.preventDefault();
	    config.saveFavorite($(this));
	});

	//添加购物车
	$('#zj-list').on('click', '.operate-cart', function(e) {
	    e.preventDefault();
	    config.addToCart($(this).attr('data-id'));
	});

    //推薦訂單
    Ajax.custom({
        url: config.projectList,
        data: {
            size: 1
        }
    }, function(response) {
        var data = response.body;
        if(!data || data.length == 0){
            $('#zj-recommend').html(config.nodata);
            return;
        }

        var result = template.render('zj-recommend-tmpl', data[0]);
        $('#zj-recommend').html(result || config.nodata);

    });

});