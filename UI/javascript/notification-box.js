/**
 * @file createSheet.js
 * Contains Javascript code that is needed on the CreateSheet page.
 */
 
$(document).ready( function() 
{
    $('.notification-box').on("click",growNotificationBox);
    $('.notification-box').on("focusout",shrinkNotificationBox);
    
    
    var lists = $('.notification-list');
    for (var i = 0; i < lists.length; i++) {
        var elem = $(lists[i]);

        var notifications = elem.find('.notification-box');
        var countNotifications = notifications.length;
        if (countNotifications>5){
            // die anderen Elemente sollen versteckt werden
            for (var b = 5; b < notifications.length; b++) {
                var hideMe = $(notifications[b]);
                hideMe.hide();
            }
            
            //pfeil einblenden
            var newElem = '<div style="height:48px;overflow:hidden;display:inline-block;"><a href="javascript:void(0);" class="growList image-button"><img class="arrowImage" style="margin-bottom:-50%;" src="Images/Arrow.png"></a></div>';
            notifications.last().after(newElem);
            
            elem.find('.growList').on("click",growNotificationList);
        }
    }
});

function growNotificationBox(event)
{
    var trig = $(this);
    if (trig.css('width') != '748px'){
        trig.attr('min-height', trig.css('height'));
        trig.attr('min-width', trig.css('width'));
        //trig.css('width', '748px');
        trig.animate({width: '748px'}, 500, 'linear', function() {     
            trig.animate({height: (trig[0].scrollHeight)+"px"}, 500).delay(750);   
        });    
    }
}

function growNotificationList(event)
{
    var trig = $(this);
    var parent = trig.parents('.notification-list');
    var notifications = parent.find('.notification-box');
    trig.find('.arrowImage').hide();
    
    // die anderen Elemente sollen versteckt werden
    var state = false;
    for (var b = 5; b < notifications.length; b++) {
        var showMe = $(notifications[b]);
        if(showMe.css('display') !== 'none') {
            state = true;
        }
        showMe.toggle(750);
    }
    
    if (state === true){
        // schließen
        trig.find('.arrowImage').css('margin-top','0px'); 
        trig.find('.arrowImage').css('margin-bottom','-200%');
    } else {
        // öffnen
        trig.find('.arrowImage').css('margin-bottom','0px');  
        trig.find('.arrowImage').css('margin-top','-200%');     
    }
    trig.find('.arrowImage').fadeIn(750);
}

function shrinkNotificationBox(event)
{
    var trig = $(this);
    if (trig.css('width') == '748px'){
        trig.animate({
           height: trig.attr('min-height')
        }, 500, 'linear', function() {
            trig.animate({width: trig.attr('min-width')}, 500, 'linear').delay(750);   
        }); 
    }
}