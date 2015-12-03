define(function(require) {
    var $ = require('jquery'),
        config = require('./config'),
        Ajax = require('../base/ajax'),
        Tools = require('../base/tools');

    require('../base/common');
    require('scrollable');
    // require('../module/iscroll/iscroll4');

    var type = Tools.getQueryValue('type'),
        position = config.ADENUM.GGGG; //广告位置

    checkType();

    getRec();

    // 计算菜单状态
    $('.content-left a').each(function() {
        if (type && $(this).attr('href').indexOf(type) >= 0) {
            $(this).addClass('active');
        }
    });

    //获取列表数据
    config.paging = function() {
        Ajax.paging({
            url: config.newsList,
            data: {
                type: type,
                size: config.pageSize
            }
        });
    };
    config.paging();

    //获取广告数据
    config.getAd(position);

    // 检测分类 2002=商城公告,2003=定制专区公告,2004=积分公告
    function checkType() {
        var name;
        if (type == '2002') {
            position = config.ADENUM.SCGG;
            name = '商城公告';
        } else if (type == '2003') {
            position = config.ADENUM.DZGG;
            name = '定制专区公告';
        } else if (type == '2004') {
            position = config.ADENUM.JFGG;
            name = '积分公告';
        }
        $('#type-name').text(name);
        if (!name) {
            $('#zj-sep').hide();
        } else {
            $('#zj-sep').show();
        }

        localStorage.setItem('NOTICETYPE', type);
    }

    // 获取置顶公告
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
            width: $('.scroller').width() * (len + 2)
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
