define(function(require) {
    var $ = require('jquery');
    require('../base/common');
    var urls = ['index.html','my-info.', 'my-creditb', 'my-contract.', 'my-note.', 
        'my-credit.', 'my-order.',
        'my-bill.', 'my-address.',
        'my-collect.', 'my-security.',
        'help/index.', 'my-project'
    ];
    for (var i = 0; i < urls.length; i++) {
    	if (location.href.indexOf('add-address') > 0) {
    		$('#commonNavLeft .icon-nav-8').addClass('active');
            break;
    	}
        if (location.href.indexOf(urls[i]) > 0) {
        	if (urls[i] == 'my-project') {
        		$('#commonNavLeft .icon-nav-6').addClass('active');
                break;
        	} else {
        		$('#commonNavLeft .icon-nav-' + (i)).addClass('active');
                break;
        	}
            
        }
    }

});
