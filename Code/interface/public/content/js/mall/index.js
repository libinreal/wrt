define(function(require) {
    var $ = require('jquery'),
        config = require('./config'),
        Ajax = require('../base/ajax'),
        Storage = require('../base/storage');
        Tools = require('../base/tools');

    require('../base/common');
    require('scrollable');

    // 模板帮助方法，确定商品列表类型
    template.helper('$getLink', function(code, factoryId, id) {
        if (!code)
            return '#';

        var key = code.substring(0, 1),
            host, param;

        if (factoryId) {
            param = '?code=' + code + '&factoryId=' + factoryId;
        } else {
            param = '?code=' + code;
        }
        if (key == '1' || (key == '2' && code.indexOf('2002') != 0)) {
            host = 'list-large.html';
            if(id){
                param += '&id=' + id;
            }
        } else if (id) {
            host = 'detail.html';
            param = '?id=' + id;
        } else {
            host = 'list.html';
        }

        return host + param;
    });

    // 模板帮助方法，确定购物车商品链接
    template.helper('$getCartLink', function(code, id) {
        if (!code)
            return '#';

        var key = code.substring(0, 1),
            host, param;

        if (key == '1' || key == '2') {
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

    //var area = Storage.get(Storage.AREA) || config.defaultArea,
    var area = {"id":"1", "name":""},
        status, //用户登录标志
        flag = {}; //标志某分类下的品牌是否已获取

    //回填用户相关数据
    config.afterCheckAccount = function(account) {
        status = true;
        account.icon = config.getUserIcon(account.icon);
        $('#zj-userinfo').html(template.render('zj-userinfo-tmpl', {
            'account': account || {},
            'area': area
        }));
        getCart();
    };

    config.failedCheckAccount = function() {
        status = false;
        $('#zj-userinfo').html(template.render('zj-nouser-tmpl', {
            'area': area
        }));
    };

    //切换城市
    $('#zj-userinfo').on('click', '.operate', function(e) {
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
        location.href = location.href;
    });
    $("#zj-province").on('click', 'button', function() {
        $("#zj-province button").removeClass("active");
        $(this).addClass("active");
    });

    //分类导航的显示与隐藏
    $('.left-bar').on('mouseover', 'li', function(e) {
        e.preventDefault();

        var code = $(this).attr('data-code');
        if (!code) {
            return;
        }

        $('#cat-' + code).show();
        get(code);
    });
    $('.left-bar').on('mouseout', 'li', function(e) {
        e.preventDefault();

        var code = $(this).attr('data-code');
        if (!code) {
            return;
        }

        $('#cat-' + code).hide();
    });

    //未登录验证
    $('#zj-categorys').on('click', 'a', function(e) {
        if (status === false) {
            e.preventDefault();
            $('#modal-login').show();
            return;
        }
    });
    $('#zj-goods').on('click', 'a', function(e) {
        if (status === false) {
            e.preventDefault();
            $('#modal-login').show();
            return;
        }
    });
    $('#zj-userinfo').on('click', '.auth', function(e) {
        if (status === false) {
            e.preventDefault();
            $('#modal-login').show();
            return;
        }
    });

    //首次登陆弹出城市选择框
    if (!area) {
        $('#modal-province').addClass('in').show();
        if ($("#zj-province").children().length == 0) {
            config.getProvince();
        }
    } else {
        getMainData();
    }

    //弹出购物车下拉框
    $('#zj-userinfo').on('mouseover', '.settleup', function() {
        $(this).addClass('hover');
    });
    $('#zj-userinfo').on('mouseout', '.settleup', function() {
        $(this).removeClass('hover');
    });

    //获取分类数据
    Ajax.custom({
        url: config.getAllCategory
    }, function(response) {
        if (response.code != 0) {
            return;
        }

        var result = template.render('zj-category-tmpl', {
            'list': response.body || []
        });
        $('#zj-category').html(result);
    });

    //获取推荐商品+推荐品牌
    function getMainData() {
        Ajax.custom({
            url: config.getrdgoods,
            data: {
                areaId: area.id
            }
        }, function(response) {
            if (response.code != 0) {
                return;
            }

            var goods = response.body || [];
            Ajax.custom({
                url: config.getrdbrands,
                data: {
                    areaId: area.id,
                    code: undefined
                }
            }, function(response) {
                if (response.code != 0) {
                    return;
                }

                //返回值格式：{10000:[{},{}],20000:[{},{}]...}
                //处理推荐商品+推荐品牌数据，转换成以下data形式
                var brands = response.body,
                    data = []; //[{name: '', goods: [], brands: []}...]
                for (var i in goods) {
                    var good = {};
                    good.goods = goods[i] || [];
                    good.brands = brands[i] || [];
                    good.name = config.topCategory[i] || '';
                    good.type = i;
                    data.push(good);
                }

                var result = template.render('zj-goods-tmpl', {
                    'list': data,
                    'status': status
                });
                $('#zj-goods').html(result);
            });
        });
    }

    //获取公告
    config.getNotice(config.NOTICEENUM.JJSC, 8);

    //获取广告
    config.getAd(config.ADENUM.JJSC);

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

    //获取可用采购额度
    Ajax.custom({
        url: config.getbuyamt
    }, function(response) {
        if (response.code != 0) {
            return;
        }
        if(Tools.formatCurrency1(response.body) != 0) {
        	$('#buy-amt').html(Tools.formatCurrency1(response.body));
        }

    });

    //获取某分类下的推荐品牌
    function getBrands(code) {
        if (flag[code] || !area) {
            return;
        }

        Ajax.custom({
            url: config.getrdbrands,
            data: {
                areaId: area.id,
                code: code
            }
        }, function(response) {
            if (response.code != 0) {
                return;
            }

            flag[code] = true;

            var data = response.body;
            if (data.length == 0) {
                //没有数据
                return;
            }
            //返回值格式：{10010100:[{},{}],10010200:[{},{}]...}
            for (var i in data) {
                var result = template.render('zj-brand-tmpl', {
                    'list': data[i],
                    'code': i
                });
                $('#brand-' + i).html(result);
            }
        });
    }

    //新分类推荐品牌
    function get(code) {
           if (flag[code] || !area) {
               return;
           }

           Ajax.custom({
               url: config.ad,
               data: {
                   position: config.position[code],
                   code: code
               }
           }, function(response) {
               if (response.code != 0) {
                   return;
               }

               flag[code] = true;

               var data = response.body;
               if (data.length == 0) {
                   //没有数据
                   return;
               }
               
               var result = template.render('zj-bds-tmpl', {
                   'list': data
               });
               $('#zj-bds-' + code).html(result);
           });
       }


});
