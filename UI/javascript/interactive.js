$(document).ready( function() {
    $('.interactive').click(function() {
        var trig = $(this);
        var target = trig.find('#target')[0];
        console.log(target);
        // if target is not a link but a function only "button"
        if (!target) {
        	target = trig.find('#function');
        	target.trigger('click');
        }
        else
        {
        	window.location.href = target.attributes.href.value;
        }
    });

    $('.interactive').children('.content-header').css('cursor','pointer');
});