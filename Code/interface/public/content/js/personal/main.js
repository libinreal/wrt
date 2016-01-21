define(function(require) {
    var $ = require('jquery');
    require('../base/common');
    var urls = ['index.html','my-info.', 'my-creditb', 'my-contract.', 'my-note.', 
        'my-credit.', 'my-order.',
        '', 'my-address.',
        'my-collect.', 'my-security.',
        'help/index.'
    ];
    var attachName = ['add-address', 'my-project', 'my-credit-add', 'my-kid-order'], 
    	attachValue = {'add-address':8, 'my-project':6, 'my-credit-add':5, 'my-kid-order':6}, 
    	number = 0;
    for (var i = 0; i < urls.length; i++) {
    	
    	for(var j = 0; j < attachName.length; j++) {
    		if (location.href.indexOf(attachName[j]) > 0) {
    			number = attachValue[attachName[j]];
    			$('#commonNavLeft .icon-nav-' + number).addClass('active');
                break;
    		}
    		
    	}
    	
        if (location.href.indexOf(urls[i]) > 0) {
        	$('#commonNavLeft .icon-nav-' + (i)).addClass('active');
            break;
            
        }
    }

});
