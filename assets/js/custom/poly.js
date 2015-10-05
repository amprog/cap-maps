




var p1 = paper.polyline([10, 10, 100, 100]);
var p2 = paper.polyline(10, 10, 100, 100);

var s = Snap("#svgout");
var g = s.group();
var tux = Snap.load("Dreaming_tux.svg", function ( loadedFragment ) {
    g.append( loadedFragment );
    g.hover( hoverover, hoverout );
    g.text(300,100, 'hover over me');
} );

var hoverover = function() { g.animate({ transform: 's2r45,150,150' }, 1000, mina.bounce ) };
var hoverout = function() { g.animate({ transform: 's1r0,150,150' }, 1000, mina.bounce ) };