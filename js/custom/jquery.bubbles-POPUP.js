(function( $ ) {

    // Create plugin
    $.fn.bubbles = function(el) {

        var $bubble,
            $body = $('body'),
            $el;

        //get chart data from json
        $.getJSON( "/wp-content/plugins/svg_map/data/all-bubbles.json")
            .done(function( json ) {
                //console.dir(json.chart_data.charts);

                all_charts = json;
                //console.dir(json.chart_data.charts);
            })
            .fail(function( jqxhr, textStatus, error ) {
                var err = textStatus + ", " + error;
                console.log( "Request Failed: " + err );
            });


        return this.each(function(i, el) {

            $el = $(el).attr("data-bubble", i);

            var $bubble = $('<div class="bubble" data-bubble="' + i + '">' + $('#b'+i).html() + '<div class="arrow"></div></div>').appendTo("body");

            var div = $el.data('bubble');

            var linkPosition = $el.position();

            $bubble.css({
                top: linkPosition.top - $bubble.outerHeight() - 13,
                left: linkPosition.left - ($bubble.width()/2)
            });



            $el
                .hover(function() {

                    $el = $(this);

                    $bubble = $('div[data-bubble=' + $el.data('bubble') + ']');

                    console.log($bubble);

                    //console.dir(all_charts.chart_data.charts[i]);

                    //console.log(i);
                    //console.dir(all_charts.chart_data.charts[i]);
                    //console.dir(all_charts.chart_data.charts);
                    console.dir(all_charts.chart_data[i].charts[0]);



                    //dynamic charts in bubbles, on bubble appear, get charts

                    //console.log('bubble_id: '+i);

                    //check for chart existance

                    console.log('c'+i+'_1');
                    //chart 0 - labor force
                    //new Chart(document.getElementById('c'+i+'_1').getContext('2d')).Doughnut(all_charts.chart_data[i].charts[0].data);

                    new Chart(document.getElementById('c'+i+'_1').getContext('2d')).Pie(all_charts.chart_data[i].charts[0].data,{segmentStrokeColor : 'transparent'});

                    //get data out of json


                    //new Chart(document.getElementById('fund_pie1').getContext('2d')).Doughnut(fund_pie1,{segmentStrokeColor : 'transparent',});


                    // Reposition bubble, in case of page movement e.g. screen resize
                    var linkPosition = $el.position();

                    /*
                    $bubble.css({
                        top: linkPosition.top - $bubble.outerHeight() - 13,
                        left: linkPosition.left - ($bubble.width()/2)
                    });
                    */

                    //positioning
                    $bubble.css({
                        top: linkPosition.top,
                        left: linkPosition.left
                    });

                    //console.log(linkPosition.top);
                    //console.log(linkPosition.left);

                    $bubble.addClass("active");

                    // Mouseleave
                }, function() {

                    $el = $(this);

                    // Temporary class for same-direction fadeout
                    $bubble = $('div[data-bubble=' + $el.data('bubble') + ']').addClass("out");

                    // Remove all classes
                    setTimeout(function() {
                        $bubble.removeClass("active").removeClass("out");
                    }, 300);

                });

        });

    }

})(jQuery);