$(document).ready( function() {

    // if the content header contains an anchor tag prevent that clicking on it
    // wil trigger the content element to collapse
    $('.collapsible').children('.content-header').find('a').click( function(event) {
        event.stopPropagation();

        return false;
    });

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
});