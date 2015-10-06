jQuery(document).ready(function( $ ) {

    $('.map_left').height($(window).height()-300);
    var region = $(".region" );
    region.hover(
        function() {
            id   = $(this).attr('id');
            poly = $('#'+id);
            fill =  poly.attr('fill');
            poly.attr('fill',"transparent");
            svg = $('#gat_svg circle, #gat_svg text');
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

    //city overlays
    $('#gat_svg').overlays();

});