$(document).ready( function() {
    $('.interactive').click(function() {
        var trig = $(this);
        var target = trig.find('#target')[0];
        window.location.href = target.attributes.href.value;
    });

    $('.interactive').children('.content-header').css('cursor','pointer');
});