/**
 * @file createSheet.js
 * Contains Javascript code that is needed on the CreateSheet page.
 */
 
$(document).ready( function() 
{
    $.fn.datetimepicker.dates['de'] = {
        days: ["Sontag","Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag", "Sonntag"],
        daysShort: ["So","Mo", "Di", "Mi", "Do", "Fr", "Sa", "So"],
        daysMin: ["So","Mo", "Di", "Mi", "Do", "Fr", "Sa", "So"],
        months: ["Januar", "Februar", "März", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember"],
        monthsShort: ["Jan", "Feb", "Mär", "Apr", "Mai", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Dez"],
        today: "Heute"
    };
        
    var all2 = $('.dtpicker');
    for (var i = 0; i < all2.length; i++) {
        var target = $(all2[i]);
        target.datetimepicker({
          language: 'de',
          pick12HourFormat: false,
          pickSeconds: false,
          weekStart: 1
        });
    } 
    
    var all3 = $('.dtpickerInit');
    for (var i = 0; i < all3.length; i++) {
        var target = $(all3[i]);
        var picker = target.data('datetimepicker');
        var localDate = picker.getLocalDate();
        if (!target.find('.dtDate').val()) {
            picker.setLocalDate(new Date(localDate.getYear()+1900, localDate.getMonth(), localDate.getDate(), 0, 0));
        }
    }
});