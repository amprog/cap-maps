<div class="wrap">
    <?php echo "<p>var: $var</p>"; echo $data['var']; ?>
    svg_Submenu
    <ul class="list">
        <li class="svg_li">
            <div id="svg_select_wrap">
                <div class="left">
                    <span class="loading h"></span>
                        <span id="svg_select_inner">
                            <span><strong>Select SVG Graphic</strong></span>
                            <select class="svg_select" name="svg_select">
                                <option>Select One</option>
                                <?php foreach($packages->svg as $package): ?>
                                    <?php if($svg_select==$package->slug): ?>
                                        <option value="<?php echo $package->slug; ?>" selected><?php echo $package->label; ?></option>
                                    <?php else: ?>
                                        <option value="<?php echo $package->slug; ?>"><?php echo $package->label; ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </span>
                </div>
                <div class="left r">
                    <button type="button" class="create" data-type="svg">NEW SVG GRAPHIC</button>
                </div>
            </div>
            <div id="svg_slug_wrap"></div>
            <div id="svg_new"><?php echo $svg_new; ?></div>
            <input type="hidden" id="ID" value="<?php echo $post->ID; ?>" />
        </li>
    </ul>
    <div class="note">
        <p>Place the following short code where you want an SVG file to appear: [cap_svg]</p>
        <p>Place multiple charts on the page by using the svg slug: [cap_svg svg="PACKAGE_SLUG"]</p>
    </div>
</div>