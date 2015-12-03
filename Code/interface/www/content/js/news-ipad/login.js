	$('.m_c').click(function(){
		$('.m_c_s').show();
	});
	
	$('.m_c_s li').each(function(){
		$(this).mouseover(function(){
			$(this).css({'color':'#000','background':'lightblue'});
		});
		$(this).mouseout(function(){
			$(this).css({'color':'#000','background':'#fff'});
		});
		$(this).click(function(){
			$('#myname').text($(this).text());
			$('.m_c').val($(this).text());
			$('.m_c_s').hide();
		});
	});