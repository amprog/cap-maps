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
         *
         */
        function svg_submenu()
        {
            $data['var'] ='data var';
            $var = 'var var';



            parent::gc_get_template($data,'admin/svg_submenu.php');


            return parent::gc_chart_action_callback();

            //return parent::gc_get_template2($data,'admin/svg_submenu.php');

        }

        /**
         * Read JSON of charts already in system and return to admin
         */
        function charts_submenu()
        {
            $charts                  = file_get_contents(dirname(__FILE__).'/charts.json');
            $data['packages']        = json_decode($charts,true);
            $data['charts_js_file']  = '/wp-content/plugins/cap-graphics/assets/js/common/'; //TODO: replace all of these with proper constant
            $data['charts_css_file'] = '/wp-content/plugins/cap-graphics/assets/css/';

            $data['dir'] = parent::get_package_uri('charts');
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