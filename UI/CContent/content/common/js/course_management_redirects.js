$(document).ready( function() 
{
    initRedirects();
    renameRedirects();
});

/**
 * sets the current time
 */
function initRedirects() 
{
    $('.removeRedirectElement').on("click",removeRedirectElement);
    $('.addRedirectElement').on("click",addRedirectElement);
}

function removeRedirectElement(event)
{
    var trig = $(this);
    var container = trig.parents('.RedirectElement');
    container.toggle(500, function() {
    container[0].parentNode.removeChild(container[0]);
    });
}

function addRedirectElement(event)
{
    var trig = $(this);
    var templateElem = trig.parent().find('.RedirectTemplates').first().val();
    
    // insert subtask
    $.get("include/CourseManagement/CourseRedirect/Redirect.template.php?template="+templateElem, function (data) {
        trig.parent().find('.endRedirect, .RedirectElement').first().before(data);
        var elem = trig.parent().find('.RedirectElement').first();
        elem.toggle(false);
        elem.toggle(500, function() {
            elem.find('.removeRedirectElement').on("click",removeRedirectElement);
            renameRedirects();
        });
    });
}

function renameRedirects()
{
    var elements = $('.RedirectElement');
    for (var i = 0; i < elements.length; i++) {
        var target = $(elements[i]);
        var giveMeAName = target.find('.RedirectName');
        
        giveMeAName.each(function(idx, el) {
            var elem = $(el);
            // get the old name
            var oldName = elem.attr('name');

            if (oldName != null){
       
                var regex = /data\[[0-9]+\]\[(.+?)]/gm;
                var nameString = "data[" + (i) + "][$1]";

                // match the regex and replace the numbers
                var newName = oldName.replace(regex, nameString);

                // set the new name
                elem.attr('name', newName);
            }
        });
        
        var giveMeAName2 = target.find('.RedirectName2');
        
        giveMeAName2.each(function(idx, el) {
            var elem = $(el);
            // get the old name
            var oldName = elem.attr('name');

            if (oldName != null){
       
                var regex = /data\[[0-9]+\]\[(.+?)]\[]/gm;
                var nameString = "data[" + (i) + "][$1][]";

                // match the regex and replace the numbers
                var newName = oldName.replace(regex, nameString);

                // set the new name
                elem.attr('name', newName);
            }
        });
    }
}
