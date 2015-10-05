jQuery(document).ready(function( $ ) {

    $('.map_left').height($(window).height()-270);

    //get chart data from json
    $.getJSON( "/wp-content/plugins/svg_map/data/2015/1957-prep.json")
        .done(function( json ) {

            var region = $(".region" );
            //side panel on prep hover
            region.hover(
                function() {
                    $('.map_right [id^=d_prep]').switchClass("in", "out", 100, "easeInOutQuad");
                    id = $(this).attr('id');
                    poly = $('#'+id);
                    $('#d_'+id).switchClass("out", "in", 100, "easeInOutQuad");
                    svg = $('#prep_svg circle, #prep_svg text');
                    svg.hide();
                }, function() {
                    svg.show();
                }
            );

        })
        .fail(function( jqxhr, textStatus, error ) {
            var err = textStatus + ", " + error;
            console.log( "Request Failed: " + err );
        });


    //go to prep page on click
    $(".region" ).live( "click", function() {
        window.location.href= $('#d_'+$(this).attr('id')+' .read_more').attr("href");
    });

    //json of cities
    $('#prep_svg').overlays();
});