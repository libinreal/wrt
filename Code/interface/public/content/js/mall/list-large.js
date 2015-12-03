define(function(require) {
    var $ = require('jquery'),
        config = require('./config'),
        Ajax = require('../base/ajax'),
        Tools = require('../base/tools'),
        Storage = require('../base/storage');

    require('../base/common');

    var area = Storage.get(Storage.AREA) || config.defaultArea,
        code = Tools.getQueryValue('code'),
        factoryId = Tools.getQueryValue('factoryId'), //厂商编号
        id = Tools.getQueryValue('id'), //特殊商品编号
        sortKey = '', //排序字段，price(价格),salesNum（销量），level（信誉等级），storeNum(可销售量)
        sort = 'desc', //排序方式，默认倒叙
        queryKeys; //格式：属性ID1:属性值；属性ID2:属性值

    // 模板帮助方法，确定购物车商品链接
    template.helper('$getCartLink', function(code, id) {
        if (!code)
            return '#';

        var key = code.substring(0, 1),
            host, param;

        if (key == '1' || (key == '2' && code.indexOf('2002') != 0)) {
            host = 'list-large.html';
            param = '?code=' + code;
            if(id){
                param += '&id=' + id;
            }
        } else {
            host = 'detail.html';
            param = '?id=' + id;
        }

        return host + param;
    });

    //分页列表数据
    config.paging = function() {
        Ajax.paging({
            url: config.searchdz,
            data: {
                code: code,
                areaId: area.id,
                factoryId: factoryId,
                goodsId: id,
                queryKeys: queryKeys,
                sortkey: sortKey,
                sort: sort,
                size: config.pageSize
            },
            key: 'goods'
        }, function(response) {
            if ($('#zj-navi').children().length == 0) {
                //第一次加载初始化导航
                var result = template.render('zj-navi-tmpl', {
                    'list': response.body.nav || [],
                    'area': area
                });
                $('#zj-navi').html(result);

                //标题栏
                var result = template.render('zj-title-tmpl', {
                    'list': response.body.nav || [],
                    'area': area
                });
                $('#zj-title').html(result);

                //特殊
                if(id){
                    $('#zj-list li:eq(0)').addClass('on');
                }

                getCart();
            }
        });
    };

    if(!area){
        $('#modal-province').addClass('in').show();
        if ($("#zj-province").children().length == 0) {
            config.getProvince();
        }
    }else{
        config.paging();
    }

    //切换城市
    $('#zj-navi').on('click', '.operate', function(e) {
        e.preventDefault();
        if ($(this).prop('readonly')) {
            return;
        }
        $('#modal-province').addClass('in').show();
        if ($("#zj-province").children().length > 0) {
            return;
        }
        config.getProvince();
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
        location.href = location.href;
    });
    //取消按钮
    $(".modal-close").click(function() {
        $('#modal-province').hide();
    });
    $("#zj-province").on('click', 'button', function() {
        $("#zj-province button").removeClass("active");
        $(this).addClass("active");
    });

    //搜索条件切换
    $(".filter-condition").on('click', '.more', function(e) {
        e.preventDefault();
        var btn = $(this);
        if (btn.hasClass("shouqi")) {
            btn.removeClass("shouqi");
            btn.parent().parent().removeClass('active');
        } else {
            btn.addClass("shouqi");
            btn.parent().parent().addClass('active');
        }
    });

    //排序切换
    $('.filter-orderby').on('click', '.filter-orderby-item', function(e) {
        e.preventDefault();

        var isDesc = $(this).hasClass('desc');
        sortKey = $(this).attr('data-key');
        $('.filter-orderby-item').removeClass('asc').removeClass('desc');
        if (isDesc) {
            sort = 'asc';
            $(this).addClass('asc');
        } else {
            sort = 'desc';
            $(this).addClass('desc');
        }

        id = undefined;
        config.paging();
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

    //搜索条件点击
    $('.filter-condition').on('click', 'a.link', function(e) {
        e.preventDefault();

        // if ($(this).hasClass('on')) {
        //     return;
        // }
        var flag = $(this).hasClass('on');

        $(this).parent().parent().find('a').removeClass('on');
        if(!flag){
            $(this).addClass('on');
        }

        if ($(this).parents('.filter-item').hasClass('area')) {
            //{"name":"上海市","id":"25"}
            var d = {};
            d.name = '';
            d.id = $(this).attr('data-id');
            area = d;
        }

        if ($(this).parents('.filter-item').hasClass('factory')) {
            factoryId = $(this).attr('data-id');
        }

        var result = '';
        $('#zj-condition a.on').each(function() {
            var id = $(this).attr('data-id');
            var v = $(this).attr('data-value');
            if (id && v)
                result += id + ':' + v + ';';
        });
        queryKeys = result;

        id = undefined;
        config.paging();
    });

    //搜索條件中的分類
    $('.filter-condition').on('click', 'a.link0', function(e) {
        var code = $(this).attr('data-code');
        var key = code.substring(0, 1);
        var host = 'list.html';
        var param = '?code=' + code;
        if (key == '1' || (key == '2' && code.indexOf('2002') != 0)) {
            host = 'list-large.html';
        } else {
            host = 'list.html';
        }
        location.href = host + param;;
    });

    //弹出购物车下拉框
    $('#zj-navi').on('mouseover','.settleup', function(){
        $(this).addClass('hover');
    });
    $('#zj-navi').on('mouseout','.settleup', function(){
        $(this).removeClass('hover');
    });

    //获取购物车数量
    function getCart(){
        Ajax.custom({
            url: config.getCartLast
        }, function(response) {
            if (response.code != 0) {
                return;
            }
            if (!response.body.total || response.body.total == 0) {
                //没有商品
                $('#settleup-empty').show();
                $('#settleup-content').hide();
                return;
            }
            $('#cart-no').text(response.body.total || 0);
            $('#settleup-content').html(template.render('zj-cart-good-tmpl', response.body));
        });
    }

    //获取商品分类的搜索条件
    Ajax.custom({
        url: config.goodsCondition,
        data: {
            code: code
        }
    }, function(response) {
        if (response.code != 0) {
            return;
        }

        var data = response.body && response.body.category,
            tmpl = 'zj-category-tmpl';
        if(!data || data.length == 0){
            tmpl = 'zj-condition-tmpl';
            data = response.body.condition || [];
        }
        var result = template.render(tmpl, {
            'list': data
        });
        $('#zj-condition').html(result);
    });

    //获取厂商
    Ajax.custom({
        url: config.getFactoryList,
        data: {
            code: code
        }
    }, function(response) {
        if (response.code != 0) {
            return;
        }

        var result = '';
        for (var i = 0; i < response.body.length; i++) {
            result += '<li><a class="link" href="#" data-id="' + response.body[i].id + '">' + response.body[i].factoryName + '</a></li>';
        };
        $('#zj-factory').html(result);
    });

    //获取区域
    config.getProvince('', '', function(response) {
        if (response.code != 0) {
            return;
        }

        var result = '';
        for (var i = 0; i < response.body.length; i++) {
            result += '<li><a class="link" href="#" data-id="' + response.body[i].id + '">' + response.body[i].name + '</a></li>';
        };
        $('#zj-area').html(result);
    },1)

});
