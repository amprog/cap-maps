jQuery(document).ready(function($) {

    //TODO: this represents a problem, perhaps use php as js. needs to get automatically from media library, or local
    $.getJSON( "/wp-content/plugins/cap-graphics/packages/svg/electoral/index.json")
        .done(function( data ) {
            el   = data;
            pre  = $('.svg_pre');
            meta = $('.svg_post_meta');
            post = $('.svg_post');

            pre.html( $('<h3>'+data.elections[0].name+'</h3><p>'+data.elections[0].description+'</p><div class="y">'));

            //iterate through all years and write to page  console.log(value);
            $.each( data.elections, function( i, value ) {
                if(i==0) {
                    $('.y').append( $( '<a href="javascript:void(0);" data-i="'+i+'" class="'+value.winner+' active">'+value.year+'</a>' ) );
                } else {
                    $('.y').append( $( '<a href="javascript:void(0);" data-i="'+i+'" class="'+value.winner+'">'+value.year+'</a>' ) );
                }

            });

            pre.append( $('</div>'));

            //only apply colors to 1st entry when user first lands here
            $.each( data.elections[0].data, function( k, v ) {
                $('path[id^="'+v.abbreviation+'"').attr("class", v.color); //jquery can't add class to svg path
            });

            meta.append('<h3>Electoral College Votes</h3><p>270 needed to win</p>');


            //create animated horizontal bar
            var total_electoral = 0;
            $.each( data.elections[0].parties[0], function( k, v ) {
                //electoral bars first or electoral with stripes
                post.append('<div class="hbar '+k+'"><span>'+ v.electoral+'</span><p>'+ v.popular+'</p></div>');
                total_electoral += parseInt(v.electoral);
            });

            //calculate percentages
            var r = $('.hbar.republicans');
            var d = $('.hbar.democrats');

            r.css("width",(data.elections[0].parties[0].republicans.electoral/total_electoral) * 100+'%');
            d.css("width",(data.elections[0].parties[0].democrats.electoral/total_electoral) * 100+'%');

        })
        .fail(function( jqxhr, textStatus, error ) {
            var err = textStatus + ", " + error;
            console.log( "Request Failed: " + err );
        });

    $( ".y a" ).live( "click", function() {
        $( ".y a" ).removeClass('active');
        var i = $(this).data('i');

        $.each( el.elections[i].data, function( key, v ) {
            $('path[id^="'+v.abbreviation+'"').attr("class", v.color); //jquery can't add class to svg path

        });
        post.html('');
        var total_electoral = 0;
        $.each( el.elections[i].parties[0], function( k, v ) {
            //electoral bars first or electoral with stripes
            post.append('<div class="hbar '+k+'">'+ v.electoral+'</div>');
            total_electoral += parseInt(v.electoral);
        });


        $('.hbar.republicans').css("width",(el.elections[i].parties[0].republicans.electoral/total_electoral) * 100+'%');
        $('.hbar.democrats').css("width",(el.elections[i].parties[0].democrats.electoral/total_electoral) * 100+'%');
        $( this).addClass('active');
    });
});