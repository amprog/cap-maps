<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css" />
<link rel="stylesheet" href="<?php echo $data['charts_css_file']; ?>/jquery.fancybox.css" />
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script src="<?php echo $data['charts_js_file']; ?>/Chart.min.js"></script>
<script src="<?php echo $data['charts_js_file']; ?>/jquery.fancybox.pack.js"></script>
<div class="wrap">
    <h2>Charts</h2>
    <p>These are the charts currently in the system.  You should be able to view, edit, and copy any of them.  Only certain roles will be able to delete charts.</p>
    TODO: floating save button and live chart on the right hand side
    <div id="list_assets">
        <ul class="l">
            <?php $i=1; foreach($data['packages']['charts'] as $chart): ?>
                <li data-slug="<?php echo $chart['slug']; ?>" class="current_chart" id="l-<?php echo $i; ?>">
                    <h3><?php echo $chart['label']; ?></h3>
                    <div class="starter <?php echo $chart['type']; ?>"></div>
                    <p><?php echo $chart['description']; ?></p>
                    <input type="text" value='[cap_chart chart="<?php echo $chart['slug']; ?>"]' class="shortcode" />

                    <div class="meta">
                        <ul>
                            <li class="view" data-i="<?php echo $i; ?>" href=""><i class="icon icon-eye"></i>  view</li>
                            <li class="edit" data-i="<?php echo $i; ?>"><i class="icon icon-pencil2"></i>  edit</li>
                            <li class="copy" data-i="<?php echo $i; ?>"><i class="icon icon-copy"></i>  copy</li>
                            <li class="delete" data-i="<?php echo $i; ?>"><i class="icon icon-bin"></i>  delete</li>
                        </ul>
                    </div>

                </li>
            <?php $i++; endforeach; ?>
        </ul>
    </div>
</div>
<div id="hidden-info">
    <input type="hidden" id="package_dir" value="<?php echo $data['package_dir']; ?>" />
</div>