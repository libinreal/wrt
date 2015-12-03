define(function(require) {
    var $ = require('jquery'),
        config = require('./config'),
        Ajax = require('../base/ajax'),
        Tools = require('../base/tools');

    require('../base/common');

    var key = Tools.getQueryValue('key');

    //
    Ajax.custom({
        url: config.goodsFind,
        data: {
            key: key
        }
    }, function(response) {
        if (response.code != 0) {
            return;
        }

        var result = template.render('zj-dazong-tmpl', {
            'list': response.body.dataDZ || [],
            'key': key,
            'count': response.body.countDZ
        });
        $('#zj-dazong').html(result || config.nodata);

        var result = template.render('zj-biaopin-tmpl', {
            'list': response.body.dataBP || [],
            'key': key,
            'count': response.body.countBP
        });
        $('#zj-biaopin').html(result || config.nodata);
    });

    //收藏
    $('#zj-dazong').on('click', '.operate-shoucang', function(e) {
        e.preventDefault();
        config.saveFavorite($(this));
    });

    //添加购物车
    $('#zj-dazong').on('click', '.operate-cart', function(e) {
        e.preventDefault();
        config.addToCart($(this).attr('data-id'));
    });

    //收藏
    $('#zj-biaopin').on('click', '.operate-shoucang', function(e) {
        e.preventDefault();
        config.saveFavorite($(this));
    });

    //添加购物车
    $('#zj-biaopin').on('click', '.operate-cart', function(e) {
        e.preventDefault();
        config.addToCart($(this).attr('data-id'));
    });

});
