define(function(require) {
    var $ = require('jquery');

    require('../base/common');

	$('.m_c').click(function(){
		$('.m_c_s').toggle();
	});
	$('.m_c_s li').click(function(){
		$('.m_c').val($(this).text());
        $('.name').text($(this).attr('data-v'));
		$('.m_c_s').hide();
	});
	var inte;
	$('.m_c_s').mouseenter(function(){
	    if(inte){
	        clearTimeout(inte);
	    }
	});
	$('.m_c_s').mouseleave(function(){
	    var that = $(this);
	    inte = setTimeout(function(){
	        that.hide();
	    },300);
	});
	$('.m_c').mouseenter(function(){
	    if(inte){
	        clearTimeout(inte);
	    }
	});
	$('.m_c').mouseleave(function(){
	    inte = setTimeout(function(){
	        $('.m_c_s').hide();
	    },300);
	});
});
