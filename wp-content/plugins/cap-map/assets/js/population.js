
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