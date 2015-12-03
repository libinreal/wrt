define(function(require) {
    var $ = require('jquery'),
        config = require('./config'),
        Ajax = require('../base/ajax'),
        Tools = require('../base/tools'),
        SpryMap = require('sprymap');

    require('../base/common');

    var type = Tools.getQueryValue('type');

    var map = new SpryMap({
        id: "worldMap",
        height: 600,
        width: 920,
        startX: 600,
        startY: 600,
        cssClass: "mappy"
    });

    //初始化url数据
    if (!type) {
        type = location.hash.replace('#', '');
    }
    if (type) {
        $('.content-left a').each(function(e) {
            if ($(this).attr('href').indexOf(type) >= 0) {
                $(this).addClass('active');
            }
        });
    }

    //点击菜单
    $('.content-left a').click(function(e) {
        $('.content-left a').removeClass('active');
        $(this).addClass('active');
        type = $(this).attr('href').replace('#', '');
        getData()
    });

    getData();

    //获取数据
    function getData() {
        Ajax.paging({
            url: config.getprjtotal,
            data: {
                type: type
            }
        }, function(response) {
            if (response.code != 0) {
                return;
            }

            var w = 966 - 100,
                h = 728 - 100,
                ow = 109,
                oh = 95;
            $('.bidding-tooltip').each(function() {
                var that = $(this),
                    areaId = $(this).attr('data-id');

                for (var i = 0; i < config.coordinate.length; i++) {
                    var d = config.coordinate[i];
                    if (d.id == areaId) {
                        that.css({
                            left: d.x -ow,
                            top: d.y - oh
                        });
                        break;
                    }
                }
            });
        });
    }

    //获取友情链接
    Ajax.custom({
        url: config.getZbLinks
    }, function(result) {
        if (!result || result.code != 0) {
            return;
        }
        $('#linksData').append(template.render('linksTmpl', {
            list: result.body || []
        }));
    });
});
