<div class="wrap">
    <h2>Charts</h2>
    <p>These are the charts currently in the system.  They can be res</p>

    <div id="list_assets">
        <ul class="l">
            <?php foreach($data['packages']['charts'] as $chart): ?>
                <li data-type="<?php echo $chart['slug']; ?>" class="current_chart">
                    <h3><?php echo $chart['label']; ?></h3>
                    <div class="starter <?php echo $chart['type']; ?>"></div>
                    <p><?php echo $chart['description']; ?></p>
                    <input type="text" value='[cap_chart chart="<?php echo $chart['slug']; ?>"]' class="shortcode" />

                    <div class="meta">
                        <ul>
                            <li class="view"><i class="icon icon-eye"></i>  view</li>
                            <li class="edit"><i class="icon icon-pencil2"></i>  edit</li>
                            <li class="copy"><i class="icon icon-copy"></i>  copy</li>
                            <li class="delete"><i class="icon icon-bin"></i>  delete</li>
                        </ul>
                    </div>

                </li>
            <?php endforeach; ?>
        </ul>
    </div>


</div>