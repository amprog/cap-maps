jQuery(document).ready(function($) {

    $('#slider1').flexslider({
        animation: "slide",
        controlNav: true,
        smoothHeight: true,
        directionNav: false,
        slideshowSpeed: 5000,
        after: function(){
            $('#slider_meta1 .meta_slide').addClass('h');
            $('#'+$('#slider1 .flex-active-slide').attr('rel')).removeClass('h');
        }
    });

    $('#slider2').flexslider({
        animation: "slide",
        controlNav: false,
        smoothHeight: true,
        directionNav: true,
        slideshow: false,
        after: function(){
            $('#slider_meta2 .meta_slide').addClass('h');
            $('#'+$('#slider2 .flex-active-slide').attr('rel')).removeClass('h');
        }
    });

    //top
    $( "#slider1 .flex-control-paging li a" ).live( "click", function() {
        var slide = this.innerText;
        $('#slider_meta1 .meta_slide').addClass('h');
        $('#slider_meta1 #meta-'+slide).removeClass('h');
    });

    //nbottom
    $( "#slider2 .flex-direction-nav" ).live( "click", function() {
        var slide = $('.industry_slider .flex-active-slide').attr('data-num');
        $('#slider_meta2 .meta_slide').addClass('h');
        $('#slider_meta2 #meta2-'+slide).removeClass('h');
    });
});
