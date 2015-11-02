<div class="main_wrap">
    <h2>CAP GRAPHICS</h2>
    <p>This plugin will allow you to easily create charts and svg graphics using the wordpress admin.  There are two seperate pages for different types of graphics you can create using this plugin.</p>


    <div class="c_1_1">
        <div class="left">
            <h3>Charts</h3>
            <p>Anyone will be able to create the charts, using simple forms.  Multiple charts should work on the same page.  You will soon be able to build more complicated charts in the near future.</p>
            <button class="new" data-type="chart">New Chart</button>
        </div>


        <div class="left">
            <canvas id="chart" width="600" height="300"></canvas>
        </div>
    </div>

    <div class="left">
        <h3>SVG Maps and Graphics</h3>
        <p>You will still need a developer to write code to create SVG graphics.  But they can now be added using the wordpress admin, which means that hosting and maintenance will be a lot earlier. You will also not have to wait for any deployments, since you can upload your files as soon as the developer finishes it.  </p>
        <button class="new" data-type="svg">New SVG Graphic</button>
    </div>

    <div class="left">
        <?php  echo Cap_Graphics::gc_svg_shortcode( array('svg'=>'electoral') );    //echo do_shortcode('[cap_svg svg="electoral"]'); ?>
    </div>
    <script src="/wp-content/plugins/cap-graphics/assets/js/common/Chart.min.js"></script>
    <script>
        jQuery(document).ready(function($) {
            $.getJSON('/wp-content/plugins/cap-graphics/assets/templates/admin/home.json')
                .done(function( data ) {
                    var data = {
                        labels: ["January", "February", "March", "April", "May", "June", "July"],
                        datasets: [
                            {
                                label: "My First dataset",
                                fillColor: "rgba(220,220,220,0.5)",
                                strokeColor: "rgba(220,220,220,0.8)",
                                highlightFill: "rgba(220,220,220,0.75)",
                                highlightStroke: "rgba(220,220,220,1)",
                                data: [65, 59, 80, 81, 56, 55, 40]
                            },
                            {
                                label: "My Second dataset",
                                fillColor: "rgba(151,187,205,0.5)",
                                strokeColor: "rgba(151,187,205,0.8)",
                                highlightFill: "rgba(151,187,205,0.75)",
                                highlightStroke: "rgba(151,187,205,1)",
                                data: [28, 48, 40, 19, 86, 27, 90]
                            }
                        ]
                    };
                    new Chart(document.getElementById("chart").getContext("2d")).Bar(data, data.options);
                })
                .fail(function( jqxhr, textStatus, error ) {
                    var err = textStatus + ", " + error;
                    console.log( "Request Failed: " + err );
                });
            });
    </script>


</div>