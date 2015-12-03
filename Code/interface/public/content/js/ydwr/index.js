define(function(require) {
    var $ = require('jquery'),
    	config = require('../base/config');

    require('../base/common');
    require('scrollable');

    //顶部滚动广告
    config.getAd(config.ADENUM.YDWR);

    //顶部右侧广告
    config.getAd(config.ADENUM.YDWR2,function(response){
    	if(response.body.length > 0)
    		$('.top-qr').html('<img src="'+config.absImg(response.body[0].adimg)+'" alt=""/>')
    });

});
