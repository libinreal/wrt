define(function(require) {
    var $ = require('jquery'),
        Ajax = require('../base/ajax'),
        config = require('./config'),
        Tools = require('../base/tools'),
        msg = require('../base/messages');

    require('../base/common');
    require('echarts');
    var macaronsTheme = require('macarons_theme');

    //echarts 图标数据
    var myEcharts = echarts.init($('.charts')[0], macaronsTheme);

    var option = {
            grid: {
                x: 40,
                y: 10,
                x2: 40,
                y2: 30
            },
            tooltip: {
                trigger: 'axis'
            },
            toolbox: {
                show: false,
                feature: {
                    mark: {
                        show: true
                    },
                    magicType: {
                        show: true,
                        type: ['line', 'bar']
                    },
                    restore: {
                        show: true
                    },
                    saveAsImage: {
                        show: true
                    }
                }
            },
            calculable: true,
            xAxis: [{
                type: 'category',
                boundaryGap: false,
                axisTick: {
                    show: false
                },
                data: ['周一', '周二', '周三', '周四', '周五', '周六', '周日']
            }],
            yAxis: [{
                type: 'value',
                axisLabel: {
                    formatter: '{value}'
                }
            }],
            series: [{
                name: '价格波动图',
                type: 'line',
                data: [11, 11, 15, 13, 12, 13, 100]
            }]
        },
        categoryCodes = {
            gangcai: '10010000',
            shuini: '20010000',
            liqing: '20020000',
            gangjiaoxian: '10020000'
        },
        category; //当前分类

    //获取初始数据
    var key = location.hash || 'gangcai';
    key = key.replace('#', '');
    $('.business-nav a').each(function() {
        if ($(this).attr('for') == key) {
            $(this).addClass('active');
        }
    })
    category = categoryCodes[key];
    getlistData();

    //切换类别
    $('.business-nav a').click(function(e) {
        if ($(this).hasClass('active')) {
            return;
        }
        category = categoryCodes[$(this).attr('for')];
        $('.business-nav a').removeClass('active');
        $(this).addClass('active');
        getlistData();
    });

    //切换厂商的价格走势图
    $('.business-list').on('click', 'li', function() {
        if ($(this).hasClass('active')) {
            return;
        }
        $('.business-list li').removeClass('active');
        $(this).addClass('active');
        getPrice($(this).attr('data-wcode'), $(this).attr('data-brand'));
    });

    //获取友情链接
    $('#btn-more').click(function(e) {
        e.preventDefault();

        $('#modal-business').show().addClass('in');
        var dialog = $('#modal-business').find('.modal-dialog');
        var mtop = ($(window).height() - dialog.height()) / 2;
        dialog.css({
            'margin-top': mtop
        });
        if ($('#links-data').children().length > 0) {
            return;
        }

        Ajax.custom({
            url: config.getSqLinks
        }, function(result) {
            if (!result || result.code != 0) {
                return;
            }
            $('#links-data').append(template.render('links-tmpl', {
                list: result.body || []
            }));

            var dialog = $('#modal-business').find('.modal-dialog');
            var mtop = ($(window).height() - dialog.height()) / 2;
            dialog.css({
                'margin-top': mtop
            });
        });
    });


    // 获得指定类别的推荐商品信息
    function getlistData() {
        Ajax.custom({
            url: config.iCategoryList,
            data: {
                categorys: category
            }
        }, function(response) {
            if (!response || response.code != 0) {
                return;
            }

            var data = response.body || [];
            //var data = [];

            var names = ['生铁', '铸造用生铁', '冷铸车轮用生铁', '优质碳素结构钢', '保证淬透性钢', '电工用热轧硅钢'];
            var factorys = ['富钢国际', '马钢', '武钢防城', '方大特钢', '福建三安', '昆明钢铁', '方大特钢', '乌钢公司', '重庆钢铁'];

            var d1 = {
                brandId: "20",
                factory: "富钢国际",
                name: "Gr 75盘螺",
                price: "888.000",
                spec: "0.6*1250*c",
            };

            var dt = new Date();
            //模拟数据
            if (data.length == 0) {
                for (var i = 0; i < 7; i++) {
                    var d = {};
                    d.brandId = ~~(Math.random() * 50);
                    d.name = names[~~(Math.random() * names.length)];
                    d.factory = factorys[~~(Math.random() * factorys.length)];
                    d.price = (Math.random() * 899).toFixed(2);
                    d.spec = '0.6*1250*c';
                    d.vary = ~~(Math.random() * 500) - ~~(Math.random() * 400);
                    d.time = dt.getTime();
                    data.push(d);
                }
            }

            var result = template.render('businessListDataTmpl', {
                'list': data
            });
            $('#businessData').html(result);

            getPrice(data[0].wcode, data[0].brandId);
        });
    }

    //获取价格走势图
    function getPrice(wcode, brandId) {
        Ajax.custom({
            url: config.getpriceline,
            data: {
                'wcode': wcode,
                'brandId': brandId
            }
        }, function(response) {
            if (response.code != 0) {
                return;
            }

            var xAxis = response.body.updates || [],
                series = response.body.prices || [];
            //模拟数据
            if (xAxis.length == 0) {
                var cur = ~~(Math.random() * 5);
                for (var i = 0; i < 14; i++) {
                    series.push(~~(Math.random() * 1000));
                    xAxis.push('9-' + ++cur);
                };
            }
            option.xAxis[0].data = xAxis;
            option.series[0].data = series;
            myEcharts.clear();
            myEcharts.setOption(option);

        });
    }

});
