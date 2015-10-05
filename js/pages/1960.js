jQuery(document).ready(function( $ ) {

    $.getJSON( "/wp-content/plugins/svg_map/data/2015/1948-trade_chart.json")
        .done(function( json ) {
            trade_chart_data = json;
        })
        .fail(function( jqxhr, textStatus, error ) {
            var err = textStatus + ", " + error;
            console.log( "Request Failed: " + err );
        });


    mobile = is_mobile();
    if(mobile.mobile==1 || mobile.width<1200) {
        $('.hide_on_mobile').hide();
        $( "#county_drop" ).change(function() {
            var i = $(this).val();
            handle(i);
        });

        $('#county_drop').bind($.browser.msie ? 'click' : 'change', function(event) {
            var i = $(this).val();
            handle(i);
        })

    } else {
        google.setOnLoadCallback(drawRegionsMap);

        function drawRegionsMap() {
            var options = {
                colorAxis: {minValue: 0, maxValue: 0,   colors: ['#1F4A82']},
                legend: 'none',
                backgroundColor: {fill: 'transparent', stroke: '#FFF', strokeWidth: 0},
                datalessRegionColor: '#f5f5f5',
                //displayMode: 'markers',
                dataMode: 'regions',
                enableRegionInteractivity: true,
                //resolution: 'metros',  //provinces
                //sizeAxis: {minValue: 1, maxValue: 1, minSize: 5, maxSize: 5},
                //region: 'world',
                //            tooltip: {textStyle: {color: '#111111', fontSize: 12}},
                keepAspectRatio: true,
                tooltip: {trigger: 'none'},
                magnifyingGlass: {enable: true, zoomFactor: 5.0}
                //showZoomOut: true,
            };

            $.getJSON( "/wp-content/plugins/svg_map/data/2015/1960.json")
                .done(function( json ) {
                    data = google.visualization.arrayToDataTable(json);
                    chart.draw(data, options);
                })
                .fail(function( jqxhr, textStatus, error ) {
                    var err = textStatus + ", " + error;
                    console.log( "Request Failed: " + err );
                });

            chart = new google.visualization.GeoChart(document.getElementById('world_map'));


            function selectHandler() {

                try {
                    var id = chart.getSelection()[0].row;
                    handle(id);
                }
                catch(err) {
                    //alert(err);
                }
            }

            google.visualization.events.addListener(chart, 'select', selectHandler);

        }
    }


    function handle(id) {
        //update legend 1
        $('.hide_on_mobile').hide();
        $('.hide_on_click').hide();
        $('#trade_legend1 ul').empty();

        var items = [];
        $.each(trade_chart_data.data_array[id].export_data, function( index, value ) {
            items.push('<li><div class="color_holder" style="background-color: '+value.color+';"></div><p>'+value.label+'</p></li>');
        });
        $(items.join( "" )).appendTo( $( "#trade_legend1 ul" ) );


        $('#trade_chart_wrapper1').empty();
        $('#trade_chart_wrapper1').html('<h3>'+trade_chart_data.data_array[id].name1+'</h3><canvas id="trade_chart1"></canvas>');
        new Chart(document.getElementById('trade_chart1').getContext('2d')).Pie(trade_chart_data.data_array[id].export_data,trade_chart_data.options);
    }

    $(window).resize(function() {
        if($(window).width()>1200) {
            $('#world_map').html('<div class="info_box error"><p>Please refresh the page to see the world map<br /><a href="javascript:window.location.reload(true)">Refresh Page</a></p></div>');
        }
    });
});

