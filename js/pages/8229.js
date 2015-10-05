jQuery(document).ready(function( $ ) {

    function goToPage(page) {
        console.log('page: '+page);

        this.pdfViewer.currentPageNumber = page;
    }

    google.setOnLoadCallback(drawRegionsMap);

    function drawRegionsMap() {
    //1F4A82
        var options = {
            /* colorAxis: { colors: ['#1F4A82','#1F4A82']}, */
            colorAxis: {minValue: 0, maxValue: 0,   colors: ['#1F4A82']},
            legend: 'none',
            backgroundColor: {fill: 'transparent', stroke: '#FFF', strokeWidth: 0},
            datalessRegionColor: '#f5f5f5',
            //displayMode: 'markers',
            dataMode: 'regions',
            enableRegionInteractivity: true,
            //resolution: 'metros',  //provinces
            //sizeAxis: {minValue: 1, maxValue: 1, minSize: 5, maxSize: 5},
            region: 151,
            keepAspectRatio: true,
            magnifyingGlass: {enable: true, zoomFactor: 5.0},
            tooltip: {trigger: 'none'}
            //showZoomOut: true,
        };

        $.getJSON( "/wp-content/plugins/svg_map/data/8229.json")
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
                var page = data.getValue(id, 2);
                $('.pdf_frame').attr('src','/wp-content/plugins/svg_map/pdfjs/web/view.php?file=http://newpa.com/wp-content/plugins/svg_map/pdfjs/pdf/biotech.pdf?download=true&amp;print=true&amp;openfile=false#page='+page);
                $('body,html').animate({ scrollTop: "1000" }, 750, 'easeOutExpo' );
            }
            catch(err) {
                //alert(err);
            }
        }
        google.visualization.events.addListener(chart, 'select', selectHandler);
    }
});

