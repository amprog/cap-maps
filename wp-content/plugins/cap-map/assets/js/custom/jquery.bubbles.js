(function( $ ) {

    // Create plugin
    $.fn.bubbles = function(el) {

        var $bubble,
            $body = $('body'),
            $el;

        /*
        //get chart data from json
        $.getJSON( "/wp-content/plugins/svg_map/data/all-bubbles.json")
            .done(function( json ) {
                all_charts = json;
            })
            .fail(function( jqxhr, textStatus, error ) {
                var err = textStatus + ", " + error;
                console.log( "Request Failed: " + err );
            });
*/

        return this.each(function(i, el) {

            $el = $(el).attr("data-bubble", i);

            $el
                .hover(function() {

                    /*
                    //hide everything
                    $('#bubble_wrap .bub').addClass('out h');

                    $el = $(this);

                    //unhide before rendering chart
                    side_bubble = $('#b'+i);
                    side_bubble.removeClass('out h');

                    //trade chart
                    new Chart(document.getElementById('c'+i+'_1').getContext('2d')).Pie(all_charts.chart_data[0].counties[i].charts[1].data,all_charts.options);

                    //industry chart
                    new Chart(document.getElementById('c'+i+'_2').getContext('2d')).Pie(all_charts.chart_data[0].counties[i].charts[2].data,all_charts.options);

                    //education chart
                    //new Chart(document.getElementById('c'+i+'_2').getContext('2d')).Pie(all_charts.chart_data[i].charts[2].data,all_charts.options);
                    new Chart(document.getElementById('c'+i+'_3').getContext('2d')).Bar(all_charts.chart_data[0].counties[i].charts[3].data_array,all_charts.options);
*/
                }, function() {

                    //may not have to do anything on Mouseleave
                });

        });

    }

})(jQuery);