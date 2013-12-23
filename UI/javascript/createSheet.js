$(document).ready( function() {
    // suppress Propagation
    $('.collapsible').children('.content-header').find('a').on("click",suppressPropagation);
    $('.interactive.add').children('.content-header').find('a').on("click",suppressPropagation);

    // map click events
    $('.collapsible').children('.content-header').find('.delete-exercise').on("click",deleteExercise);
    $('.full-width-list').find('.delete-subtask').on("click",deleteSubtask);
    $('.interactive.add').children('.content-header').find('.add-exercise').on("click",addExercise);

    $('#submitSheet').on("click", function(event) {
        $('#submitSheetButton').click();
    });
});

// rename exercise headers with correct enumeration
// rename inputs
function renumberExercises() {
    var allCollapsible = $('.collapsible');

    for (var i = 1; i < allCollapsible.length; i++) {
        var current = jQuery(allCollapsible[i]);
        current.children('.content-header').children('.content-title')[0].innerText = "Aufgabe " + i;
        var body = current.find('.content-body');
        console.log(body);
        var newHTML = body[0].innerHTML.replace(/(exercises\[)([0-9]+)(\])/, "$1" + (i - 1) + "$3");
        body[0].innerHTML = newHTML;
    }
}

// if the content header contains an anchor tag prevent that clicking on it
// will trigger the content element to collapse
function suppressPropagation(event) {
    event.stopPropagation();

    return false;
}


// deletes the exercise and its related content-element when clicking on
// a link with the class 'delete-exercise'
function deleteExercise(event) {

    var trig = $(this);
    var container = trig.parents('.collapsible');
    container.slideToggle('fast', function() {
        container[0].parentNode.removeChild(container[0]);

        renumberExercises();
    });
}


// deletes the subtask and its related list-element when clicking on
// a link with the class 'delete-subtask'
function deleteSubtask(event) {
    var trig = $(this);
    trig.parent().slideToggle('fast', function() {
        trig.parent().remove();
    });
}


// adds a new exercise when clicking on a link with the class 'add-exercise'
// at the end of the page
function addExercise(event) {

    // append content to last exercise
    $.get("include/CreateSheet/ExerciseSettings.template.html", function (data) {

        var collapsible = $(".collapsible");
        if (collapsible.length == 1) {
            $(".add").last().after(data);
        } else {
            $(".collapsible").last().after(data);
        }

        // animate new element
        $('.collapsible').last().hide().fadeIn(1000);

        // map click events on new exercise
        $('.collapsible').last().children('.content-header').find('a').on("click",suppressPropagation);
        $('.collapsible').last().children('.content-header').find('.delete-exercise').on("click",deleteExercise);
        $('.full-width-list').last().find('.delete-subtask').on("click",deleteSubtask);
        $('.collapsible').last().children('.content-header').on("click",collapseElement);

        // set mouse curser on mouse-over to pointer
        $('.collapsible').last().children('.content-header').css('cursor','pointer');

        renumberExercises();
    });
}