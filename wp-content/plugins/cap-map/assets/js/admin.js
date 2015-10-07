jQuery(document).ready(function($) {
    $( ".graphic_select" ).change(function() {

        $( ".graphic_select option:selected" ).each(function() {
            console.log($( this ).val());

            $('.'+$( this ).val()+'_li').slideDown();
        });

    });
});