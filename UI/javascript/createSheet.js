$(document).ready( function() {
    suppressPropagation();
    addClickFunction();
});

function suppressPropagation() {
    // if the content header contains an anchor tag prevent that clicking on it
    // will trigger the content element to collapse
    $('.collapsible').children('.content-header').find('a').click( function(event) {
        event.stopPropagation();

        return false;
    });
}

function addClickFunction() {
    // deletes the exercise and its related content-element when clicking on
    // a link with the class 'delete-exercise'
    $('.collapsible').children('.content-header').find('.delete-exercise').unbind('click');
    $('.collapsible').children('.content-header').find('.delete-exercise').click( function(event) {

        var container = $(this).parents('.collapsible');
        container.slideToggle('fast', function() {
            container[0].parentNode.removeChild(container[0]);

            renumberExercises();
        });
    });


    // deletes the subtask and its related list-element when clicking on
    // a link with the class 'delete-subtask'
    $('.full-width-list').find('.delete-subtask').unbind('click');
    $('.full-width-list').find('.delete-subtask').click(function(event) {
        var trig = $(this);
        trig.parent().slideToggle('fast', function() {
            trig.parent().remove();
        });
    });


    // adds a new exercise when clicking on a link with the class 'add-exercise'
    // at the end of the page
    $('.content-header').find('.add-exercise').unbind('click');
    $('.content-header').find('.add-exercise').click(function(event) {
        // find last content-element
        var lastExercise = $('.content-element').last();

        // append content to last exercise
        $('#loadTarget').load("include/CreateSheet/ExerciseSettings.template.html",
                              function() {
            // create a new content element in a special div
            var newExercise = $('#loadTarget').children('.content-element');

            // animate inserting the new element
            lastExercise.toggle();
            lastExercise.after(newExercise);
            lastExercise.slideToggle('fast');

            // add click funtions to the content elements
            renumberExercises();
            makeChildrenCollapsible();
            suppressPropagation();
            addClickFunction();
        });
    });
}

function renumberExercises() {
    var allCollapsible = $('.content-wrapper').children('.collapsible');

    for (var i = 1; i < allCollapsible.length; i++) {
        jQuery(allCollapsible[i]).find('.content-title')[0].innerText = "Aufgabe " + i;
    }
}