jQuery(document).ready(function( $ ) {

    $.getJSON( "/wp-content/plugins/svg_map/data/2015/131.json")
        .done(function( json ) {
            contact_data = json;
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
        function drawRegionsMap() {
            var options = {
                colorAxis: {minValue: 0, maxValue: 0, colors: ['#1F4A82']},
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

            $.getJSON("/wp-content/plugins/svg_map/data/2015/investment-reps.json")
                .done(function (json) {
                    data = google.visualization.arrayToDataTable(json);
                    chart.draw(data, options);
                })
                .fail(function (jqxhr, textStatus, error) {
                    var err = textStatus + ", " + error;
                    console.log("Request Failed: " + err);
                });

            chart = new google.visualization.GeoChart(document.getElementById('world_map'));

            function selectHandler() {

                try {
                    handle(chart.getSelection()[0].row)
                }
                catch(err) {
                    //alert(err);
                }
            }
            google.visualization.events.addListener(chart, 'select', selectHandler);
        }

        google.setOnLoadCallback(drawRegionsMap);


    }

    function handle(id) {
        var c       = $('#contact_wrapper').empty();
        var contact = contact_data.data_array[id].contacts;
        $('.hide_on_mobile').hide();
        c.html('<h3>Export Contact Info for '+contact.repcountry+'</h3><ul class="contact_list">' +
            '<li>'+contact.agency+'</li>' +
            '<li>'+contact.name+'</li>' +
            '<li>'+contact.street1+'</li>' +
            '<li>'+contact.city+'  '+contact.zip+'</li>' +
            '<li>'+contact.country+'</li>' +
                //'<li>Territory: '+contact.territory+'</li>' +
            '<li>Contact Email:  <i class="icon-mail"></i><a href="mailto:'+contact.email+'">'+contact.name+'</a></li>' +
            '</ul>' +
            '<h3>Countries Represented </h3><ul class="contact_list">' +
            '<li>'+contact.territory+'</li></ul><br />'
        );
    }

    $(window).resize(function() {
        if($(window).width()>1200) {
            $('#world_map').html('<div class="info_box error"><p>Please refresh the page to see the world map<br /><a href="javascript:window.location.reload(true)">Refresh Page</a></p></div>');
        }
    });
});

