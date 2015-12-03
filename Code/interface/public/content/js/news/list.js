define(function(require) {
    var $ = require('jquery'),
        config = require('./config'),
        Ajax = require('../base/ajax'),
        Tools = require('../base/tools');

    require('../base/common');
    require('scrollable');
    // require('../module/iscroll/iscroll4');

    var type = Tools.getQueryValue('type'),
        position = {
            '1001': 3,
            '1002': 4,
            '1003': 5
        };

    checkType();
    getRec();

    // 计算菜单状态
    $('.content-left a').each(function() {
        if ($(this).attr('href').indexOf(type) >= 0) {
            $(this).addClass('active');
        }
    });

    //获取列表数据
    config.paging = function() {
        Ajax.paging({
            url: config.newsList,
            data: {
                catType: type,
                size: config.pageSize
            }
        });
    };
    config.paging();

    //获取广告数据
    config.getAd(position[type]);

    // 检测分类 （媒体声音(1001)、品牌新闻(1002)、市场活动(1003)。）
    function checkType() {
        var name;
        if (type == '1001') {
            name = '媒体声音';
        } else if (type == '1002') {
            name = '品牌新闻';
        } else {
            name = '市场活动';
        }
        $('#type-name').text(name);

        localStorage.setItem('NEWSTYPE', type);
    }

    // 获取置顶新闻
    function getRec() {
        Ajax.custom({
            url: config.newsTop,
            data: {
                type: type
            }
        }, function(response, textStatus, jqXHR) {
            if (response.code != 0) {
                return;
            }

            var d = response.body || {};
            if (!d || d.length == 0) {
                //接口返回值可能为空数组，这里过滤掉
                return;
            }

            d.type = type;
            var result = template.render('zj-rec-tmpl', d);
            $('#rec-detail').html(result);
            initScroll2();

        });
    }

    var initScroll2 = function() {
        var len = $('.scroller').children().length;
        if (len == 0) {
            return;
        }
        $('.scroller').css({
            width: $('.scroller .item').width() * (len + 2)
        });
        $('.slider').scrollable({
            circular: true,
            items: '.scroller'
        }).autoscroll({
            autoplay: true
        }).navigator({
            navi: '.subscript',
            naviItem: 'span'
        });
    }

    //初始化滚动图片
    var initScroll = function(opt, mode) {
        var nav = $('.subscript');
        var len = $('.scroller').children().length;
        if (len == 0) {
            return;
        }
        $('.scroller').css({
            width: $('.scroller').width() * len
        });
        var res = '';
        for (var i = 0; i < len; i++) {
            if (i == 0) {
                res += '<span class="active"></span>';
            } else {
                res += '<span></span>';
            }
        }
        nav.html(res);
        previewScroll = new iScroll($('.slider')[0], {
            snap: true,
            momentum: false,
            hScrollbar: false,
            onScrollEnd: function() {
                var cur = $('#img-' + this.currPageX);
                cur.attr('src', cur.attr('data-src'));
                nav.find('span').removeClass('active');
                nav.find('span').eq(this.currPageX).addClass('active');
                $(nav.find('span')[this.currPageX]).addClass('active');
            }
        });

        $('#img-0').attr('src', $('#img-0').attr('data-src'));

    };

});
