$(document).ready( function() {
    // get all elements with class .content-header with index greater 2 and hide the first element .content-body
    $('.content-header:gt(2)').next('.content-body').hide();
    /**
     * toggle function on click to hide/show .content-header elements
     */
    $('.content-header').click( function() {
        // trig = event sender
        var trig = $(this);
        // toggle the next available element of .content-body near the "trig" with duration "fast"
        trig.next('.content-body').slideToggle('fast');
    });
    // set mouse curser on mouse-over to pointer 
    $('.content-header').css('cursor','pointer');
});