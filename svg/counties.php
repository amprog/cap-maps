<?php

/**
 * Get County data from database and write all bubbles json
 */

//get data from db



$i = 0;
foreach($bubbles['chart_data'][0]['counties'] as $bubble): //foreach($bubbles['chart_data'][0]['counties'] as $bubble): //works ?>

<div id="b<?php echo $i; ?>" class="bub h">
    <h3><?php  echo $bubble['charts'][0]['county_name'];  //echo $bubbles['chart_data'][0]['counties'][0]['county_name'][$i]; ?> County</h3>
    <h4 class="l"><?php echo $bubble['charts'][0]['name']; ?></h4>
    <table>
        <tr>
            <td>Male</td>
            <td><?php echo number_format($bubble['charts'][0]['data'][0]['Male']); ?></td>
            <td><?php echo $bubble['charts'][0]['data'][1]['Male_p']; ?>%</td>
        </tr>
    </table>
</div>
<?php endforeach; ?>