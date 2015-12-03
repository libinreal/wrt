define(function(require) {
    var $ = require('jquery'),
        config = require('./config'),
        Ajax = require('../base/ajax'),
        Tools = require('../base/tools'),
        Storage = require('../base/storage');

    require('../base/common');

    //功能清单：获取详细数据，同类数据，浏览历史，评论列表，发表评论，添加购物车
    var tempData;//缓存当前商品数据

    //浏览历史显示与隐藏
    $(".look-history").click(function() {
        var w = $(".history-content").outerWidth();
        var sw = $(this).outerWidth();
        if ($(this).hasClass("active")) {
            $("#panelBg").hide();
            $(this).removeClass("active");
            $(".history-content").animate({
                "margin-right": '-965px'
            });
        } else {
            getHistory();
            $("#panelBg").show();
            $(this).addClass("active");
            $(".history-content").animate({
                "margin-right": '0px'
            });
        }
    });

    //其他内容切换
    $('.intro_nav').on('click', 'a', function(e) {
        e.preventDefault();

        $('.intro_nav a').removeClass('active');
        $(this).addClass('active');
        $('.shop_intro_detail').children().hide();
        $($(this).attr('data-to')).show();

        //商品评价
        if ($(this).attr('data-to') == '#sppj') {
            config.paging();
        }
    });

    //图片切换效果
    $('#zj-detail').on('click', '.s-img', function(e) {
        e.preventDefault();

        $('.bimg').attr('src', $(this).attr('data-big'));
    });

    //收藏or取消
    $('#zj-detail').on('click', '.collect', function(e) {
        e.preventDefault();
        config.saveFavorite($(this), id);
    });

    //改变商品数量
    var num = 1,
        maxNum = 99; //当前商品数量
    $('#zj-detail').on('click', '.num-apply a', function(e) {
        e.preventDefault();

        if ($(this).hasClass('minus')) {
            num--;
        } else {
            num++;
        }

        if (num < 1) {
            num = 1
        } else if (num > maxNum) {
            num = maxNum;
        }

        $('.num-input').val(num);
    });
    $('#zj-detail').on('keyup', '.num-input', function(e) {
        var v = $(this).val();

        if (isNaN(v)) {
            v = 1;
        }

        num = v;
        if (num < 1) {
            num = 1
        } else if (num > maxNum) {
            num = maxNum;
        }
        $('.num-input').val(num);
    });

    //切换城市
    $('#zj-detail').on('click', '.operate', function(e) {
        e.preventDefault();
        if ($(this).prop('readonly')) {
            return;
        }
        $('#modal-province').addClass('in').show();
        if ($("#zj-province").children().length > 0) {
            return;
        }
        config.getProvince('','','',1);
    });
    var $pro_change = $(".province-change"),
        $panel_bg = $(".panel-bg");
    //确认后赋值并隐藏省份选择框 
    $("#confirm_pro").click(function() {
        $('#modal-province').hide();
        var d = {};
        var cur = $("#zj-province button[class='active']");
        d.name = cur.html();
        d.id = cur.attr('data-id');

        if (cur.length == 0) {
            return;
        }

        Storage.set(Storage.AREA, d);
        $(".distribution-choose").html(d.name);
        if(tempData.salesArea != '全国'){
            if(d.name != '全国'){
                $('#quality-status').text('无货');
                $('#quality-num').text(0);
                $('.num-input').val(0);
                $('#add-cart').addClass('disabled');
            }else{
                $('#quality-status').text(storeNum > 0 ? '有货' : '无货');
                $('#quality-num').text(data.storeNum || '--');
                $('.num-input').val(1);
                $('#add-cart').removeClass('disabled');
            }
        }
    });
    $("#zj-province").on('click', 'button', function() {
        $("#zj-province button").removeClass("active");
        $(this).addClass("active");
    });

    //评论
    var minTextNum = 5, //最小评论文字
        maxTextNum = 500; //最大评论文字
    $('#text').keyup(function() {
        // var v = $(this).val().replace(/[^\x00-\xff]/g, '__').length
        var v = $(this).val().length

        $('#text-stat').text(maxTextNum - v);
    });

    var id = Tools.getQueryValue('id');

    Ajax.custom({
        url: config.getDetail,
        data: {
            id: id
        }
    }, function(response) {
        if (response.code != 0) {
            return;
        }

        var data = response.body.goodsDetail || {},
            nav = response.body.nav || [],
            lastNav = nav[nav.length - 1] || {};
        var result = template.render('zj-detail-tmpl', response.body.goodsDetail);
        $('#zj-detail').html(result);

        //缓存当前数据
        tempData = data;

        //最大货量
        maxNum = parseInt(data.storeNum);

        //设置商品信息
        var result = template.render('zj-detail1-tmpl', {
            'goods': response.body.goodsDetail || {},
            'type': lastNav
        });
        $('#zj-detail1').html(result);

        //设置缩略图
        $('#thumb').attr('src', config.absImg(data.thumb));

        //设置返回列表
        $('.back').attr('href', 'list.html?code=' + lastNav.code);

        //设置附加信息
        $('#spjs').html(data.des);
        $('#sysm').html(data.instr);
        $('#shbz').html(data.afterSale);
        /*var resultStr = '';
        // for (var i = 0; i < data.spec.length; i++) {
        for (var i in data.spec) {
            resultStr += '<tr><td class="ttitle">' + data.spec[i].name + '</td><td>' + data.spec[i].value + '</td></tr>'
        }
        $('#ggjs table').html(resultStr);*/
        $('#ggjs').html('<p class="ttitle">' + data.spec + '</p>');

        //设置导航
        var result = template.render('zj-navi-tmpl', {
            'list': nav,
            'name': response.body.goodsDetail.name
        });
        $('#zj-navi').html(result);

        //标题栏
        var result = template.render('zj-title-tmpl', {
            'list': nav,
            'name': response.body.goodsDetail.name
        });
        $('#zj-title').html(result);

        //发表评论绑定
        $('#send').click(sendComment);

        //添加购物车绑定
        $('#add-cart').click(function(e) {
            e.preventDefault();
            if($(this).hasClass('disabled')){
                return;
            }
            config.addToCart(id, $('.num-input').val());
        });

        //详细导航显示
        $('.intro_nav').show();
        $('.shop_intro_list').show();
    });

    //获取浏览历史
    function getHistory() {
        Ajax.custom({
            url: config.getHistory
        }, function(response) {
            if (response.code != 0) {
                return;
            }

            var result = template.render('zj-history-tmpl', {
                'list': response.body || []
            });
            $('#zj-history').html(result);

            //浏览历史中的收藏按钮
            $('.operate-shoucang').click(function(e) {
                e.preventDefault();
                config.saveFavorite($(this));
            });

            //浏览历史中的添加购物车
            $('.operate-cart').click(function(e) {
                e.preventDefault();
                config.addToCart($(this).attr('data-id'));
            });
        });
    }

    //获取评论
    config.paging = function() {
        Ajax.paging({
            url: config.getcommonList,
            data: {
                id: id,
                size: config.pageSize
            },
            renderFor: 'zj-comment-tmpl',
            renderEle: '#zj-comment',
            key: 'content'
        }, function(response) {
            if (response.body.length == 0) {
                $('#zj-comment').html(config.nodata);
            }
            $('#total').text(response.body.total)
        })
    }

    //发表评论
    function sendComment() {
        if ($('#text').val().length < minTextNum) {
            Tools.showToast('评论内容最少5个字符');
            return;
        }
        if ($('#text').val().length > maxTextNum) {
            Tools.showToast('评论内容最多500个字符');
            return;
        }

        Ajax.custom({
            url: config.addCommon,
            data: {
                goodsId: id,
                content: $('#text').val()
            },
            type: 'POST'
        }, function(response) {
            if (response.code != 0) {
                Tools.showToast(response.message || '发表评论失败');
                return;
            }
            Tools.showToast('发表评论成功');
            $('#text').val('');
            $('#text-stat').text(maxTextNum);

            config.paging();
        });
    }

    //同类商品推荐
    Ajax.custom({
        url: config.getSimilar,
        data: {
            id: id
        },
        type: 'get'
    }, function(response) {
        if (response.code != 0) {
            return;
        }
        if (response.body.length == 0) {
            return;
        }
        var result = template.render('zj-tltj-tmpl', {
            'list': response.body
        });
        $('#zj-tltj').html(result);

    });

});
