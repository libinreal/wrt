define(function(require) {
    var $ = require('jquery'),
        config = require('./config'),
        Ajax = require('../base/ajax');

    require('../base/common');
    require('scrollable');

    var status; //用户登录状态

    config.afterCheckAccount = function(account) {
        status = true;
    };
    config.failedCheckAccount = function() {
        status = false;
    };

    //登录验证
    $('.authority').click(function(e) {
        if (status === false) {
            e.preventDefault();
            $('#modal-login').show();
            return;
        }
    });

    //在线申请滚动
    function initAutoScroll() {
        var item = $('.online-scroll').find('.items'),
            inte, top = 0,
            h = item.height(),
            run = function() {
                inte = setInterval(function() {
                    item.animate({
                        'top': top
                    }, 500, 'linear', function(){
                        top -= 48;
                        if (Math.abs(top) > h) {
                            top = -48;
                            item.css({
                                top: -0
                            });
                        }
                    });
                }, 520);
            },
            scrollTo = function(){
                item.animate({
                    'top': -h
                }, 20000, 'linear', function(){
                    item.css({
                        top: -0
                    });
                    scrollTo();
                });
            };

        scrollTo();

        item.append(item.children().clone());

        item.hover(function() {
            // clearInterval(inte);
            item.stop();
        }, function() {
            scrollTo();
        });
    }

    //获得在线申请
    Ajax.custom({
        url: config.getallapply,
        data: {
            size: 20
        }
    }, function(response) {
        if (response.code != 0) {
            return;
        }

        var data = response.body;
        var result = template.render('zj-credit-tmpl', data);
        $('#zj-credit').html(result);

        initAutoScroll();
    });

    //获取广告
    config.getAd(config.ADENUM.XYC);
});
