$(document).ready( function() {
    // get all elements with class .sheet-header with index greater 2 and hide the first element .content-body-wrapper

    $('.collapsible:gt(2)').children(('.content-body-wrapper, .content-footer')).hide();
    $('.collapsible:gt(2)').children('.content-header').addClass('inactive');
    /**
     * toggle function on click to hide/show .content-header elements
     */
    $('.collapsible').children('.content-header').click( function(event) {
        // trig = event sender
        var trig = $(this);
        // toggle the next available element of .content-body-wrapper near the "trig" with duration "fast"
        if (trig.parent('.collapsible').length !== 0) {
            trig.parent().children('.content-body-wrapper, .content-footer').slideToggle('fast');
            trig.toggleClass( 'inactive',  !trig.hasClass('inactive') );
        }
    });

    // if the content header contains an anchor tag prevent that clicking on it
    // wil trigger the content element to collapse
    $('.collapsible').children('.content-header').find('a').click( function(event) {
        event.stopPropagation();
        return false;
    });

    // set mouse curser on mouse-over to pointer
    $('.collapsible').children('.content-header').css('cursor','pointer');
});