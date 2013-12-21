$(document).ready( function() {

    // if the content header contains an anchor tag prevent that clicking on it
    // will trigger the content element to collapse
    $('.collapsible').children('.content-header').find('a').click( function(event) {
        event.stopPropagation();

        return false;
    });


    // deletes the exercise and its related content-element when clicking on
    // a link with the class 'delete-exercise'
    $('.collapsible').children('.content-header').find('.delete-exercise').click( function(event) {

        var trig = $(this);
        var container = trig.parents('.collapsible');
        container.slideToggle('fast', function() {
            container[0].parentNode.removeChild(container[0]);

            var allCollapsible = $('.collapsible').children('.content-header');

            for (var i = 1; i < allCollapsible.length; i++) {
                jQuery(allCollapsible[i]).children('.content-title')[0].innerText = "Aufgabe " + i;
            }
        });
    });


    // deletes the subtask and its related list-element when clicking on
    // a link with the class 'delete-subtask'
    $('.full-width-list').find('.delete-subtask').click( function(event) {
        $(this).parent().remove();
    });
});