define(function(require) {
    var $ = require('jquery'),
        template = require('template'),
        config = require('./config'),
        Ajax = require('../base/ajax'),
        Tools = require('../base/tools');

    require('../base/common');

    var chanKind = '',
        chanDateEnum = '';

    $('#creditb-time').on('click', 'a', function(e) {
        e.preventDefault();

        chanDateEnum = $(this).attr('data-v');
        config.paging();
    });

    $('#creditb-type').on('click', 'a', function(e) {
        e.preventDefault();

        chanKind = $(this).attr('data-v');
        config.paging();
    });

    config.paging = function() {
        Ajax.paging({
            url: config.getrestorehistory,
            data: {
                chanKind: chanKind,
                chanDateEnum: chanDateEnum,
            }
        }, function(response) {
            if (response.code != 0) {
                return;
            }
            if (!response.body || response.body.length == 0) {
                $('#zj-list').html(config.nodata);
            }
        });
    }

    config.paging();
});
