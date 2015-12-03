define(function(require) {
    var $ = require('jquery'),
        config = require('./config'),
        Ajax = require('../base/ajax'),
        Tools = require('../base/tools');

    require('../base/common');

    var id = Tools.getQueryValue('id'),
        type = Tools.getQueryValue('type');

    checkType();

    // 计算菜单状态
    $('.content-left a').each(function() {
        if ($(this).attr('href').indexOf(type) >= 0) {
            $(this).addClass('active');
        }
    });

    Ajax.detail({
        url: config.newsDetail,
        data: {
            id: id
        }
    }, function(response) {
        $('#title').text(response.body.title);
    });

    // 检测分类
    function checkType() {
        if (type == '1001' || type == '1002' || type == '1003') {
            localStorage.setItem('NEWSTYPE', type);
        } else {
            type = localStorage.getItem('NEWSTYPE');
        }

        var name;
        if (type == '1001') {
            name = '媒体声音';
        } else if (type == '1002') {
            name = '品牌新闻';
        } else {
            name = '市场活动';
        }
        $('#type-name').text(name).attr('href', 'list.html?type=' + type);
    }

});
