$(document).ready( function() {
    // get all elements with class .sheet-header with index greater 2 and hide the first element .content-body-wrapper

    console.log($('.collapsible:gt(2)'));

    $('.collapsible:gt(2)').children('.content-body-wrapper').hide();
    $('.collapsible:gt(2)').children('.content-header').addClass('inactive');
    /**
     * toggle function on click to hide/show .content-header elements
     */
    $('.collapsible').children('.content-header').click( function() {
        // trig = event sender
        var trig = $(this);
        // toggle the next available element of .content-body-wrapper near the "trig" with duration "fast"
        if (trig.parent('.collapsible').length != 0) {
            trig.next('.content-body-wrapper').slideToggle('fast');
            trig.toggleClass( 'inactive',  !trig.hasClass('inactive') );
        }
    });
    // set mouse curser on mouse-over to pointer 
    $('.collapsible').children('.content-header').css('cursor','pointer');
});