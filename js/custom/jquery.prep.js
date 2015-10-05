(function( $ ) {

    //not working and not necessary
    // Create plugin
    $.fn.prep = function(el) {

        var $bubble,
            $body = $('body'),
            $el;

        //get chart data from json
        $.getJSON( "/wp-content/plugins/svg_map/data/1957-prep.json")
            .done(function( json ) {

                prep_charts = json;
                //console.dir(prep_charts);
            })
            .fail(function( jqxhr, textStatus, error ) {
                var err = textStatus + ", " + error;
                console.log( "Request Failed: " + err );
            });




        return this.each(function(i, el) {
            //console.dir(prep_charts);
            $el = $(el).attr("data-bubble", i);

            $el
                .hover(function() {

                    $el = $(this);

                    $('#prep_wrap div').switchClass("in", "out", 100, "easeInOutQuad");

                    id = $(this).attr('id');
                    fill =  $('#'+id).children().attr('fill');
                    $('#'+id).children().attr('fill',"transparent");
                    $('#d_'+id).switchClass("out", "in", 100, "easeInOutQuad");


                    //unhide before rendering chart
                    side_bubble = $('#b'+i);
                    side_bubble.removeClass('out h');


                    //Top Industries
                    new Chart(document.getElementById('c'+i+'_1').getContext('2d')).Pie(prep_charts.regions[i].charts[0].data,prep_charts.options);

                    //Major Regional Employers
                    new Chart(document.getElementById('c'+i+'_2').getContext('2d')).Pie(prep_charts.regions[i].charts[1].data.data,prep_charts.options);


                }, function() {
                    $('#'+id).children().attr('fill',fill);
                });

        });

    }

})(jQuery);