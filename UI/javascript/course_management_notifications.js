/**
 * @file createSheet.js
 * Contains Javascript code that is needed on the CreateSheet page.
 */
 
$(document).ready( function() 
{
    initNotifications();
    renameNotifications();
});

/**
 * sets the current time
 */
function initNotifications() 
{
    $('.removeNotificationElement').on("click",removeNotificationElement);
    $('.addNotificationElement').on("click",addNotificationElement);
}

function removeNotificationElement(event)
{
    var trig = $(this);
    var container = trig.parents('.notificationElement');
    container.toggle(500, function() {
    container[0].parentNode.removeChild(container[0]);
    });
}

function addNotificationElement(event)
{
    var trig = $(this);

    // insert subtask
    $.get("include/CourseManagement/CourseNotification/Notification.template.php", function (data) {
        trig.parent().find('.endNotification, .notificationElement').first().before(data);
        var elem = trig.parent().find('.notificationElement').first();
        elem.toggle(false);
        elem.toggle(500, function() {
            elem.find('.removeNotificationElement').on("click",removeNotificationElement);
            
            var all2 = elem.find('.dtpicker');
            for (var i = 0; i < all2.length; i++) {
                var target = $(all2[i]);
                target.datetimepicker({
                  language: 'de',
                  pick12HourFormat: false,
                  pickSeconds: false,
                  weekStart: 1
                });
            }
                
            var all3 = elem.find('.dtpickerInit');
            for (var i = 0; i < all3.length; i++) {
                var target = $(all3[i]);
                var picker = target.data('datetimepicker');
                var localDate = picker.getLocalDate();
                if (!target.find('.dtDate').val()) {
                    picker.setLocalDate(new Date(localDate.getYear()+1900, localDate.getMonth(), localDate.getDate(), 0, 0));
                }
            }
            
            renameNotifications();
        });
    });
}

function renameNotifications()
{
    var elements = $('.notificationElement');
    for (var i = 0; i < elements.length; i++) {
        var target = $(elements[i]);
        var giveMeAName = target.find('.notificationName');
        
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
        
        var giveMeAName2 = target.find('.notificationName2');
        
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
