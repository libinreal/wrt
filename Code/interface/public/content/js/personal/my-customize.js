define(function(require) {
    var $ = require('jquery'),
        config = require('./config'),
        Ajax = require('../base/ajax'),
        Tools = require('../base/tools');
    require('../base/common');

    // 模板帮助方法，获取状态文字，
    // 0=已提交，1=已受理，2=定制成功
    template.helper('$getStatus', function(content, defaultValue) {
        if (content == 0) {
            return '已提交'
        } else if (content == 1) {
            return '已受理'
        } else {
            return '定制成功'
        }
    });

    var type = Tools.getQueryValue('type');
    var expire = '',
        categoryNo = '';

    var isA = true,
        optionA = {
            url: config.myapplys,
            data: {
                expire: expire,
                categoryNo: categoryNo,
                size: config.pageSize,
            },
            renderFor: 'zj-exchange-tmpl',
            renderEle: '#exchange'
        },
        optionB = {
            url: config.myproject,
            data: {
                size: config.pageSize,
            },
            renderFor: 'zj-record-tmpl',
            renderEle: '#record'
        };


    //获取列表数据
    config.paging = function() {
        Ajax.paging(isA ? optionA : optionB, function(response) {
            if (response.body.length == 0) {
                return;
            }
            if (isA) {
                optionA.beginTime = response.body[0].createAt;
                optionA.endTime = response.body[response.body.length - 1].createAt;
            } else {
                optionB.beginTime = response.body[0].createAt;
                optionB.endTime = response.body[response.body.length - 1].createAt;
            }
        });
    };
    config.paging();

    //定制切换
    $('.nav-tabs a').click(function(e) {
        e.preventDefault();
        var name = $(this).attr('href');
        var target = $(name);

        $('.nav-tabs a').each(function() {
            $(this).removeClass('active');
            target.hide();
        });
        $('.tab-item').hide();
        $(this).addClass('active');
        target.show();

        isA = name == '#exchange';
        config.beginTime = isA ? optionA.beginTime : optionB.beginTime;
        config.endTime = isA ? optionA.endTime : optionB.endTime

        config.paging();
    });

    $('#mycustomize-time').on('click', 'a', function(e) {
        e.preventDefault();

        if (!isA) return;

        optionA.data.expire = $(this).attr('data-v');
        config.paging();
    });

    $('#mycustomize-type').on('click', 'a', function(e) {
        e.preventDefault();

        if (!isA) return;
        optionA.data.categoryNo = $(this).attr('data-v');
        config.paging();
    });

    Ajax.custom({
        url: config.getsort
    }, function(response) {
        var result = template.render('zj-sort-tmpl', {
            'list': response.body
        });
        $('#mycustomize-type').append(result);
    });


});
