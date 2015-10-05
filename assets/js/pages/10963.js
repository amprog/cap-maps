jQuery(document).ready(function( $ ) {
    /*
    $('.counter.type2').each(function() {
    
        var delay = $(this).data('delay');
        var time = 0;
    
        if(delay !== "" && delay !== 0){
            time = delay;
        }
    
        var $this = $(this);
    
        if($('.touch .no_delay').length){
            $this.parent().css('opacity', '1');
            $this.absoluteCounter({
                speed: 2000,
                fadeInDelay: 1000
            });
            resizeFonts();
        }else{
            $this.appear(function() {
                setTimeout(function(){
                    $this.parent().css('opacity', '1');
                    $this.absoluteCounter({
                        speed: 2000,
                        fadeInDelay: 1000
                    });
                    resizeFonts();
                },time);
            },{accX: 0, accY: -200});
        }
    
    });
    */

    $('.counter.type1').each(function() {

        var delay = $(this).data('delay');
        var time = 0;

        if(delay !== "" && delay !== 0){
            time = delay;
        }

        var $this = $(this);

        if($('.touch .no_delay').length){
            $this.parent().css('opacity', '1');
            var $max = parseFloat($this.text());
            $this.countTo({
                from: 0,
                to: $max,
                speed: 1500,
                refreshInterval: 50
            });
            //resizeFonts();
        }else{
            $this.appear(function() {
                setTimeout(function(){
                    $this.parent().css('opacity', '1');
                    var $max = parseFloat($this.text());
                    $this.countTo({
                        from: 0,
                        to: $max,
                        speed: 1500,
                        refreshInterval: 50
                    });
                    //resizeFonts();
                },time);
            },{accX: 0, accY: -200});
        }
    });


    //export chart
    $.getJSON( "/wp-content/plugins/svg_map/data/2015/10963.json")
        .done(function( json ) {
            $('#s_1_1').appear(function() {
                new Chart(document.getElementById('s_1_1').getContext('2d')).Bar(json.data_array,json.options);
            },{accX: 0, accY: -200});

        })
        .fail(function( jqxhr, textStatus, error ) {
            var err = textStatus + ", " + error;
            console.log( "Request Failed: " + err );
        });

    //top export partners
    $.getJSON( "/wp-content/plugins/svg_map/data/2015/international.json")
        .done(function( json ) {
            $('#s_1_2').appear(function() {
                new Chart(document.getElementById('s_1_2').getContext('2d')).Doughnut(json.data_array[0].export_data,json.options);
            },{accX: 0, accY: -200});

        })
        .fail(function( jqxhr, textStatus, error ) {
            var err = textStatus + ", " + error;
            console.log( "Request Failed: " + err );
        });

    
    //progress bars

    $('.progress_bars').each(function() {
        var $this = $(this);
        if($('.touch .no_delay').length){
            initToCounterHorizontalProgressBar();
            $this.find('.progress_bar').each(function() {
                var percentage = $(this).find('.progress_content').data('percentage');
                $(this).find('.progress_content').css('width', '0%');
                $(this).find('.progress_content').animate({'width': percentage+'%'}, 2000);
                $(this).find('.progress_number').css('width', '0%');
                $(this).find('.progress_number').animate({'width': percentage+'%'}, 2000);
            });
        }else{
            $this.appear(function() {
                initToCounterHorizontalProgressBar();
                $this.find('.progress_bar').each(function() {
                    var percentage = $(this).find('.progress_content').data('percentage');
                    $(this).find('.progress_content').css('width', '0%');
                    $(this).find('.progress_content').animate({'width': percentage+'%'}, 2000);
                    $(this).find('.progress_number').css('width', '0%');
                    $(this).find('.progress_number').animate({'width': percentage+'%'}, 2000);
                });
            },{accX: 0, accY: -200});
        }
    });

    $('.normal .percentage').each(function() {

        var $barColor = '#e91b23';

        if($(this).data('active') !== ""){
            $barColor = $(this).data('active');
        }

        var $trackColor = '#e3e3e3';

        if($(this).data('noactive') !== ""){
            $trackColor = $(this).data('noactive');
        }

        var $size = 171;

        var delay = $(this).data('delay');
        var time = 0;

        if(delay !== "" && delay !== 0){
            time = delay;
        }

        var $this = $(this);

        if($('.touch .no_delay').length){
            initToCounterProgressBar();

            $this.parent().css('opacity', '1');
            $this.easyPieChart({
                barColor: $barColor,
                trackColor: $trackColor,
                scaleColor: false,
                lineCap: 'butt',
                lineWidth: 31,
                animate: 1500,
                size: $size
            });

        }else{
            $this.appear(function() {
                initToCounterProgressBar();
                setTimeout(function(){

                    $this.parent().css('opacity', '1');
                    $this.easyPieChart({
                        barColor: $barColor,
                        trackColor: $trackColor,
                        scaleColor: false,
                        lineCap: 'butt',
                        lineWidth: 31,
                        animate: 1500,
                        size: $size
                    });

                },time);
            },{accX: 0, accY: -200});
        }

    });
});