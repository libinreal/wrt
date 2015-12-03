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
            url: config.projectList,
            data: {
                size: config.pageSize,
                createAt: config.beginTime
            }
        });
    };
    config.paging();

    //取消订单
    $('#zj-list').on('click', 'button', function(e) {
    	e.preventDefault();
    	
        var that = $(this);
        Tools.showConfirm('确认取消订单吗？', function() {
            Ajax.custom({
                url: config.orderustatus,
                data: {
                    id: that.attr('data-id'),
                    status: config.ORDERENUM.YQX
                }
            }, function(response) {
                if (response.code != 0) {
                    return;
                }
                
                that.parents('a').fadeOut('normal', function() {
                    $(this).remove();
                });

            });
        });
    });

});
