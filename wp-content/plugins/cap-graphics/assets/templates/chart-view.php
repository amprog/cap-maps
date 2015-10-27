
<?php

//TODO:  getting data in here will break the plugin as far as portability.  need to use JS to create this chart
//normally this is a no no, but we get the data we need here from the slug

$json_file = '';


?>

<canvas id="c-<?php echo $data['slug']; ?>" width="<?php echo $data['width']; ?>" height="<?php echo $data['height']; ?>"></canvas>