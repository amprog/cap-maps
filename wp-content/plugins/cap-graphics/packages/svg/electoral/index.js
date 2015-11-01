jQuery(document).ready(function($) {

    //TODO: this represents a problem, perhaps use php as js. needs to get automatically from media library, or local
    $.getJSON( "/wp-content/plugins/cap-graphics/packages/svg/electoral/index.json")
        .done(function( data ) {
            el = data;
            $('.svg_meta').before( $('<h3>'+data.elections[0].name+'</h3><p>'+data.elections[0].description+'</p>'));

            //iterate through all years and write to page
            $.each( data.elections, function( i, value ) {console.log(value);
                $('.svg_meta').before( $( '<a class="y" href="javascript:void(0);" data-i="'+i+'">'+value.year+'</a>' ) );
            });

            //only apply colors to 1st entry when user first lands here
            $.each( data.elections[0].data, function( k, v ) {
                $('path[id^="'+v.abbreviation+'"').attr("class", v.color); //jquery can't add class to svg path
                //$('#'+ v.abbreviation+'_1').css('content',v.number);  //not working
                //t = s.text(100, 500, [v.number]); //snap requires exact x y coordinates
                //console.log(t);
            });
        })
        .fail(function( jqxhr, textStatus, error ) {
            var err = textStatus + ", " + error;
            console.log( "Request Failed: " + err );
        });


    $( ".y" ).live( "click", function( event ) {
        var i = $(this).data('i');
        $.each( el.elections[i].data, function( key, v ) {
            $('path[id^="'+v.abbreviation+'"').attr("class", v.color); //jquery can't add class to svg path
        });
    });
});