jQuery(document).ready(function( $ ) {

    $('.map_left').height($(window).height()-270);

    var region = $(".region" );
    region.hover(
        function() {
            id   = $(this).attr('id');
            poly = $('#'+id);
            fill =  poly.attr('fill');
            poly.attr('fill',"transparent");
            svg = $('#ren_svg circle, #ren_svg text');
            svg.hide();
        }, function() {
            poly.attr('fill',fill);
            svg.show();
        }
    );

    region.fancybox({
        'type': 'ajax',
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

    $('.hide_on_hover').fadeOut();
    $('#ren_svg').overlays();

});