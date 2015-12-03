define(function(require) {
    var $ = require('jquery'),
        config = require('./config'),
        Ajax = require('../base/ajax'),
        Tools = require('../base/tools');
    require('../base/common');
    
    //信用等级详情
    Ajax.detail({
        url: config.creditdetail
    }, function(response) {
        if (response.code != 0) {
            return;
        }
    });

});
