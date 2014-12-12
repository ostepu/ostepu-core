$(document).ready( function() 
{
    $('.selectAll').on("click",selectAll);
    $('.selectNothing').on("click",selectNothing);
    $('.selectAllProposals').on("click",selectAllProposals);
});

function selectAll(event) 
{
    var trig = $(this);  
    var all2 = trig.parent().find('input');
    //alert(all2.length);
    for (var i = 0; i < all2.length; i++) {
        var target = $(all2[i]);
        target.prop('checked',true);
    }
}

function selectAllProposals(event) 
{
    var trig = $(this);  
    var all2 = trig.parent().find('input');
    //alert(all2.length);
    for (var i = 0; i < all2.length; i++) {
        var target = $(all2[i]);
        target.prop('checked',false);
    }
    
    var all2 = trig.parent().find('.checkProposal');
    for (var i = 0; i < all2.length; i++) {
        var target = $(all2[i]);
        target.prop('checked',true);
    }
}

function selectNothing(event) 
{
    var trig = $(this);  
    var all2 = trig.parent().find('input');
    //alert(all2.length);
    for (var i = 0; i < all2.length; i++) {
        var target = $(all2[i]);
        target.prop('checked',false);
    }
}