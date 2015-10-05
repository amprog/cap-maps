jQuery(document).ready(function( $ ) {
    $('.map_left').height($(window).height()-270);
    $(".region").fancybox({
        'type': "inline",
        'height':'95%',
        'width': '95%',
        'autoDimensions':false,
        'autoSize':false,
        'scrolling':'auto',
        'helpers': {
            'overlay': {
                'locked': false
            }
        }
    });

    $('.hide_on_load').hide();
});