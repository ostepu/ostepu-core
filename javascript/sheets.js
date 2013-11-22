$(document).ready( function() {
	$('.content-header:gt(2)').next('.content-body').hide();
	$('.content-header').click( function() {
		var trig = $(this);
		if ( trig.hasClass('content-header_active') ) {
			trig.next('.content-body').slideToggle('fast');
			trig.removeClass('content-header_active');
		} else {
			//$('.content-header_active').next('.content-body').slideToggle('fast');
			//$('.content-header_active').removeClass('content-header_active');
			trig.next('.content-body').slideToggle('fast');
			trig.addClass('content-header_active');
		};
		return false;
	});
	$('.content-header').css('cursor','pointer');
});