define(function(require) {
    var $ = require('jquery'),
        config = require('./config'),
        Ajax = require('../base/ajax'),
        Tools = require('../base/tools');

    require('../base/common');

    var type = Tools.getQueryValue('type'),
        areaId = Tools.getQueryValue('areaId');

    if (areaId) {
        $('.bidding-menu-content a').each(function() {
            var a = $(this).attr('href');
            $(this).attr('href', a + '&areaId=' + areaId);
        });
    }
    if (type) {
        $('.bidding-menu-content a').each(function() {
            if($(this).attr('href').indexOf(type) >= 0){
                $(this).addClass('active')
            }
        });
    }

    //获取数据
    config.paging = function() {
        Ajax.paging({
            url: config.getbiddingList,
            data: {
                type: type,
                areaId: areaId,
                size: config.pageSize
            }
        });
    };

    config.paging();
});
