<ul class="list">
    <li class="chart_li">
        <div id="chart_select_wrap">
            <div class="left">
                <span class="loading h"></span>
                <span>Select Existing Chart</span>
                <select class="chart_select" name="chart_select">
                    <option>Select One</option>
                    <?php foreach($packages->charts as $package): ?>
                        <?php if($chart_select==$package->slug): ?>
                            <option value="<?php echo $package->slug; ?>" selected><?php echo $package->label; ?></option>
                        <?php else: ?>
                            <option value="<?php echo $package->slug; ?>"><?php echo $package->label; ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="left r">
                <button type="button" class="create" data-type="chart">NEW CHART</button>
            </div>
        </div>
        <div id="chart_new"><?php echo $chart_new; ?></div>
    </li>
</ul>
<div  class="note">
    <p>Place the following short code where you want a CHART to appear: [cap_chart]</p>
</div>