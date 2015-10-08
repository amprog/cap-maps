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




    //charting
    $( ".chart_select" ).change(function() {
        $( ".chart_select option:selected" ).each(function() {
            console.log($( this ).val());

            var data = {
                'action': 'cap_map_chart_action',
                'chart_slug': $( this ).val()
            };
            // We can also pass the url value separately from ajaxurl for front end AJAX implementations
            jQuery.post(ajaxurl, data, function(response) {
                console.dir(response);

                $('#chart_new').html(response.html);
            });
        });
    });


    /**
     * TODO: should i hook into form and validate?
     */
    $( "#chart_submit" ).live( "click", function() {

        var chart_slug = $('#chart_slug').val();
        var chart_name = $('#chart_name').val();
        var source     = $('#source').val();


        //chart_data
        var labels = $("#chart_new input[name=chart_data]");

        console.dir(labels);

        //var chart_json = $('#chart_json').val();

        //console.log(chart_json);

    });

});