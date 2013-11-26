$(document).ready( function() {
    // get all elements with class .sheet-header with index greater 2 and hide the first element .content-body-wrapper
    $('.sheet-header:gt(2)').next('.content-body-wrapper').hide();
    $('.sheet-header:gt(2)').addClass('inactive');
    /**
     * toggle function on click to hide/show .content-header elements
     */
    $('.sheet-header').click( function() {
        // trig = event sender
        var trig = $(this);
        // toggle the next available element of .content-body-wrapper near the "trig" with duration "fast"
        trig.next('.content-body-wrapper').slideToggle('fast');
        trig.toggleClass( 'inactive',  !trig.hasClass('inactive') );
    });
    // set mouse curser on mouse-over to pointer 
    $('.sheet-header').css('cursor','pointer');
});