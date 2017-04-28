/**
 * @file interactive.js
 * Contains Javascript code for elements with the interactive class.
 */

/**
 * Sets the function that is called when an interactive element is clicked.
 *
 * An interactive element contains a child, that should receive the click instead
 * when the interactive element is clicked the click is forwarded to that element.
 * The child element should have the id target or function.
 */
$(document).ready( function() {
    $('.interactive').click(function() {
        var trig = $(this);

        // find the child element that is the click target
        var target = trig.find('#target')[0];

        // if target is not a link but a function only "button"
        if (!target) {
            target = trig.find('#function');
            target.trigger('click');
        } else {
            // redirect the browser to a new page
            window.location.href = target.attributes.href.value;
        }
    });

    // show a different mouse pointer on the element.
    $('.interactive').children('.content-header').css('cursor','pointer');
});