function deleteForm(event) {
    var trig = $(this);
    var container = trig.parents('.form');
    trig.parents('.form').parent().find('.use-form').hide().fadeIn('fast');
    
    trig.parents('.form').parents('.content-body').find('.mime-field').removeAttr("disabled");    
    
    container.slideToggle('fast', function() {
    container[0].parentNode.removeChild(container[0]);

    });
}

function useForm(event) {
var trig = $(this);

    // append content to last exercise
    $.get("include/CreateSheet/Form/FormSettings.template.php", function (data) {
    
        trig.parents('.content-body').find('.mime-field').first().attr("disabled", "disabled");   

        trig.after(data);
        trig.hide().fadeOut('fast');

        // animate new element
        trig.parent().find('.form').first().hide().fadeIn('fast');
        
        trig.parent().find('.form').first().children('.content-header').on("click",collapseForm);     
        trig.parent().find('.form').first().children('.content-header').css('cursor','pointer');   
        trig.parent().find('.form').first().children('.content-header').find('.delete-form').on("click",deleteForm);

        trig.parent().find('.form').first().find('.use-input').first().on("click",useInput);
        trig.parent().find('.form').first().find('.use-radio').first().on("click",useRadio);
        trig.parent().find('.form').first().find('.use-checkbox').first().on("click",useCheckbox);
    });
}

function useInput(event) {
var trig = $(this);

trig.parents('.form').last().find('.content-title').first().text("Eingabezeile");
    // append content
    $.get("include/CreateSheet/Form/FormInput.template.php", function (data) {

        var parent = trig.parent();

        trig.parent().find('.use-checkbox').first().remove();
        trig.parent().find('.use-radio').first().remove();
        trig.parent().find('.use-input').first().remove();
        parent.after(data);
        renameInput(parent.parent());
        
    });
}

function rename(){
    var all = $('.input-choice');

    for (var i = 0; i < all.length; i++) {
        // add a new header text
        var elem = $(all[i]);
        var oldName = elem.attr('name');

            var regex = /exercises\[(.+?)]\[.+?\]\[(.+?)]\[(.+?)]\[[0-9]+\]/gm;
            var nameString = "exercises[$1][subexercises][$2][$3]["+ (i) +"]";

            // match the regex and replace the numbers
            var newName = oldName.replace(regex, nameString);

            // set the new name
            elem.attr('name', newName);
    }
    
    var all2 = $('.input-choice-text');

    for (var i = 0; i < all2.length; i++) {
        // add a new header text
        var elem = $(all2[i]);
        var oldName = elem.attr('name');

            var regex = /exercises\[(.+?)]\[.+?\]\[(.+?)]\[(.+?)]\[[0-9]+\]/gm;
            var nameString = "exercises[$1][subexercises][$2][$3]["+ (i) +"]";

            // match the regex and replace the numbers
            var newName = oldName.replace(regex, nameString);

            // set the new name
            elem.attr('name', newName);
    }
}

function renameInput(trig) {
    renumberExercises();
    rename();
}

function useRadio(event) {
var trig = $(this);
trig.parents('.form').last().find('.content-title').first().text("Einfachauswahl");
    // append content
    $.get("include/CreateSheet/Form/FormRadio.template.php", function (data) {

        var parent = trig.parent();
        
        trig.parent().find('.use-input').first().remove();
        trig.parent().find('.use-checkbox').first().remove();
        trig.parent().find('.use-radio').first().remove();
        parent.after(data);

        parent.parent().find('.add-choice').first().on("click",addRadio);
        parent.parent().find('.add-choice').first().click();
    });
}

function addRadio(event){
var trig = $(this);
    $.get("include/CreateSheet/Form/FormAddRadio.template.php", function (data) {
    
        trig.before(data);
        renameRadio(trig.parent());
        
        if (trig.parent().find('.delete-choice').length >= 2) {
            trig.parent().find('.delete-choice').first().fadeIn('fast');
        }
        else{
            trig.parent().find('.delete-choice').first().fadeOut('fast');
        }
        
        trig.parent().find('.delete-choice').last().on("click",removeRadio);

    });
}

function removeRadio(event){
var trig = $(this);
    
trig.parent().slideToggle('fast', function() {
        trig.parent().remove();
    });

if (trig.parent().parent().find('.form-input-radio').length == 2) {
            trig.parent().parent().find('.form-input-radio').not(trig.parent()).first().find('.delete-choice').fadeOut('fast');
        }
}

function renameRadio(trig) {
    renumberExercises();
    rename();
}

function useCheckbox(event) {
var trig = $(this);
trig.parents('.form').last().find('.content-title').first().text("Mehrfachauswahl");
    // append content
    $.get("include/CreateSheet/Form/FormCheckbox.template.php", function (data) {

        var parent = trig.parent();
        
        trig.parent().find('.use-input').first().remove();
        trig.parent().find('.use-radio').first().remove();
        trig.parent().find('.use-checkbox').first().remove();
        parent.after(data);

        parent.parent().find('.add-choice').first().on("click",addCheckbox);
        parent.parent().find('.add-choice').first().click();
    });
}

function addCheckbox(event){
var trig = $(this);
    $.get("include/CreateSheet/Form/FormAddCheckbox.template.php", function (data) {
    
        trig.before(data);
        renameRadio(trig.parent());
        
        if (trig.parent().find('.delete-choice').length >= 2) {
            trig.parent().find('.delete-choice').first().fadeIn('fast');
        }
        else{
            trig.parent().find('.delete-choice').first().fadeOut('fast');
        }
        
        trig.parent().find('.delete-choice').last().on("click",removeCheckbox);

    });
}

function renameCheckbox(trig) {
    renumberExercises();
    rename();
}

/**
 * toggle function on click to hide/show .content-header elements
 */
function collapseForm(event) {
    // trig = event sender
    var trig = $(this);
    // toggle the next available element of .content-body-wrapper near the "trig" with duration "fast"
    if (trig.parent('.form').length !== 0) {
        trig.parent().children('.content-body-wrapper, .content-footer').first().slideToggle('fast');
        trig.toggleClass( 'inactive',  !trig.hasClass('inactive') );
    }
}