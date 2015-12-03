define(function(require) {
    var $ = require('jquery'),
        Tool = require('../base/tools'),
        config = require('./config'),
        Ajax = require('../base/ajax'),
        template = require('template');

    require('../base/common');
    var id = Tool.getQueryValue('id');

    // 获取导航列表
    Ajax.custom({
        url: config.help,
        data: {
            'catType': 3005
        }
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

        $('#navList a').on('click', function() {
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
        Ajax.detail({
            url: config.article,
            data: 'id=' + id
        }, function(result) {
            if (!result.body) {
                return;
            }

            $('#type-name').html(result.body.title);
            $('#zj-detail').html(result.body.content);
        });
    }

});
