<h1><?php echo $data['h1']; ?></h1>
<p>Select the type of chart you want to create.  More chart types will be added shortly.</p>

<div id="list_assets">
    <ul class="l">
        <?php foreach($data['charts'] as $key=>$chart): ?>
            <li data-type="<?php echo $key; ?>" class="new_chart">
                <a href="javascript:void(0);">
                    <h3><?php echo $chart['label']; ?></h3>
                    <div class="starter <?php echo $key; ?>"></div>
                    <div class="meta">
                        <p>Create New</p>
                    </div>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

