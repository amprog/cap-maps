(function( $ ) {

    $.fn.overlays = function() {
        var el = this[0];
        //json of cities
        $.getJSON( "/wp-content/plugins/svg_map/data/2015/cities.json")
            .done(function( json ) {
                var s = Snap('#'+el.id);
                $.each(json.cities[0], function( index, c ) {
                    if(!c.capital) {

                        var city = s.circle(c.x, c.y, c.size);
                        city.attr({
                            fill: c.fill,
                            stroke: c.stroke,
                            strokeWidth: c.strokeWidth,
                            class: 'zone'
                        });
                        t = s.text(c.name_x, c.name_y, [c.name]);
                    } else {
                        s.image(c.capital, c.x-30,c.y-10, 60, 30);
                    }
                });
            })
            .fail(function( jqxhr, textStatus, error ) {
                var err = textStatus + ", " + error;
                console.log( "Request Failed: " + err );
            });
    }
})(jQuery);