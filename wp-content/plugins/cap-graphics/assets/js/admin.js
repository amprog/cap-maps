jQuery(document).ready(function($) {
    //TODO:  js for placing charts needs to be a foreach that iterates through all charts it finds on the page
    svg_wrap = $('#svg_select_wrap');
    //creating new charts
    $( ".create" ).live( "click", function(e) {

        if($(this).data('type')=='svg') {

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

    //create new charts
    $( ".new_chart" ).live( "click", function(e) {
        var data = {
            'action': 'cap_map_chart_action',
            'chart_type': $( this ).data('type')
        };
        jQuery.post(ajaxurl, data, function(response) {
            $('#list_assets').html(response.html); console.dir(response);
        });
    });


    //save new chart
    /* DO NOT DO THIS WITH AJAX
    $( "#frm_new_chart .button-primary" ).live( "click", function() {
        console.dir($('#frm_new_chart').serialize());
        var data = {
            'action': 'gc_chart_save_callback',
            'data': $('#frm_new_chart').serialize()
        };
        jQuery.post(ajaxurl, data, function(response) {
            $('#list_assets').html(response.html); console.dir(response);
        });
    });
*/


    //all button clicks for charts
    $( ".charts_admin .meta ul li" ).live( "click", function(e) {

        var c = $(this).attr('class');
        chart = $(this).data('i');
        slug  = $(this).parent().parent().parent().data('slug');
        dir   = $(this).data('dir');
        type  = $(this).data('type');

        if(c=='delete') {
            var question = "Are you sure you want to delete this chart?";
            confirmation(question).then(function (answer) {
                if(answer=='true') {
                    var data = {
                        'action': 'gc_item_status',
                        'type': type,
                        'slug': slug,
                        'status': 0
                    };
                    jQuery.post(ajaxurl, data, function (response) {
                        msg('This chart has been deleted',2000,'info');
                        $('#l-' + chart).remove();
                    });
                }
            });
        } else if (c=='copy') {
            //TODO: open the same screen for edit, except without disabling slug field
            console.log('copy');

            var data = {
                'action': 'gc_chart_action',
                'chart_slug': $( '#l-'+chart).data('slug'),
                'chart_action': c
            };
            jQuery.post(ajaxurl, data, function(response) {
                $('#list_assets').html(response.html); console.dir(response);
            });


        } else if (c=='view') {
            //inline version is messy but increases scalability and portability
            var obj = {chart_slug:slug, chart_type:"blah",action:'gc_chart_view'}; console.dir(obj); console.log(ajaxurl);
            $.ajax({
                type: "POST",
                cache: false,
                url: this.href, // preview.php
                data: obj, // all form fields
                success: function (data) {
                    // on success, post (preview) returned data in fancybox
                    $.fancybox(data, {
                        // fancybox API options
                        fitToView: false,
                        width: '95%',
                        height: '95%',
                        autoSize: false,
                        closeClick: false,
                        openEffect: 'none',
                        closeEffect: 'none',
                        //href: ajaxurl,
                        href: '/wp-content/plugins/cap-graphics/assets/templates/chart-view.php'
                    });
                }
            });
        } else if (c=='edit') {

            console.log('edit');
            var data = {
                'action': 'gc_chart_action',
                'chart_slug': $( '#l-'+chart).data('slug'),
                'chart_action': c
            };
            jQuery.post(ajaxurl, data, function(response) {
                $('#list_assets').html(response.html);
            });

        } else {

        }
    });

    //all button clicks for svg
    $( ".svg_admin .meta ul li" ).live( "click", function(e) {

        var c = $(this).attr('class');
        svg   = $(this).data('i');
        slug  = $(this).parent().parent().parent().data('slug');
        type  = $(this).data('type');

        if(c=='delete') {

            var question = "Are you sure you want to delete this SVG package?";
            confirmation(question).then(function (answer) {
                if(answer=='true') {
                    var data = {
                        'action': 'gc_item_status',
                        'type': type,
                        'slug': slug,
                        'status': 0
                    };
                    jQuery.post(ajaxurl, data, function (response) {
                        msg('This SVG has been deleted',2000,'info');
                        $('#l-' + svg).remove();
                    });
                }
            });
        } else if (c=='copy') {
            //TODO: open the same screen for edit, except without disabling slug field
            console.log('copy');

            var data = {
                'action': 'gc_svg_action',
                'svg_slug': $( '#l-'+svg).data('slug'),
                'svg_action': c
            }; console.log("gc_svg_action copy"); console.dir(data);
            jQuery.post(ajaxurl, data, function(response) {
                $('#list_assets').html(response.html); console.dir(response);
            });


        } else if (c=='view') {
            //inline version is messy but increases scalability and portability
            var obj = { slug:slug, chart_type:"blah",action:'gc_svg_view'}; console.dir(obj); console.log(ajaxurl);
            $.ajax({
                type: "POST",
                cache: false,
                url: this.href, // preview.php
                data: obj, // all form fields
                success: function (data) {
                    // on success, post (preview) returned data in fancybox
                    $.fancybox(data, {
                        // fancybox API options
                        fitToView: false,
                        width: '95%',
                        height: '95%',
                        autoSize: false,
                        closeClick: false,
                        openEffect: 'none',
                        closeEffect: 'none',
                        //href: ajaxurl,
                        href: '/wp-content/plugins/cap-graphics/assets/templates/svg-view.php'
                    });
                }
            });

        } else if (c=='edit') {


            var data = {
                'action': 'gc_svg_action',
                'svg_slug': $( '#l-'+svg).data('slug'),
                'svg_action': c
            }; console.dir(data);
            jQuery.post(ajaxurl, data, function(response) { console.log(response);
                $('#list_assets').html(response.html);
            });

        } else {

        }

    });

    //copy shortcode
    $( ".shortcode" ).live( "click", function() {
        var copy  = $(this).addClass('highlight');
        var short = copy.val();
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val($(this).val()).select();
        copy.val('copied!');
        document.execCommand("copy");
        $temp.remove();
        setTimeout(function() {
            copy.removeClass('highlight');
            copy.val(short);
        }, 3000);
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
     * FIXME:  add field needs to set a _SESSION for count. so that we can get this session in app.php LINE 460
     */
    $( ".add_field" ).live( "click", function() {
        var chart_type = $(this).data('type');
        if(chart_type) {
            var data = {
                'action': 'gc_chart_line_action',
                'chart_type': chart_type,
                'number': $('.chart_data_inner').length
            };console.log('number: '+$('.chart_data_inner').length);
            $.post(ajaxurl, data, function(response) {
                console.log('add_field response');
                console.dir(response);
                $(response.chart_data).prependTo( ".chart_data_wrap").fadeIn("slow");
            });
        } else {
            msg("You must first choose a chart type before adding data!",3000,'warning');
        }
    });

    //delete chart data line
    $( ".btn.delete" ).live( "click", function() {
        $('#data-'+$(this).data('id')).remove();
    });

    //save svg
    $( ".save_svg" ).live( "click", function() {
        var svg_slug = $('#svg_slug').val();

        if(svg_slug) {
            var data = {
                'action': 'gc_file_save_action',
                'svg_name': $('#svg_name').val(),
                'svg_description': $('#svg_description').val(),
                'extra_files': $('#extra_files').val(),
                'svg_action': $('#svg_action').val(),
                'svg_slug': validSlug(svg_slug),
                'id': $('#id').val(),
                'svg_data': $("textarea[name='svg_data']").val(),
                'js_data': $("textarea[name='js_data']").val(),
                'css_data': $("textarea[name='css_data']").val(),
                'json_data': $("textarea[name='json_data']").val()
            }; console.dir(data);
            $.post(ajaxurl, data, function(response) {console.dir(response);

                if(response.svg_new=='1') {
                    $('#svg_slug').val(response.svg_slug);
                    $('#svg_slug').attr('disabled',true);
                    $('#svg_action').val('edit');
                }
                msg("SVG Graphic Saved",3000,'info');
            });
        } else {
            msg("Please enter a slug",3000,'warning');
        }
    });

    //update canvas and save
    $( ".chart_update" ).live( "click", function() {

        var chart_action = $('#chart_action').val();
        var chart_type   = $('#chart_type').val();

        //check if total value is over 100
        if(chart_type=='Pie' || chart_type=='Doughnut') {

            var numbers = $( ".chart_data_wrap input[type='number']" ).toArray();
            var total   = 0;
            $.each(numbers, function( index, value ) {
                total += parseInt(value.value);
            });
            if(total==100) {
                msg("Chart saved",3000,'info');
            } else {
                msg("This pie chart adds up to "+total,3000,'warning');
            }
        }

        //first check to see if this is copy
        if(chart_action=='copy') {
            //make sure the slug name changed
            if($('#chart_slug_d').val()==$('#chart_slug').val()) {
                msg('You need to change the slug name since you are copying another chart!',3000,'danger')
                return;
            }
            var chart_slug = $('#chart_slug').val();
        } else {
            var chart_slug = $('#chart_slug_d').val();
        }

        //turn a form into an object
        obj = $('#frm_chart_update').serializeObject();
        var data = {
            'action': 'gc_chart_save',
            'chart_action': chart_action,
            'chart_slug': chart_slug,
            'chart_type': chart_type,
            'data': obj
        }; console.dir(data);
        //FIXME:  we need a better way to keep track of counts
        $.post(ajaxurl, data, function(response) {
            json = jQuery.parseJSON(response.chart_array_data);
            $('.chart').replaceWith(response.html);
            var str = json.options.chart_type.toString();
            new Chart(document.getElementById('c1').getContext('2d'))[str](json.data_array[0].chart_data,json.options);

        });
    });

    //go back button
    $(".goback").live( "click", function() {
        window.location.href = $(this).data('url');
    });


    //autoupdate
    /* TODO: add autoupdate feature
    $('#frm_chart_update').change(function() {

        var data = {
            'action': 'gc_chart_save_input',
            'id': $(this).attr('id'),
            'value': $(this).attr('value')
        };
        $.post(ajaxurl, data, function(response) {
            console.dir(response);
            //$('#btn_'+file).removeClass('loading');
        });



        //$('#update').html('This is ' + $('#choose').val() + ' and other info');



    });

*/


    $( ".new" ).click(function() {
        window.location.href = '/wp-admin/admin.php?page=cap-graphics-new-'+$(this).data('type');
    });


    /**
     *
     * @param msg
     */
    function msg(message,delay,type) {
        $.notify({
            message: message
        },{
            type: 'pastel-'+type,
            delay: delay,
            template: '<div data-notify="container" class="col-xs-11 col-sm-3 alert alert-{0}" role="alert">' +
            '<span data-notify="title">{1}</span>' +
            '<span data-notify="message">{2}</span>' +
            '</div>'
        });
    }
    /**
     * Popup with confirmation
     * @param question
     * @returns {*}
     */
    function confirmation(question) {
        var defer = $.Deferred();
        $('<div></div>')
            .html(question)
            .dialog({
                autoOpen: true,
                modal: true,
                title: 'Confirmation',
                buttons: {
                    "Yes": function () {
                        defer.resolve("true")
                        $(this).dialog("close");
                    },
                    "No": function () {
                        defer.resolve("false");//this text 'false' can be anything. But for this usage, it should be true or false.
                        $(this).dialog("close");
                    }
                },
                close: function () {
                    $(this).remove();
                }
            });
        return defer.promise();
    }

    /**
     *
     * @param cnt
     * @returns {{}}
     */
    function create_object(cnt) {

        var obj = {};
        $.each($(cnt).serializeArray(), function(_, kv) {
            if (obj.hasOwnProperty(kv.name)) {
                obj[kv.name] = $.makeArray(obj[kv.name]);
                obj[kv.name].push(kv.value);
            }
            else {
                obj[kv.name] = kv.value;
            }
        });
        return obj;
    }

});

function checkColor() {
    if(typeof myfunc == 'wpColorPicker'){
        console.log("exist");
    }else{
        jQuery(".colorpicker").wpColorPicker();
    }
}

/**
 * check for valid slug, clean and return valid slug if possible
 * @param str
 * @returns {*}
 */
function validSlug(str) {
    str = str.replace(/[^a-zA-Z0-9\s]/g,"_");
    str = str.toLowerCase();
    str = str.replace(/\s/g,'_');
    return str;
}