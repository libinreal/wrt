define(function(require) {
    var $ = require('jquery'),
        config = require('./config'),
        Ajax = require('../base/ajax'),
        Tools = require('../base/tools');

    require('../base/common');
    var id = Tools.getQueryValue('id');

    //获取订单详情
    Ajax.detail({
        url: config.myprojectdetail,
        data: {
            id: id
        }
    }, function(response) {
        if (response.code != 0) {
            return;
        }
    });

});
