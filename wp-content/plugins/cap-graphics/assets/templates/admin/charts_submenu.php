<div class="wrap">
    <h2>Charts</h2>
    <p>These are the charts currently in the system.  They can be res</p>

    <div id="list_assets">
        <ul>
            <?php foreach($data['packages']['charts'] as $chart): ?>
                <li data-type="<?php echo $chart['slug']; ?>" class="current_chart">
                    <h3><?php echo $chart['label']; ?></h3>
                    <div class="starter <?php echo $chart['type']; ?>"></div>

                    <p><?php echo $chart['description']; ?></p>

                    <input type="text" value='[cap_chart chart="<?php echo $chart['slug']; ?>"]' class="shortcode" />


                    <div class="meta">
                        <button class="view">edit</button>
                        <button class="edit">edit</button>
                        <button class="delete">delete</button>
                        <button class="copy">copy</button>
                    </div>

                </li>
            <?php endforeach; ?>
        </ul>
    </div>


</div>