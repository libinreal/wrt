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

    //获取详情
    Ajax.detail({
        url: config.newsDetail,
        data: {
            id: id
        }
    }, function(response) {
        $('#title').text(response.body.title);
    });

    // 检测分类 2002=商城公告,2003=定制专区公告,2004=积分公告
    function checkType() {
        if (type == '2002' || type == '2003' || type == '2004') {
            localStorage.setItem('NOTICETYPE', type);
        } else {
            type = localStorage.getItem('NOTICETYPE');
        }

        var name;
        if (type == '2002') {
            name = '商城公告';
        } else if (type == '2003') {
            name = '定制专区公告';
        } else {
            name = '积分公告';
        }
        $('#type-name').text(name).attr('href', 'list.html?type=' + type);
    }

});
