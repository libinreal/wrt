define(function(require) {
    var $ = require('jquery');
    require('../base/common');
    var urls = ['index.html','my-info.', 'my-creditb', 'my-contract.', 'my-note.', 
        /*'my-project', 'my-customize.',*/
        'my-jifen.', 'my-order.',
        'my-bill.', 'my-address.',
        'my-collect.', 'my-security.',
        'help/index.'
    ];
    for (var i = 0; i < urls.length; i++) {
        if (location.href.indexOf(urls[i]) > 0) {
            $('#commonNavLeft .icon-nav-' + (i)).addClass('active');
            break;
        }
    }

});
