<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css" />
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script src="<?php echo $data['charts_js_file']; ?>common/Chart.min.js"></script>
<script src="<?php echo $data['charts_js_file']; ?>custom/jquery.object.js"></script>
<div class="wrap">
    <div class="message"></div>
    <h2>Current Charts</h2>
    <div id="list_assets" class="charts_admin">
        <ul class="l">
            <?php $i=1; foreach($data['packages'] as $chart): ?>
                <li data-slug="<?php echo $chart['slug']; ?>" class="current_chart" id="l-<?php echo $i; ?>">
                    <h3><?php echo $chart['name']; ?></h3>
                    <div class="starter <?php echo $chart['type']; ?>"></div>
                    <p><?php echo $chart['description']; ?></p>
                    <input type="text" value='[cap_chart chart="<?php echo $chart['slug']; ?>"]' class="shortcode" />

                    <div class="meta">
                        <ul>
                            <!--<li class="view" data-i="<?php echo $i; ?>" data-dir="<?php echo $data['dir']; ?>"><i class="icon icon-eye"></i>  view</li>-->
                            <li class="edit" data-i="<?php echo $i; ?>" data-type="<?php echo $chart['type']; ?>"><i class="icon icon-pencil2"></i>  edit</li>
                            <li class="copy" data-i="<?php echo $i; ?>"><i class="icon icon-copy"></i>  copy</li>
                            <li class="delete" data-i="<?php echo $i; ?>" data-type="charts"><i class="icon icon-bin"></i>  delete</li>
                        </ul>
                    </div>

                </li>
            <?php $i++; endforeach; ?>
        </ul>
    </div>
</div>