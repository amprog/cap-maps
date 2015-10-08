jQuery(document).ready(function($) {

    /*
    $( ".graphic_select" ).change(function() {
        $( ".graphic_select option:selected" ).each(function() {
            $('.'+$( this ).val()+'_li').slideDown();
        });
    });
    */



    $( ".create" ).live( "click", function() {

        var type = $(this).data('type');
        console.log(type);
        $('#'+type).slideDown();




    });

    /**
     * Call AJAX to create a json file from input
     */
    $( "#chart_submit" ).live( "click", function() {

        var chart_name = $('#chart_name').val();
        var chart_json = $('#chart_json').val();

        console.log(chart_json);

    });


    //charting
    $( ".chart_select" ).change(function() {
        $( ".chart_select option:selected" ).each(function() {
            console.log($( this ).val());
        });
    });

});