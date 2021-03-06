jQuery(document).ready(function($) {
    svg_wrap = $('#svg_select_wrap');
    //creating new charts
    $( ".create" ).live( "click", function(e) {

        if($(this).data('type')=='svg') {
            console.log($( this ).val());
            $('#svg_select_wrap').remove();
            //$('#svg_select_wrap #svg_select_inner').addClass('h');
            $('#svg_slug_wrap').html('<span>SVG Package Name</span>  <input type="text" name="svg_slug" id="svg_slug" placeholder="Enter a slug for this SVG Graphic" />');
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
        e.preventDefault();
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
            $('#chart_select_wrap .loading').removeClass('h');
            var data = {
                'action': 'cap_map_chart_action',
                'chart_slug': chart_select
            };
            jQuery.post(ajaxurl, data, function(response) {
                $('#chart_new').html(response.html);
                $('#chart_select_wrap .loading').addClass('h');
            });
        }
    });

    //svg
    $( "#svg_select_wrap .svg_select" ).live( "change", function() {
    //$( "#svg_select_wrap .svg_select" ).change(function() {
        var svg_select =  $( this ).val(); console.log(svg_select);
        var loading    =  $('#svg_select_wrap .loading');
        if(svg_select!='Select One') {
            loading.removeClass('h');
            var data = {
                'action': 'cap_map_svg_action',
                'svg_slug': svg_select
            };console.dir(data);
            jQuery.post(ajaxurl, data, function(response) {
                $('#svg_new').html(response.html);console.dir(response);
                loading.addClass('h');
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
            $.post(ajaxurl, data, function(response) { console.dir(response);
                $(response.chart_data).prependTo( ".chart_data_wrap").fadeIn("slow");
            });
        } else {
            alert("You must first choose a chart type before adding data!");
        }
    });

    //delete chart data line
    $( ".btn.delete" ).live( "click", function() {
        $('#data-'+$(this).data('id')).remove();
    });

    //file save
    $( ".btn.save" ).live( "click", function() {
        $(this).addClass('loading');
        file     =  $(this).data('file');

        if($('#svg_slug').val()) {
            var svg_slug = $('#svg_slug').val();
        } else if($('.svg_select').val()) {
            var svg_slug = $('.svg_select option:selected').val();
        } else {
            alert("Please enter a slug for this SVG Graphic");
        }
        var data = {
            'action': 'cap_map_file_save_action',
            'ID': $('.svg_li #ID').val(),
            'file': file,
            'svg_slug': svg_slug,
            'data': $("textarea[name='"+$(this).data('file')+"']").val()
        };console.dir(data);
        $.post(ajaxurl, data, function(response) { console.dir(response);
            $('#btn_'+file).removeClass('loading');
        });
    });

    // form validation
    $( "#post" ).submit(function( event ) {
        if($('#chart_action').val()=='new'  && $('.chart_type option:selected').val() == 'Select One') {
            event.preventDefault();
            $('.chart_type').addClass('error');
            window.location.hash='#chart_type_anchor';
            alert("If adding a new chart please make sure to select one from the dropdown!");
        }
        //event.preventDefault();
    });
});
