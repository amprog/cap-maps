<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css" />
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script src="<?php echo $data['charts_js_file']; ?>common/Chart.min.js"></script>
<script src="<?php echo $data['charts_js_file']; ?>custom/jquery.object.js"></script>
<h1><?php echo $data['h1']; ?></h1>
<p>Select the type of chart you want to create.  More chart types will be added shortly.</p>

<div id="list_assets">
    <ul class="l">
        <?php foreach($data['charts'] as $key=>$chart): ?>
            <li data-type="<?php echo $key; ?>" class="create">
                <a href="javascript:void(0);">
                    <h3><?php echo $chart['label']; ?></h3>
                    <div class="starter <?php echo strtolower($key); ?>"></div>
                    <div class="meta">
                        <p>Create New</p>
                    </div>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

