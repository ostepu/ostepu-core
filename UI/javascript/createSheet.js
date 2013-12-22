$(document).ready( function() {
    // suppress Propagation
    $('.collapsible').children('.content-header').find('a').on("click",suppressPropagation);
    $('.interactive.add').children('.content-header').find('a').on("click",suppressPropagation);
    
    // map click events
    $('.collapsible').children('.content-header').find('.delete-exercise').on("click",deleteExercise);
    $('.full-width-list').find('.delete-subtask').on("click",deleteSubtask);
    $('.interactive.add').children('.content-header').find('.add-exercise').on("click",addExercise);
    $('.body-option-color.right.deny-button.skip-list-item').on("click",addSubtask);
});

// rename exercise headers with correct enumeration 
function renumberExercises() {
    var allCollapsible = $('.collapsible').children('.content-header');

    for (var i = 1; i < allCollapsible.length; i++) {
        jQuery(allCollapsible[i]).children('.content-title')[0].innerText = "Aufgabe " + i;
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

    var subtaskCount = trig.parent().parent().find('li').not( ".skip-item" ).length;

    //hide delete-subtask link if there were 2 subtasks before deleting
    if (subtaskCount == 2) {
        // first not deleted Subtask
        var firstSubtask = trig.parent().parent().find('li').not( ".skip-item" ).not(trig.parent()).first();

        firstSubtask.find('.delete-subtask').fadeOut('fast');
    }
}


// adds a new exercise when clicking on a link with the class 'add-exercise'
// at the end of the page
function addExercise(event) {

    // append content to last exercise
    $.get("include/CreateSheet/ExerciseSettings.template.html", function (data) {
        $("#content-wrapper").append(data);
        
        // animate new element
        $('.collapsible').last().hide().fadeIn('fast');

        // map click events on new exercise
        $('.collapsible').last().children('.content-header').find('a').on("click",suppressPropagation);
        $('.collapsible').last().children('.content-header').find('.delete-exercise').on("click",deleteExercise);
        $('.full-width-list').last().find('.delete-subtask').on("click",deleteSubtask);
        $('.collapsible').last().children('.content-header').on("click",collapseElement);
        $('.body-option-color.right.deny-button.skip-list-item').last().on("click",addSubtask);

        // set mouse curser on mouse-over to pointer
        $('.collapsible').last().children('.content-header').css('cursor','pointer');

        renumberExercises();
    });
}


// adds new subtask when clicking on a link with the class '.body-option-color.right.deny-button.skip-list-item'
function addSubtask(event) {
    var trig = $(this);

    // insert subtask
    $.get("include/CreateSheet/Subtask.template.html", function (data) {
        trig.parent().before(data);

        var insertedSubtask = trig.parent().parent().find('li').not( ".skip-item" ).last();
        var subtaskCount = trig.parent().parent().find('li').not( ".skip-item" ).length;

        // animate new element
        insertedSubtask.hide().fadeIn('fast');

        //show delete-subtask link if there are 2 subtasks
        if (subtaskCount == 2) {
            var firstSubtask = trig.parent().parent().find('li').not( ".skip-item" ).first();
            firstSubtask.find('.delete-subtask').fadeIn('fast');
        }

        // map click events on new subtask
        insertedSubtask.find('.delete-subtask').on("click",deleteSubtask);
    });
}