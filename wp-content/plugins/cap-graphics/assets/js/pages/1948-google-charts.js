//requires extra libraries
/*
function drawCircle(svg) {
    // ref. http://g.raphaeljs.com/reference.html#Paper.piechart
    var r1 = Raphael(10, 10, 800, 400),
        pie = r1.piechart(100, 300, 50, [55, 20, 13, 32, 5, 1, 2]),
        pie_op = r1.piechart(300, 100, 70, [55, 20, 13, 32, 5, 1, 2]);

    // ref. http://raphaeljs.com/reference.html#Element.attr
    pie_op.attr({"opacity": 0.5});
    //var data = [55, 20, 13, 32, 5, 1, 2];

    // ref. http://raphaeljs.com/reference.html#Raphael.easing_formulas
    var anim_bfr = Raphael.animation({"stroke-width": 10}, 50, "<>");
    var anim_aft = Raphael.animation({"stroke-width":  1}, 50, "<>");

    var r2  = Raphael(10, 10, 800, 400),
        pie_hv = r2.piechart(500, 300, 80, [55, 20, 13, 32, 5, 1, 2]),
        pie_st = r2.piechart(700, 100, 80, [55, 20, 13, 32, 5, 1, 2]),
        cir_st = r2.circle(700, 100, 80);

    cir_st.attr({"stroke": "#fff"});
    pie_st.hover(function () {
        cir_st.animate(anim_bfr);
    }, function () {
        cir_st.animate(anim_aft);
    });

    // ref. http://g.raphaeljs.com/piechart2.html
    pie_hv.hover(function () {
        this.sector.stop();
        this.sector.scale(1.1, 1.1, this.cx, this.cy);
    }, function () {
        this.sector.animate({ transform: 's1 1 ' + this.cx + ' ' + this.cy }, 500, "bounce");
    });
}
*/

jQuery(document).ready(function($) {

    //$('#map').vectorMap({map: 'world_mill_en'});


    //world map
    /*
    $(function(){
        /*
        $('#map').vectorMap({
            map: 'world_mill_en',
            scaleColors: ['#C8EEFF', '#0071A4'],
            normalizeFunction: 'polynomial',
            hoverOpacity: 0.7,
            hoverColor: false,
            markerStyle: {
                initial: {
                    fill: '#F8E23B',
                    stroke: '#383f47'
                }
            },
            backgroundColor: '#383f47'
        });

        $('.map').vectorMap({map: 'world_mill_en'});

    });
*/

    //dynamic trade chart
    var initial_trade_chart = [
        {
            value: 60,
            color:'#f5989c',
            "label": "Machinery",
            "highlight": "#FF5A5E"
        },
        {
            value: 25,
            color:'#B93A43',
            "label": "Machinery",
            "highlight": "#5AD3D1"
        },
        {
            value: 15,
            color:'#35394A',
            "label": "Machinery",
            "highlight": "#FFC870"
        }
    ];

    //get data to use in all these functions

    $.getJSON( "/wp-content/plugins/svg_map/data/1948-trade_chart.json")
        .done(function( json ) {
            chart = json;
        })
        .fail(function( jqxhr, textStatus, error ) {
            var err = textStatus + ", " + error;
            console.log( "Request Failed: " + err );
        });


    //load initial chart
    $('#trade_chart').appear(function() {
        //myTradeChart = new Chart(document.getElementById('trade_chart').getContext('2d')).Doughnut(initial_trade_chart,{segmentStrokeColor : 'transparent',});
        //myTradeChart = new Chart(document.getElementById('trade_chart').getContext('2d')).Pie(initial_trade_chart,{segmentStrokeColor : '#fff',animateRotate : true,animateScale : true, legendTemplate : "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\"></span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>"});
        myTradeChart = new Chart(document.getElementById('trade_chart').getContext('2d')).Pie(initial_trade_chart,{segmentStrokeColor : 'transparent',animateRotate : true,animateScale : true});

        //var c = document.getElementById("trade_chart").getContext("2d");
        //var myTradeChart = new Chart(c[0]).Pie(initial_trade_chart,{segmentStrokeColor : 'transparent'});
    },{accX: 0, accY: -200});




    //
    $( ".str2" ).live( "click", function() {

        //var id = $(this).attr( "id" );
        var id = $(this).data( "n" );

        //update legend

        $('.chart h3').html(chart[id].name);
        $('.pie_graf_legend ul').empty();
        var items = [];
        $.each(chart[id].data, function( index, value ) {
            items.push('<li><div class="color_holder" style="background-color: '+value.color+';"></div><p>'+value.label+'</p></li>');
        });
        $(items.join( "" )).appendTo( $( ".pie_graf_legend ul" ) );

        //update chart
        //myTradeChart.update();
        //window.myTradeChart.destroy();
        //myTradeChart = new Chart(document.getElementById('trade_chart').getContext('2d')).Doughnut(initial_trade_chart,{segmentStrokeColor : 'transparent',});


        //var myTradeChart = new Chart(document.getElementById('trade_chart').getContext('2d')).Line(chart[id].data);


        //myTradeChart.clear();
    });

    //TODO: add appear

    //import/export by country using google charts
    google.load("visualization", "1.1", {packages:["bar"]});
    //google.load("visualization", "1", {packages:["corechart"]});  //breaks
    google.setOnLoadCallback(drawChart);




    function drawChart() {

        //1948-import_export.json
        $.getJSON( "/wp-content/plugins/svg_map/data/2015/1948-import_export.json")
            .done(function( json ) {
                var hb_data = google.visualization.arrayToDataTable(json.data);  //array to datatable
                var chart = new google.charts.Bar(document.getElementById("import_export_chart"));
                var options = google.charts.Bar.convertOptions(json.options);

                //chart.draw(hb_data, json.options);
                //for material charts
                chart.draw(hb_data, options);
            })
            .fail(function( jqxhr, textStatus, error ) {
                var err = textStatus + ", " + error;
                console.log( "Request Failed: " + err );
            });


        //1948-industry.json
        $.getJSON( "/wp-content/plugins/svg_map/data/1948-industry.json")
            .done(function( json ) {
                var hb_data2 = google.visualization.arrayToDataTable(json.data);  //array to datatable
                var chart2 = new google.charts.Bar(document.getElementById("industry_chart"));
                var options2 = google.charts.Bar.convertOptions(json.options);

                //for material charts
                chart2.draw(hb_data2, options2);

                //chart2.draw(hb_data2, json.options);
                //chart2.draw(hb_data2, {width: 400, height: 240,vAxis: {textStyle: {fontSize: 17}}});
            })
            .fail(function( jqxhr, textStatus, error ) {
                var err = textStatus + ", " + error;
                console.log( "Request Failed: " + err );
            });

/*
            //pie charts
        var data3 = google.visualization.arrayToDataTable([
            ['Task', 'Hours per Day'],
            ['Work',     11],
            ['Eat',      2],
            ['Commute',  2],
            ['Watch TV', 2],
            ['Sleep',    7]
        ]);

        var options3 = {
            title: 'Imports by Country'
        };

        var chart3 = new google.visualization.PieChart(document.getElementById('gpie1'));

        chart3.draw(data3, options3);

        var data4 = google.visualization.arrayToDataTable([
            ['Task', 'Hours per Day'],
            ['Work',     11],
            ['Eat',      2],
            ['Commute',  2],
            ['Watch TV', 2],
            ['Sleep',    7]
        ]);

        var options4 = {
            title: 'Exports by Country'
        };

        var chart4 = new google.visualization.PieChart(document.getElementById('gpie2'));

        chart4.draw(data4, options4);
*/

        /* pull from json keep this for referance
        var data = google.visualization.arrayToDataTable([
            ['Country', 'Import', 'Export'],
            ['Canada', 8000, 8000],
            ['Mexico', 24000, 20000],
            ['China', 30000, 24000],
            ['Netherlands', 50000, 45000],
            ['Germany', 60000, 55000],
            ['Japan', 50000, 75000],
            ['United Kingdom', 60000, 29000],
            ['Brazil', 50000, 75000],
            ['South Korea', 60000, 45000],
            ['Belgium', 60000, 35000]
        ]);

        var options = {
            chart: {
                title: 'Based off 2013 Stats'
                //subtitle: 'Sales, Expenses, and Profit: 2014-2017',
            },
            //width: 1200,
            //height: 700,
            fontSize: '17',  //?
            fontName: 'arial', //?
            backgroundColor: '#000000', //?
            //dataOpacity:'.7', //?
            hAxis: {
                gridlines:  {color: '#ffffff', count: 0}
                //position: 'left',
                //alignment: 'start'
            },
            legend: {
                position: 'none'
                //position: 'left',
                //alignment: 'start'
            },
            colors: ['#93A9CF', '#4572A7'],
            bars: 'horizontal' // Required for Material Bar Charts.
        };
        */


    }


    //population chart
    var pa_pop_chart = {
        labels : ["1970"," 1980"," 1990"," 2000","2010"],datasets : [
            {
                //pa
                label: 'Pennsylvania',
                fillColor: 'rgba(192,0,0,0.7)',
                //#B93A43
                data:[11800000,11860000,11900000,12280000,12710000
                ]
            },
            {
                label: 'New Jersey',
                fillColor: 'rgba(9,63,127,0.7)',
                //#093f7f
                data:[7171000,7365000,7763000,8431000,8802000
                ]
            }
        ]};

    $j('#pa_pop_chart').appear(function() {
        new Chart(document.getElementById('pa_pop_chart').getContext('2d')).Line(pa_pop_chart,{scaleOverride : true,
            scaleStartValue: 6000000,
            scaleStepWidth : 1000000,
            scaleSteps : 8,
            bezierCurve : false,
            pointDot : true,
            scaleLineColor: '#000000',
            scaleFontColor : '#4F4F4F',
            scaleFontSize : 13,
            scaleGridLineColor : '#e1e1e1',
            datasetStroke : false,
            datasetStrokeWidth : 0,
            animationSteps : 120,});
    },{accX: 0, accY: -200});

    //export stats
    /*
    var map_chart = [
        {
            value: 15,
            color:'#f5989c'
        },
        {
            value: 25,
            color:'#B93A43'
        },
        {
            value: 60,
            color:'#35394A'
        }
    ];

    $('#map_chart').appear(function () {
        new Chart(document.getElementById('map_chart').getContext('2d')).Doughnut(map_chart, {segmentStrokeColor: 'transparent',});
    }, {accX: 0, accY: -200});

    */





});