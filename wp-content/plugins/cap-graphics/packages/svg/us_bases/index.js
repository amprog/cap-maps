jQuery(document).ready(function($) {

    $.getJSON( "/wp-content/plugins/cap-graphics/packages/svg/us_bases/index.json")
        .done(function( data ) {
            el  = data;
            pre = $('.svg_pre');
            pre.html( $('<h3>'+data.elections[0].name+'</h3><p>'+data.elections[0].description+'</p><div class="y">'));

            //iterate through all years and write to page  console.log(value);
            $.each( data.elections, function( i, value ) {
                $('.y').append( $( '<a href="javascript:void(0);" data-i="'+i+'" class="'+value.winner+'">'+value.year+'</a>' ) );
            });

            pre.append( $('</div>'));

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