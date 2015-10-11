jQuery(document).ready(function($) {

    //creating new charts
    $( ".create" ).live( "click", function() {

        if($(this).data('type')=='svg') {
            //$('#svg_select_wrap').remove();
            var data = {
                'action': 'cap_map_svg_action',
                'svg_slug': $( this ).val()
            };
            jQuery.post(ajaxurl, data, function(response) { console.dir(response);
                $('#svg_new').html(response.html);
            });
        } else if($(this).data('type')=='chart') {
            $('#chart_select_wrap').remove();
            var data = {
                'action': 'cap_map_chart_action',
                'chart_slug': $( this ).val()
            };
            jQuery.post(ajaxurl, data, function(response) {
                $('#chart_new').html(response.html);
            });
        } else {

        }
    });

    //new chart, needs blank form fields by chart type
    $( ".chart_type" ).live( "change", function() {
        if($('#chart_action').val()=='new') {
            var chart_type = $('.chart_type option:selected').val();
            $( ".add_field").attr('data-type',chart_type); //need to set data type now
            var data = {
                'action': 'cap_map_chart_line_action',
                'chart_type': chart_type,
                'number': 0
            }; console.dir(data);
            jQuery.post(ajaxurl, data, function(response) { console.dir(response);
                $(response.chart_data).prependTo( ".chart_data_wrap").fadeIn("slow");
            });
        }
    });

    //charting
    $( "#chart_select_wrap .chart_select" ).change(function() {
        var chart_select =    $( this ).val()
        if(chart_select!='Select One') {
            var data = {
                'action': 'cap_map_chart_action',
                'chart_slug': chart_select
            };
            jQuery.post(ajaxurl, data, function(response) {
                $('#chart_new').html(response.html);
            });
        }
    });

    //svg
    $( "#svg_select_wrap .svg_select" ).change(function() {
        var svg_select =    $( this ).val()
        if(svg_select!='Select One') {
            var data = {
                'action': 'cap_map_svg_action',
                'svg_slug': svg_select
            };
            jQuery.post(ajaxurl, data, function(response) {
                $('#svg_new').html(response.html);
            });
        }
    });

    //switches
    $( ".cb-enable" ).live( "click", function() {
        var parent = $(this).parents('.switch');
        $('.cb-disable',parent).removeClass('selected');
        $(this).addClass('selected');
        var myClass = $(this).data("class");
        $('#'+myClass+'_enabled').attr('checked', true);
        $('#'+myClass+'_disabled').attr('checked', false);
    });
    $( ".cb-disable" ).live( "click", function() {
        var parent = $(this).parents('.switch');
        $('.cb-enable',parent).removeClass('selected');
        $(this).addClass('selected');
        var myClass = $(this).data("class");
        $('#'+myClass+'_enabled').attr('checked', false);
        $('#'+myClass+'_disabled').attr('checked', true);
    });

    /**
     * Add fields
     */
    $( ".add_field" ).live( "click", function() {
        var chart_type = $('.chart_type option:selected').val();
        if(chart_type  && chart_type != 'Select One') {
            var data = {
                'action': 'cap_map_chart_line_action',
                'chart_type': chart_type,
                'number': $('.chart_data_inner').length
            };console.dir(data);
            jQuery.post(ajaxurl, data, function(response) { console.dir(response);
                $(response.chart_data).prependTo( ".chart_data_wrap").fadeIn("slow");
            });
        } else {
            alert("You must first choose a chart type before adding data!");
        }

    });


    //delete chart data line
    $( ".btns_delete" ).live( "click", function() {
        $('#data-'+$(this).data('id')).remove();
    });


    // form validation
    $( "#post" ).submit(function( event ) {
        //if there is  new chart being added, make sure select chart drop down is picked
        console.log($('.chart_type option:selected').val());
        if($('#chart_action').val()=='new'  && $('.chart_type option:selected').val() == 'Select One') {
            event.preventDefault();
            $('.chart_type').addClass('error');
            window.location.hash='#chart_type_anchor';
            alert("If adding a new chart please make sure to select one from the dropdown!");
        }
        //event.preventDefault();
    });
});
