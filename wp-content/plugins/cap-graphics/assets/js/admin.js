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


    //handle all button clicks
    $( ".meta ul li" ).live( "click", function(e) {

        var c = $(this).attr('class');
        chart = $(this).data('i');
        slug  = $(this).parent().parent().parent().data('slug');
        dir   = $(this).data('dir');

        if(c=='delete') {
            var question = "Are you sure you want to delete this chart?";
            confirmation(question).then(function (answer) {
                if(answer=='true'){
                    //TODO: run ajax that will delete chart from json
                    $('#l-'+chart).remove();
                } else {
                    console.dir('else');
                }

            });
        } else if (c=='copy') {
            //TODO: run ajax that will OPEN A LIGHTBOX, AND ALLOW COPY
            console.log('copy');

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
                    }); // fancybox
                } // success
            }); // ajax

            /*

            $.getJSON(dir+slug+'/index.json').done(function( data ) {

                json = data;

                html = '<div class="left"><h3>'+json.options.chart_name+'</h3><canvas id="f1" width="'+json.options.width+'" height="'+json.options.height+'"></canvas></div>' +
                           '<div class="left"><p class="chart_source">'+json.options.chart_source+'</p></div>'


            })
                .fail(function( jqxhr, textStatus, error ) {
                    var err = textStatus + ", " + error;
                    console.log( "Request Failed: " + err );
                });



            $.fancybox({
                'type': "ajax",
                'width':'95%',
                'height':'95%',
                'autoDimensions':false,
                'autoSize':false,
                'scrolling':'no',
                'href': '',
                'helpers': {
                    'overlay': {
                        'locked': false
                    }
                },
                afterLoad: function() {

                    console.dir(json); console.log(json.options.chart_type);
                    var str = json.options.chart_type.toString();         console.log("str: "+str);
                    new Chart(document.getElementById('f1').getContext('2d'))[str](json.data_array[0].chart_data,json.options);



                }
            });

             'content': '<div class="left"><h3>'+json.options.chart_name+'</h3><canvas id="f1" width="'+json.options.width+'" height="'+json.options.height+'"></canvas></div>' +
             '<div class="left"><p class="chart_source">'+json.options.chart_source+'</p></div>',  //TODO:  there should be a template with this data, that can also be used for shortcode!

             */

        } else if (c=='edit') {
            //TODO: run ajax that will get chart data, and replace main div with it

            var data = {
                'action': 'gc_chart_action',
                'chart_slug': $( '#l-'+chart).data('slug')
            };
            jQuery.post(ajaxurl, data, function(response) {
                $('#list_assets').html(response.html); console.dir(response);
            });

        } else {

        }

    });

    //copy shortcode
    $( ".shortcode" ).live( "click", function() {
        var copy  = $(this).addClass('highlight');
        var short = copy.val();
        copy.val('copied!');
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val($(this).val()).select();
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

    //charting
    /* TODO: MAY NOT NEED OR WILL USE THIS ANY MORE
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
     */



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
        var chart_type = $(this).data('type');
        if(chart_type) {
            var data = {
                'action': 'gc_chart_line_action',
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
    /* TODO: may not need this any more
    $( "#post" ).submit(function( event ) {
        if($('#chart_action').val()=='new'  && $('.chart_type option:selected').val() == 'Select One') {
            event.preventDefault();
            $('.chart_type').addClass('error');
            window.location.hash='#chart_type_anchor';
            alert("If adding a new chart please make sure to select one from the dropdown!");
        }
        //event.preventDefault();
    });

    */



    //update canvas and save
    $( ".chart_update" ).live( "click", function() {



        //obj = create_object('#frm_chart_update');  //manhandles the array

        //obj = $('#frm_chart_update').serializeArray;  //does not get 3d array

        json =  JSON.stringify(obj);

        console.dir(json);

        var data = {
            'action': 'gc_chart_save',
            'data': json
        };
        $.post(ajaxurl, data, function(response) {
            console.dir(response);
            //$('#btn_'+file).removeClass('loading');
        });
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
                    "Yes": function () {//TODO: build delete functionality
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
