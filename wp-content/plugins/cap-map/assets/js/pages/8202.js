jQuery(document).ready(function( $ ) {

    $.getJSON( "/wp-content/plugins/svg_map/data/2015/1948-trade_chart.json")
        .done(function( json ) {
            chart_data = json;

            if($('.touch .no_delay').length){
                new Chart(document.getElementById('trade_chart').getContext('2d')).Pie(chart_data.data_array[0].data,chart_data.options);
            }else{

                $('#trade_chart').appear(function() {
                    new Chart(document.getElementById('trade_chart').getContext('2d')).Pie(chart_data.data_array[0].data,chart_data.options);
                },{accX: 0, accY: -200});
            }
        })
        .fail(function( jqxhr, textStatus, error ) {
            var err = textStatus + ", " + error;
            console.log( "Request Failed: " + err );
        });

    //industry bar chart
    $.getJSON( "/wp-content/plugins/svg_map/data/2015/1948-industry_bar.json")
        .done(function( json ) {

            if($('.touch .no_delay').length){
                new Chart(document.getElementById('industry_bar').getContext('2d')).Bar(json.data_array,json.options);

            }else{
                $('#industry_bar').appear(function() {
                    new Chart(document.getElementById('industry_bar').getContext('2d')).Bar(json.data_array,json.options);
                },{accX: 0, accY: -200});
            }
        })
        .fail(function( jqxhr, textStatus, error ) {
            var err = textStatus + ", " + error;
            console.log( "Request Failed: " + err );
        });
}); 