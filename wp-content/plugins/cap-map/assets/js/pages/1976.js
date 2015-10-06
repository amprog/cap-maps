jQuery(document).ready(function( $ ) {


    //top export partners
    $.getJSON( "/wp-content/plugins/svg_map/data/2015/water.json")
        .done(function( json ) {
            $('#s_1_2').appear(function() {
                new Chart(document.getElementById('s_1_2').getContext('2d')).Doughnut(json.data_array[0].data,json.options);
            },{accX: 0, accY: -200});

        })
        .fail(function( jqxhr, textStatus, error ) {
            var err = textStatus + ", " + error;
            console.log( "Request Failed: " + err );
        });
});