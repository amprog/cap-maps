jQuery(document).ready(function( $ ) {
    $('.wpcf7-range').on("change mousemove", function() {
        $('#range_results').html($(this).val());
    });
});
