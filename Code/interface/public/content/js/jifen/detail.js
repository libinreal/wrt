define(function(require) {
    var $ = require('jquery'),
        config = require('./config'),
        Ajax = require('../base/ajax'),
        Tools = require('../base/tools');

    require('../base/common');

    var id = Tools.getQueryValue('id');

    //获取详情
    Ajax.detail({
        url: config.getDetail,
        data: {
            id: id
        }
    });

    $('#zj-detail').on('click', "#tabBackground li", function() {
        var liHeader = $(this);
        $("div.divBackground.divContentActive").removeClass("divContentActive");
        $("#tabBackground li").removeClass("active");
        liHeader.addClass("active");
        $("div.divBackground:eq(" + $(this).index() + ")").addClass("divContentActive");
    });

});
