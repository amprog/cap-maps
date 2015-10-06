jQuery(document).ready(function($) {

    $.getJSON("/wp-content/plugins/svg_map/data/2015/11013.json")
        .done(function( json ) {

            if($('.touch .no_delay').length){
                new Chart(document.getElementById('s1_1').getContext('2d')).Doughnut(json.s1_1.data,json.options);
                new Chart(document.getElementById('s1_2').getContext('2d')).Doughnut(json.s1_2.data,json.options);
                new Chart(document.getElementById('s1_3').getContext('2d')).Doughnut(json.s1_3.data,json.options);
            } else {

                $('#s1_1').appear(function() {
                    new Chart(document.getElementById('s1_1').getContext('2d')).Doughnut(json.s1_1.data,json.options);
                },{accX: 0, accY: -200});

                $('#s1_2').appear(function() {
                    new Chart(document.getElementById('s1_2').getContext('2d')).Doughnut(json.s1_2.data,json.options);
                },{accX: 0, accY: -200});

                $('#s1_3').appear(function() {
                    new Chart(document.getElementById('s1_3').getContext('2d')).Doughnut(json.s1_3.data,json.options);
                },{accX: 0, accY: -200});
            }
        })
        .fail(function( jqxhr, textStatus, error ) {
            var err = textStatus + ", " + error;
            console.log( "Request Failed: " + err );
        });

});