define(function(require) {
    var $ = require('jquery'),
    	config = require('./config'),
    	Ajax = require('../base/ajax');

    //获得最新的工程招标信息
    Ajax.custom({
        url: config.getpnewest,
        data: {
            size: 4
        }
    }, function(response) {
        if (response.code != 0) {
            return;
        }

        var result = template.render('zj-project-tmpl', {
            'list': response.body || []
        });
        $('#zj-project').html(result || config.nodata);
    });

    //获得最新的商情信息
    Ajax.custom({
        url: config.getbnewest,
        data: {
            size: 4
        }
    }, function(response) {
        if (response.code != 0) {
            return;
        }

        var result = template.render('zj-business-tmpl', {
            'list': response.body || []
        });
        $('#zj-business').html(result || config.nodata);
    });
});
