<?php if($data['d3']): ?>
    <script src="<?php echo $data['d3_file']; ?>custom/jquery.object.js"></script>
<?php endif; ?>
<div class="wrap">
    <h2>Current SVG Graphics</h2>
    <div id="list_assets" class="svg_admin">
        <ul class="l">
            <?php $i=1; foreach($data['packages']['svg'] as $svg): ?>
                <li data-slug="<?php echo $svg['slug']; ?>" class="current_svg" id="l-<?php echo $i; ?>">
                    <h3><?php echo $svg['label']; ?></h3>
                    <p><?php echo $svg['description']; ?></p>
                    <input type="text" value='[cap_svg svg="<?php echo $svg['slug']; ?>"]' class="shortcode" />
                    <div class="meta">
                        <ul>
                            <li class="edit" data-i="<?php echo $i; ?>"><i class="icon icon-pencil2"></i>  edit</li>
                            <li class="copy" data-i="<?php echo $i; ?>"><i class="icon icon-copy"></i>  copy</li>
                            <li class="delete" data-i="<?php echo $i; ?>"><i class="icon icon-bin"></i>  delete</li>
                        </ul>
                    </div>
                </li>
                <?php $i++; endforeach; ?>
        </ul>
    </div>
    <div id="svg_content"></div>
</div>