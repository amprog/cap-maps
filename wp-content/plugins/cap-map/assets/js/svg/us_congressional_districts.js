jQuery(document).ready(function($) {

    //$('.svg_wrap').height($(window).height()-300);

    //get snap script
    //$.getScript( "/wp-content/plugins/cap-map/assets/js/common/snap.min.js" )
        //.done(function( script, textStatus ) {
            //console.log( textStatus );
            //s = Snap('#us_congressional_districts');

            $.getJSON( "/wp-content/plugins/cap-map/data/us_congressional_districts.json")
                .done(function( data ) {
                    el = data;
                    $('.svg_meta').before( $('<h3>'+data.elections[0].name+'</h3><p>'+data.elections[0].description+'</p>'));

                    //iterate through all years and write to page
                    $.each( data.elections, function( i, value ) {
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
        //})

        //.fail(function( jqxhr, settings, exception ) {
        //    $( "div.log" ).text( "Triggered ajaxError handler." );
        //});

});