<script src="https://code.jquery.com/ui/1.11.3/jquery-ui.min.js"></script>
<link rel='stylesheet' href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.4/jquery-ui.css" type='text/css'  />
<div class="wrap">
    <div class="message"></div>
    <h2>Current SVG Graphics</h2>
    <div id="list_assets" class="svg_admin">
        <ul class="l">
            <?php $i=1; foreach($data['packages'] as $svg): ?>
                <li data-slug="<?php echo $svg['slug']; ?>" class="current_svg" id="l-<?php echo $i; ?>">
                    <h3><?php echo $svg['name']; ?></h3>
                    <p><?php echo $svg['description']; ?></p>
                    <input type="text" value='[cap_svg svg="<?php echo $svg['slug']; ?>"]' class="shortcode" />
                    <div class="meta">
                        <ul>
                            <li class="edit" data-i="<?php echo $i; ?>"><i class="icon icon-pencil2"></i>  edit</li>
                            <li class="copy" data-i="<?php echo $i; ?>"><i class="icon icon-copy"></i>  copy</li>
                            <li class="delete" data-i="<?php echo $i; ?>" data-type="svg"><i class="icon icon-bin"></i>  delete</li>
                        </ul>
                    </div>
                </li>
            <?php $i++; endforeach; ?>
        </ul>
    </div>
    <div id="svg_content"></div>
</div>