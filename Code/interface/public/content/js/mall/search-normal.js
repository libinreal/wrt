define(function(require) {
    var $ = require('jquery'),
        config = require('./config'),
        Ajax = require('../base/ajax'),
        Tools = require('../base/tools');

    require('../base/common');

    var key = Tools.getQueryValue('key');

    //分页查询
    config.paging = function() {
        Ajax.paging({
            url: config.goodsFindBP,
            data: {
                key: key,
                size: 12
            }
        }, function(response) {
        });
    };

    config.paging();

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

});
