jQuery(document).ready(function( $ ) {

    $.getJSON( "/wp-content/plugins/svg_map/data/2015/1948-trade_chart.json")
        .done(function( json ) {
            trade_chart_data = json;
        })
        .fail(function( jqxhr, textStatus, error ) {
            var err = textStatus + ", " + error;
            console.log( "Request Failed: " + err );
        });

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

                console.log(id);
                console.dir(trade_chart_data);


                //contacts
                var c       = $('#contact_wrapper').empty();
                var contact = trade_chart_data.data_array[id].contacts;

                c.html('<h3>Export Contact Info</h3><ul class="contact_list">' +
                '<li>'+contact.agency+'</li>' +
                '<li>'+contact.name+'</li>' +
                '<li>'+contact.street1+'</li>' +
                '<li>'+contact.city+'  '+contact.zip+'</li>' +
                '<li>'+contact.country+'</li>' +
                //'<li>Territory: '+contact.territory+'</li>' +
                '<li>Contact in PA:  <i class="icon-mail"></i><a href="mailto:'+contact.pa_email+'">'+contact.pa_name+'</a></li>' +
                '</ul>');

            }
            catch(err) {
                //alert(err);
            }
        }

        google.visualization.events.addListener(chart, 'select', selectHandler);

    }


    //imports and exports line
    $.getJSON( "/wp-content/plugins/svg_map/data/2015/1948-import_export_line.json")
        .done(function( json ) {
            $('#import_export_line').appear(function() {
                new Chart(document.getElementById('import_export_line').getContext('2d')).Line(json.data_array,json.options);
            },{accX: 0, accY: -200});
        })
        .fail(function( jqxhr, textStatus, error ) {
            var err = textStatus + ", " + error;
            console.log( "Request Failed: " + err );
        });

    //industry bar chart
    $.getJSON( "/wp-content/plugins/svg_map/data/2015/1948-industry_bar.json")
        .done(function( json ) {
            $('#industry_bar').appear(function() {
                new Chart(document.getElementById('industry_bar').getContext('2d')).Bar(json.data_array,json.options);
            },{accX: 0, accY: -200});

        })
        .fail(function( jqxhr, textStatus, error ) {
            var err = textStatus + ", " + error;
            console.log( "Request Failed: " + err );
        });
});

