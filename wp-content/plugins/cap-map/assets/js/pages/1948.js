jQuery(document).ready(function($) {

    $.getJSON( "/wp-content/plugins/svg_map/data/2015/1948-trade_chart.json")
        .done(function( json ) {
            trade_chart_data = json;
            myTradeChart = new Chart(document.getElementById('trade_chart').getContext('2d')).Pie(trade_chart_data.data_array[0].data,trade_chart_data.options);
        })
        .fail(function( jqxhr, textStatus, error ) {
            var err = textStatus + ", " + error;
            console.log( "Request Failed: " + err );
        });

    //live update
    $( ".str2" ).live( "click", function() {
        var id = $(this).data( "n" );

        //update legend
        $('.chart h3').html(trade_chart_data.data_array[id].name);
        $('.pie_graf_legend ul').empty();

        var items = [];
        $.each(trade_chart_data.data_array[id].data, function( index, value ) {
            items.push('<li><div class="color_holder" style="background-color: '+value.color+';"></div><p>'+value.label+'</p></li>');
        });
        $(items.join( "" )).appendTo( $( ".pie_graf_legend ul" ) );

        $('#trade_chart_wrapper').empty();
        $('#trade_chart_wrapper').html('<canvas id="trade_chart"></canvas>');
        new Chart(document.getElementById('trade_chart').getContext('2d')).Pie(trade_chart_data.data_array[id].data,trade_chart_data.options);
    });

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