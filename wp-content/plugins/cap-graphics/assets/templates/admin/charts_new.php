<h1><?php echo $data['h1']; ?></h1>


<div id="list_assets">
    <ul>
        <?php foreach($data['charts'] as $key=>$chart): ?>
            <li data-type="<?php echo $key; ?>">
                <h3><?php echo $chart['label']; ?></h3>
                <img src="<?php echo $chart['img']; ?>" alt="<?php echo $chart['label']; ?>" />
            </li>
        <?php endforeach; ?>
    </ul>

</div>