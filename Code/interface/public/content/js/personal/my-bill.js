define(function(require) {
    var $ = require('jquery'),
        config = require('./config'),
        Ajax = require('../base/ajax'),
        Tools = require('../base/tools');

    require('../base/common');

    // 模板帮助方法，确认订单状态
    template.helper('$getStatus', function(content) {
        if(!content)
            return '--';
        return config.ORDERSTATUS[content] || '--';
    });

    //获取列表数据
    config.paging = function() {
        Ajax.paging({
            url: config.getlist,
            data: {
                status: config.ORDERENUM.DZZ,
                size: config.pageSize
            }
        });
    };
    config.paging();

});
