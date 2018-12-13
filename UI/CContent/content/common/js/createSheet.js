/**
 * @file createSheet.js
 * Contains Javascript code that is needed on the CreateSheet page.
 */
 
$(document).ready( function() 
{

    // set mouse cursor on mouse-over to pointer
    $('.collapsible').children('.content-header').css('cursor','pointer');

    // map click events
    $('.collapsible').children('.content-header').on("click",collapseElement);

    $.fn.datetimepicker.dates['de'] = {
        days: ["Sontag","Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag", "Sonntag"],
        daysShort: ["So","Mo", "Di", "Mi", "Do", "Fr", "Sa", "So"],
        daysMin: ["So","Mo", "Di", "Mi", "Do", "Fr", "Sa", "So"],
        months: ["Januar", "Februar", "März", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember"],
        monthsShort: ["Jan", "Feb", "Mär", "Apr", "Mai", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Dez"],
        today: "Heute"
    };

    $('#datetimepicker1').datetimepicker({
      language: 'de',
      pick12HourFormat: false,
      pickSeconds: false,
      weekStart: 1
    });

    $('#datetimepicker2').datetimepicker({
      language: 'de',
      pick12HourFormat: false,
      pickSeconds: false,
      weekStart: 1
    });

    setCurrentTimeData();
    
    // suppress Propagation
    $('.collapsible').children('.content-header').find('a').on("click",suppressPropagation);
    $('.interactive.add').children('.content-header').find('a').on("click",suppressPropagation);

    // map click events
    $('.collapsible').children('.content-header').find('.delete-exercise').on("click",deleteExercise);
    $('.full-width-list').find('.delete-subtask').on("click",deleteSubtask);
    $('.interactive.add').children('.content-header').find('.add-exercise').on("click",addExercise);
    
    $('.deleteFile').on("click",deleteFile);

    $('#submitSheet').on("click", function(event) {
        $('#submitSheetButton').click();
    });
    
    $('.body-option-color.right.deny-button.skip-list-item').on("click",addSubtask);

    
    $('.form').children('.content-header').on("click",collapseForm);     
    $('.form').children('.content-header').css('cursor','pointer');   
    $('.form').children('.content-header').find('.delete-form').on("click",deleteForm);
    
    $('.form-input-radio').parent().find('.add-choice').on("click",addRadio);
    $('.form-input-radio').find('.delete-choice').on("click",removeRadio);
    
    $('.form-input-checkbox').parent().find('.add-choice').on("click",addCheckbox);
    $('.form-input-checkbox').find('.delete-choice').on("click",removeCheckbox);
    
    var all2 = $('.use-form');
    for (var i = 0; i < all2.length; i++) {
        var target = $(all2[i]);
        if (target.parent().find('.delete-form').length>0){
            target.hide().fadeOut('fast');
            target.parents('li').find('.mime-field').last().attr("disabled", "disabled"); 
            var all3 = target.parent().find('.delete-choice');
            if (all3.length == 1) {
                for (var b = 0; b < all3.length; b++) {
                    var choice = $(all3[b]);
                    choice.fadeOut('fast');
                }
            }
        }
    }

    
    $('.use-form').on("click",useForm);
    $('.use-processor').on("click",useProcessor);
    
    $('.processor').children('.content-header').on("click",collapseProcessor);     
    $('.processor').children('.content-header').css('cursor','pointer');   
    $('.processor').children('.content-header').find('.delete-processor').on("click",deleteProcessor);
        
    if ($('.delete-exercise').length==0)
        $('.add-exercise').click();
        
   /* $('.processor').children('.content-header').on("click",collapseProcessor);     
    $('.processor').children('.content-header').css('cursor','pointer');   
    $('.processor').children('.content-header').find('.delete-processor').on("click",deleteProcessor);*/

    $('.processor-type').on("change",loadProcessorTemplate);
    ///$('.processor-type').change();
    
    renumberExercises();
    renameProcessor();
    rename();
    // activate ckeditor
    var all = $('.ckeditor');
    for (var i = 0; i < all.length; i++) {
        var oldName = $(all[i]).attr('name');
        CKEDITOR.inline( oldName );
    }
    
    processorsReady();
});

function deleteFile(event) 
{
    var trig = $(this);
    var container = trig.parents('.divFile');
    var button = container.parent('.fileArea');
    container.slideToggle('fast', function() {
        container[0].parentNode.removeChild(container[0]);
        //button.find('.fileButton').slideToggle('fast');
        button.find('.fileButton').hide().fadeIn('fast');
    });
}

/**
 * sets the current time
 */
function setCurrentTimeData() 
{
    var picker = $('#datetimepicker1').data('datetimepicker');
    var localDate = picker.getLocalDate();

    if (!$('#startDate').val()) {
        picker.setLocalDate(new Date(localDate.getYear()+1900, localDate.getMonth(), localDate.getDate(), 0, 0));
    }

    var picker = $('#datetimepicker2').data('datetimepicker');
    var localDate = picker.getLocalDate();

    if (!$('#endDate').val()) {
        picker.setLocalDate(new Date(localDate.getYear()+1900, localDate.getMonth(), localDate.getDate()+7, 23, 59));
    }
} 

/**
 * Renames page elements.
 *
 * Renames exercise headers with correct enumeration.
 * Renames input elements.
 */
function renumberExercises() 
{
    var allCollapsible = $('.collapsible');

    for (var i = 1; i < allCollapsible.length; i++) {
        // add a new header text
        var current = $(allCollapsible[i]);
        //current.children('.content-header').children('.content-title').first().innerText = "Aufgabe " + i;
        current.children('.content-header').children('.content-title').first().text( "Aufgabe " + i);
        
        //rename the input element
        var listElements = current.find('li');
        listElements.each(renameSubtask(i));

    }
}

/**
 * Return a closure that can replace numbers in names of form elements.
 */
function renameSubtask(i) 
{
    // return a closure to rename inputs in subtasks
    return function (index, listElement) {
        var element = $(listElement);
        var inputs = element.find('input, select, textarea');
        inputs.each(function(idx, el) {
            var elem = $(el);
            // get the old name
            var oldName = elem.attr('name');

            if (oldName != null){
       
                var regex = /exercises\[[0-9]+\]\[.+?\]\[[0-9]+\]\[(.+?)]/gm;
                var nameString = "exercises[" + (i - 1) + "][subexercises][" + index + "][$1]";

                // match the regex and replace the numbers
                var newName = oldName.replace(regex, nameString);

                // set the new name
                elem.attr('name', newName);
            }
        });
    };
}

/**
 * prevent that an event is propagated up the responder chain.
 */
function suppressPropagation(event)
{
    event.stopPropagation();

    return false;
}


/**
 * Delete an exercise from the page.
 *
 * Deletes the exercise and its related content-element when clicking on
 * a link with the class 'delete-exercise'.
 */
function deleteExercise(event) 
{

    var trig = $(this);
    var container = trig.parents('.collapsible');
    container.slideToggle('fast', function() {
        container[0].parentNode.removeChild(container[0]);

        renumberExercises();
    });
}


/**
 * Deletes a subtask from an exercise.
 *
 * Deletes the subtask and its related list-element when clicking on
 * a link with the class 'delete-subtask'.
 */
function deleteSubtask(event) 
{
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
    renumberExercises();
}

/**
 * Adds a new exercise to the page.
 *
 * adds a new exercise when clicking on a link with the class 'add-exercise'
 * at the end of the page.
 */
function addExercise(event) 
{

    // append content to last exercise
    $.get("include/CreateSheet/ExerciseSettings.template.php", function (data) {

        var collapsible = $(".collapsible");
       // if (collapsible.length == 1) {
            $(".add").last().before(data);
        //} else {
        //    $(".collapsible").last().before(data);
        //}

        // animate new element
        $('.collapsible').last().hide().fadeIn('fast');

        // map click events on new exercise
        $('.collapsible').last().children('.content-header').find('a').on("click",suppressPropagation);
        $('.collapsible').last().children('.content-header').find('.delete-exercise').on("click",deleteExercise);
        $('.full-width-list').last().find('.delete-subtask').on("click",deleteSubtask);
        

        
        $('.collapsible').last().children('.content-header').on("click",collapseElement);
       
        $('.body-option-color.right.deny-button.skip-list-item').last().on("click",addSubtask);
        $('.body-option-color.right.deny-button.skip-list-item').last().click();

        // set mouse curser on mouse-over to pointer
        $('.collapsible').last().children('.content-header').css('cursor','pointer');
        renumberExercises();
    });
    
}

/**
 * Adds a new subtask to an exercise
 *
 * Adds new subtask when clicking on a link with the class
 * '.body-option-color.right.deny-button.skip-list-item'
 */
function addSubtask(event) 
{
    var trig = $(this);

    // insert subtask
    $.get("include/CreateSheet/Subtask.template.php", function (data) {
        trig.parent().before(data);

        var insertedSubtask = trig.parent().parent().find('li').not( ".skip-item" ).last();
        var subtaskCount = trig.parent().parent().find('li').not( ".skip-item" ).length;

        // animate new element
        insertedSubtask.hide().fadeIn('fast');

        //show delete-subtask link if there are 2 subtasks
        if (subtaskCount >= 2) {
            var firstSubtask = trig.parent().parent().find('li').not( ".skip-item" ).first();
            firstSubtask.find('.delete-subtask').fadeIn('fast');
        }
        else{
            var firstSubtask = trig.parent().parent().find('li').not( ".skip-item" ).first();
            firstSubtask.find('.delete-subtask').fadeOut('fast');
        }
            
        // map click events on new subtask
        insertedSubtask.find('.delete-subtask').on("click",deleteSubtask);
        insertedSubtask.find('.use-form').on("click",useForm);
        insertedSubtask.find('.use-processor').on("click",useProcessor);
        
        renumberExercises();
    });
}

/**
 * toggle function on click to hide/show .content-header elements
 */
function collapseElement(event) 
{
    // trig = event sender
    var trig = $(this);
    // toggle the next available element of .content-body-wrapper near the "trig" with duration "fast"
    if (trig.parent('.collapsible').length !== 0) {
        trig.parent().children('.content-body-wrapper, .content-footer').first().slideToggle('fast');
        trig.toggleClass( 'inactive',  !trig.hasClass('inactive') );
    }
}

