<?php



$json1     = file_get_contents('linepie.json');
//$linepie1 = json_decode(file_get_contents('wp-content/plugins/svg_map/data/2015/9266-linepie.json'));

?>

<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>

<script src="jquery.linepie.js"></script>

<div class="full_container">
    <div id="wide_page">
        <section>
            <h1>Chart Wrapper Tests and Code</h1>


            <h3>Line Pie Chart</h3>
            <div class="chart">
                <canvas id="linepie1" width="400" height="400"></canvas>
            </div>

            <input id="range" type="range" min="0" max="100" />
            <canvas id="counter" width="240" height="240"></canvas>



        </section>
    </div>
</div>
<script>
    jQuery(document).ready(function($) {
        $("#linepie1").chartWrap(<?php echo $json1; ?>);
    });
</script>
