/**
 * @file createSheet.js
 * Contains Javascript code that is needed on the CreateSheet page.
 */
 
$(document).ready( function() 
{
    $.fn.datetimepicker.dates['de'] = {
        days: ["Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag", "Sonntag", "Montag"],
        daysShort: ["Mo", "Di", "Mi", "Do", "Fr", "Sa", "So", "Mo"],
        daysMin: ["Mo", "Di", "Mi", "Do", "Fr", "Sa", "So", "Mo"],
        months: ["Januar", "Februar", "März", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember"],
        monthsShort: ["Jan", "Feb", "Mär", "Apr", "Mai", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Dez"],
        today: "Heute"
    };
    
    var all2 = $('.datetimepicker');
    for (var i = 0; i < all2.length; i++) {
        var target = $(all2[i]);
        target.datetimepicker({
          language: 'de',
          pick12HourFormat: false,
          pickSeconds: false,
        });
        
        var picker = target.data('datetimepicker');
        var localDate = picker.getLocalDate();

        if (!target.child('Date').val()) {
            picker.setLocalDate(new Date(localDate.getYear()+1900, localDate.getMonth(), localDate.getDate(), 0, 0));
        }
    }
});