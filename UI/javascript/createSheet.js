$(document).ready( function() {
    // rename exercise headers with correct enumeration 
    function renameExercises() {
        var allCollapsible = $('.collapsible').children('.content-header');

        for (var i = 1; i < allCollapsible.length; i++) {
            jQuery(allCollapsible[i]).children('.content-title')[0].innerText = "Aufgabe " + i;
        }
    }

    // if the content header contains an anchor tag prevent that clicking on it
    // will trigger the content element to collapse
    function stopProp(event) {
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

            renameExercises();
        });
    }


    // deletes the subtask and its related list-element when clicking on
    // a link with the class 'delete-subtask'
    function deleteSubtask(event) {
        $(this).parent().remove();
    }


    // adds a new exercise when clicking on a link with the class 'add-exercise'
    // at the end of the page
    function addExercise(event) {

        // append content to last exercise
        $.get("include/CreateSheet/ExerciseSettings.template.html", function (data) {
            $("#content-wrapper").append(data);
            
            // map click events on new exercise
            $('.collapsible').last().hide().fadeIn(1000);
            $('.collapsible').last().children('.content-header').find('a').on("click",stopProp);
            $('.collapsible').last().children('.content-header').find('.delete-exercise').on("click",deleteExercise);
            $('.full-width-list').last().find('.delete-subtask').on("click",deleteSubtask);
            $('.collapsible').last().children('.content-header').on("click",collapseElement);

            // set mouse curser on mouse-over to pointer
            $('.collapsible').last().children('.content-header').css('cursor','pointer');

            renameExercises();
        });
    }

    // map click events
    $('.collapsible').children('.content-header').find('a').on("click",stopProp);
    $('.collapsible').children('.content-header').find('.delete-exercise').on("click",deleteExercise);
    $('.full-width-list').find('.delete-subtask').on("click",deleteSubtask);
    $('.interactive.add').children('.content-header').find('.add-exercise').on("click",addExercise);
});