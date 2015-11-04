jQuery(document).ready(function($) {

    $.getJSON( "/wp-content/plugins/cap-graphics/packages/svg/us_bases/index.json")
        .done(function( data ) {
            
        })
        .fail(function( jqxhr, textStatus, error ) {
            var err = textStatus + ", " + error;
            console.log( "Request Failed: " + err );
        });


    $( ".y" ).live( "click", function() {


    });
});