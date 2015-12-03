define(function(require) {
    var $ = require('jquery'),
        config = require('./config'),
        Ajax = require('../base/ajax'),
        Tools = require('../base/tools');

    require('../base/common');

    var id = Tools.getQueryValue('id');

    Ajax.detail({
        url: config.getDetail,
        data: {
            id: id
        }
    });
});
