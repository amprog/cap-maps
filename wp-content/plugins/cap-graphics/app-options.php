<?php
if ( !defined( 'ABSPATH' ) ) die(); //keep from direct access
if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

if (!class_exists("Cap_Graphics_Options")) {

    class Cap_Graphics_Options extends Cap_Graphics
    {

        var $page = '';
        var $message = 0;

        function __construct()
        {




        }

        /**
         * Build options page
         */
        function option_function()
        {

            $messages[1] = __('Action Taken.', parent::APP_SLUG);

            if (isset($_GET['message']) && (int)$_GET['message']) {
                $message = $messages[$_GET['message']];
                $_SERVER['REQUEST_URI'] = remove_query_arg(array('message'), $_SERVER['REQUEST_URI']);
            }

            $title = __(parent::SETTINGS_PAGE_TITLE, parent::APP_SLUG);
            ?>
            <div class="wrap">
                <h2><?php echo esc_html($title); ?></h2>

                <?php
                if (!empty($message)) :
                    ?>
                    <div id="message" class="updated fade"><p><?php echo $message; ?></p></div>
                    <?php
                endif;
                ?>

                <form method="post" action="options.php">
                    <?php
                    settings_fields(parent::OPTIONS_PAGE);
                    do_settings_sections(parent::SETTINGS_PAGE1);
                    ?>
                    <p>
                        <input type="submit" class="button button-primary" name="save_options" value="<?php esc_attr_e('Save Options'); ?>"/>
                    </p>
                </form>
                <p class="note"><?php print parent::APP_NAME; ?> by Amir Meshkin
                    version <?php print parent::APP_VERSION; ?></p>
                <span> debug: <?php print parent::APP_DEBUG; ?></span>
            </div>
            <?php
        }


        /**
         * Instructions page
         */
        function instruction_submenu()
        {
            echo ABSPATH . parent::APP_DIR . '/assets/templates/home.php';
            $var = 'balsgdf saldgf sd';
            include(WP_CONTENT_DIR . parent::PLUGIN_DIR . parent::APP_DIR . '/assets/templates/admin/home.php');
            // echo Cap_Graphics_Frontend::get_template($data,'admin/home.php'); //this one not working
        }

        /**
         * Return svg packages with status of 1
         */
        function svg_submenu()
        {

            $svg  = parent::gc_get_package('svg');
            $json = json_decode($svg,true);
            $new  = array();
            foreach($json['svg'] as $k=>$v) { error_log(print_r($v,true));
                if($v['status']==1) {
                    $new['svg'][] = $v;
                }
            }
            $data['packages']   = $new;
            return parent::gc_get_template($data,'admin/svg_submenu.php');
        }

        /**
         * Read JSON of charts already in system and return to admin
         */
        function charts_submenu()
        {
            $charts = parent::gc_get_package('charts');
            $json   = json_decode($charts,true);
            $new    = array();
            foreach($json['charts'] as $k=>$v) { error_log(print_r($v,true));
                if($v['status']==1) {
                    $new['charts'][] = $v;
                }
            }
            $data['packages']        = $new;
            $data['charts_js_file']  = '/wp-content/plugins/cap-graphics/assets/js/'; //TODO: replace all of these with proper constant
            $data['charts_css_file'] = '/wp-content/plugins/cap-graphics/assets/css/';

            return parent::gc_get_template($data,'admin/charts_submenu.php');
        }


        /**
         * Create a new CHART
         */
        function charts_new() {

            //check for post data, if it exists, save

            if($_POST) {
                echo '<pre>';
                 print_r($_POST);
                echo '</pre>';


                $result = parent::gc_chart_save_callback($_POST);


                echo '<pre>';
                print_r($result);
                echo '</pre>';


            } else {


                $images      =    plugin_dir_url(__FILE__).'/assets/images/';
                $data['h1']          = 'Create a New Chart';


                //add chart typs here to one array
                $data['charts']['pie']['label'] = 'Pie Chart';
                $data['charts']['pie']['img']   = $images.'pie.png';


                $data['charts']['doughnut']['label'] = 'Doughnut Chart';
                $data['charts']['doughnut']['img']   = $images.'doughnut.png';


                return parent::gc_get_template($data,'admin/charts_new.php');
            }

        }

        /**
         * Create a new CHART
         */
        function svg_new() {

            $images      =    plugin_dir_url(__FILE__).'/assets/images/';
            $data['h1']          = 'Create a New SVG Graphic';




            return parent::gc_get_template($data,'admin/svg_new.php');
        }


    }
}
/*
 *     <?php foreach($data['charts'] as $chart): ?>
        <div class="chart">
            <?php echo $chart['name']; ?>
        </div>
    <?php endforeach; ?>
 */
?>