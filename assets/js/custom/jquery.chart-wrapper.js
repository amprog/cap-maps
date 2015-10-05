(function( $ ) {

    // Create plugin to wrap all charts in!
    $.fn.chartWrap = function(data) {

        if(data.options.chartType=='linepie') {

            var canvas  = document.getElementById('linepie1');
            var ctx     = canvas.getContext('2d');

            var draw = function(radius,color,value,lineWidth){


                //var x                = canvas.width / 2-100;
                x                    = canvas.width / 2;
                y                    = canvas.height / 2;
                var startAngle       = 0;
                var endAngle         = 2 * Math.PI;
                var counterClockwise = false;
                var quart            = Math.PI / 2;

                ctx.beginPath();

                ctx.arc(x, y, radius, -(quart), ((endAngle) * value) - quart, false);
                //ctx.arc(x, y, radius, startAngle, endAngle, counterClockwise);

                ctx.lineWidth = lineWidth;

                // line color
                ctx.strokeStyle = color;
                ctx.stroke();
            }

            //write text in middle
            var text = function(label,value,color,y) {

                ctx.font      = data.options.font;
                ctx.fillStyle = color;

                //ctx.fillText(value+'%',canvas.width / 2-45,y);
                ctx.fillText(value+'%',canvas.width / 2-45,y+45);
                //ctx.fillText(value+'%',canvas.width / 2-45,y);  //no labels

            }

            $.each( data.data_array[0].data, function( k, v ) {

                if(k==0) {
                    m = 1;
                } else {
                    m = k + 1;
                }
                var radius = 75 + (data.options.lineWidth * m);
                var y      = 135 - data.options.lineWidth * m;

                draw(radius,v.color, v.value/ 100, data.options.lineWidth);
                text(v.label,v.value, v.color,y);

                if(data.options.showLabels) {
                    ctx.fillText(v.label, 30,y+45);  //no labels
                }

            });


            if(data.options.showChartName) {
                ctx.textAlign = 'center';
                ctx.fillStyle = data.options.defaultColor;
                ctx.fillText(data.options.chartName,x,y);
            }


        }

        //TODO: wrap other charts around this as well
        //new Chart(document.getElementById('industry_bar').getContext('2d')).Bar(data.data_array,data.options);

    }
})(jQuery);