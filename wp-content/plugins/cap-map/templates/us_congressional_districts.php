<?php
/**
 * timeline election map
 * http://www.270towin.com/
 */


$namespace  = basename(__FILE__);
$plugin_uri = 'wp-content/plugins/cap-map/'; //get this from constant set in main class


wp_enqueue_style($namespace, 'assets/css/'.$namespace.'.css' );
?>


<div class="<?php echo $namespace; ?>_wrap">
    <?php include('../us_congressional_districts.svg');  ?>
</div>


<script>
    jQuery(document).ready(function( $ ) {

        $('#<?php echo $namespace; ?>').height($(window).height()-300);
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
    });
</script>