$(document).ready( function() 
{
    var all2 = $('.download');
    for (var i = 0; i < all2.length; i++) {
        var target = $(all2[i]);
        target.on("click",download);
        target.attr('tag',target.attr('href'));
        target.attr('href','javascript:void(0)');
        target.attr('col',target.css('color'));
        target.attr('tex',target.text());
    }
});

function download(event) 
{   event.isPropagationStopped();
    var trig = $(this);
    var url = trig.attr('tag');
    
    trig.text('Bitte warten...');
    trig.css('color','#f4ad32');
    $.getJSON(url, function (data) {
        if (data.address===undefined){
            fail(trig);
        } else {
            if (data.fileSize===undefined){
                trig.css('color','#DE3838');
                trig.text('Kein Inhalt...');
                setTimeout(function() {
                    trig.text(trig.attr('tex'));
                    trig.css('color',trig.attr('col'));
                }, 2000);
            } else {
                window.location.href = "../FS/FSBinder/"+data.address+"/"+data.displayName;
                trig.css('color','#2DB22D');
                trig.text('Weiterleitung...');
                setTimeout(function() {
                    trig.text(trig.attr('tex'));
                    trig.css('color',trig.attr('col'));
                }, 2000);
            }
        }
    })
    .fail(function() {
        fail(trig);
    });
}

function fail(trig)
{
    trig.text('Fehler');
    trig.css('color','#DE3838');
    setTimeout(function() {
        trig.text(trig.attr('tex'));
        trig.css('color',trig.attr('col'));
    }, 2500);
}