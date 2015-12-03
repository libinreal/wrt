define(function(require) {
    var $ = require('jquery'),
        config = require('./config'),
        Ajax = require('../base/ajax'),
        Tools = require('../base/tools');

    require('../base/common');

    var type = Tools.getQueryValue('type');

    //分页列表
    config.paging = function() {
        Ajax.paging({
            url: config.getList,
            data: {
                categoryId: type,
                size: 12
            },
            timeKey: 'updateAt'
        });
    };
    config.paging();

    //获取菜单
    Ajax.custom({
        url: config.getCategorys
    }, function(response) {
        var category = response.body || [];
        var result = template.render('zj-category-tmpl', {
            'list': category
        });
        $('#zj-category').append(result);

        // 计算菜单状态
        $('#zj-category a').each(function() {
            if ($(this).attr('href').indexOf('type=' + type) >= 0) {
                $(this).addClass('active');
            }
        });

        setCategoryName(category, type);
    });


    //获取分类名称
    function setCategoryName(categorys, id) {
        var name = '';
        for (i = 0; i < categorys.length; i++) {
            if (categorys[i].id == id) {
                name = categorys[i].name;
                break;
            }
        }
        $('#category-name').text(name);
        $('#type-name').text(name);
    }

});
