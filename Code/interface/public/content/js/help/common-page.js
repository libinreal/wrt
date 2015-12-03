define(function(require) {
    var $ = require('jquery'),
        Tool = require('../base/tools'),
        config = require('./config'),
        Ajax = require('../base/ajax'),
        template = require('template');

    require('../base/common');
    var type = Tool.getQueryValue('type') || 'newhand',
        id = Tool.getQueryValue('id');

    checkType();

    // 接口地址
    var interfaces = {
        newhand: config.newhand,
        guide: config.guide,
        service: config.service,
        about: config.about
    };

    // 获取导航列表
    Ajax.custom({
        url: interfaces[type]
    }, function(result) {
        if (result.code != 0) {
            return;
        }
        if (!result.body || !result.body.length) {
            return;
        }
        $('#navList').append(template.render('navs-tmpl', {
            list: result.body || []
        }));

        //关于我们中添加物融新闻链接
        if(type == 'about'){
            $('#link-to-news').show();
        }

        $('#navList a').on('click', function() {
            if($(this).attr('id') == 'link-to-news'){
                return;
            }
            $('#navList a').removeClass('active');
            $(this).addClass("active");
            seeArticle($(this).attr('data-id'));
        });

        // 默认文章
        if (id) {
            var cur = $('#navList a[data-id="' + id + '"]').addClass('active');
            seeArticle(id);
        } else if (location.hash) {
            var idx = location.hash.replace('#', '');
            var cur = $('#navList a:eq(' + idx + ')');
            cur.addClass('active');
            seeArticle(cur.attr('data-id'));
        } else {
            $('#navList').find('a:first').addClass("active");
            seeArticle(result.body[0].id);
        }
    });

    // 查看文章
    function seeArticle(id) {
        if (!id) return;
        $('#article-name').text($('#navList a[data-id="' + id + '"]').text());
        Ajax.detail({
            url: config.article,
            data: 'id=' + id
        }, function(result) {
            if (!result.body) {
                return;
            }
            $('#zj-detail').html(result.body.content);
        });
    }

    // 检测分类 （售后服务(3001)、新手教学(3002)、采购指南(3003)、关于我们(3004)。）
    function checkType() {
        var name;
        if (type == 'service') {
            classify = '3001';
            name = '售后服务';
        } else if (type == 'newhand') {
            classify = '3002';
            name = '新手教学';
        } else if (type == 'guide') {
            classify = '3003';
            name = '采购指南';
        } else {
            type = 'about';
            classify = '3004';
            name = '关于我们';
        }
        $('#type-name').attr('href','?type='+type).text(name);
        $('title').text(name + '-帮助中心');

        localStorage.setItem('NEWSTYPE', type);
    }
});
