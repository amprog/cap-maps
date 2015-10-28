{
    "options": {
        "chart_type": "Pie",
        "chart_name": "<?=$chart_name;?>",
        "segmentStrokeColor": "#0a0a0a",
        "chart_source": "http://www.wisertrade.org",
        "legend": 1,
        "source": 0,
        "name": 0,
        "width": 300,
        "height": 300,
        "animateRotate": true
    },
    "data_array": [
        {
            "chart_data": [
            <?php $i=0;foreach($chart_data as $data): ?>
                {
                    "label": "Other",
                    "value": 41,
                    "color": "#398c66",
                    "highlight": "#076f40"
                }
                <?php if($i <= count($chart_data) echo ','; } ?>
            <?php $i++; endforeach; ?>
            ]
        }
    ]
}