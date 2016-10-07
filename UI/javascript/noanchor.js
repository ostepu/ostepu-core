/**
 * Die Klasse noanchor verhindert, dass ein Anker beim Aufruf der Schaltfläche übergeben wird
 * Dazu wird die URL des Formulars angepasst
 */
$(document).ready( function() {
    $('.noanchor').click(function(event ) {
        var trig = $(this);
        var formParent = trig.closest('form');
        formParent.attr('action',trig.closest('form').attr('action').replace(/#[^#]*$/, ""));
    });
});