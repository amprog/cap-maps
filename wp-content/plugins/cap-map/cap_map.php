<?php
/*
Plugin Name: Cap Map
Plugin URI: http://portfolio.amir-meshkin.com
Description: Custom svg map and charts plugin
Version: .7
Author: Amir Meshkin
Author URI: http://portfolio.amir-meshkin.com
*/

class Cap_Map {

    /**
     *
     */
    function __construct() {


        //set variables
        $this->plugin_name = 'CAP MAP';
        $this->slug        = 'cap_map';
        $this->namespace   = dirname(basename(__FILE__));
        $this->plugin_uri  = '/wp-content/plugins/cap-map/'; //get this from constant set in main class

        // PHP5 only
        if(!version_compare(PHP_VERSION, '5.0.0', '>=')) {
            add_action('admin_notices', 'capmaperror');
            function capmaperror() {
                $out = '<div class="error" id="messages"><p>';
                $out .= 'This plugin requires PHP5. Your server is running PHP4. Please ask your hosting company to upgrade your server to PHP5. It should be free.';
                $out .= '</p></div>';
                echo $out;
            }
            return;
        }

    }

    /**
     *
     */
    function settings_init() {
        register_setting( 'wp_cap_map', 'cap_map_options', array(&$this, 'sanitize_settings') );
    }

    /**
     * @param $input
     *
     * @return mixed
     */
    function sanitize_settings($input) {
        return $input;
    }

    /**
     * @param $links
     * @param $file
     *
     * @return mixed
     */
    function settings_link( $links, $file ) {
        static $this_plugin;
        if( ! $this_plugin ) $this_plugin = plugin_basename(__FILE__);
        if ( $file == $this_plugin ) {
            $settings_link = '<a href="' . admin_url( 'options-general.php?page=cap_map_options' ) . '">' . __('Settings', 'cap_map_options') . '</a>';
            array_unshift( $links, $settings_link ); // before other links
        }
        return $links;
    }

    /**
     *
     */
    function cap_map_options_admin() {
        add_options_page('Cap Map', 'Cap Map', 'administrator', 'cap_map_options','Cap_Map::cap_map_admin');
    }

    /**
     *
     */
    function cap_map_admin() {
        $cap_map = new Cap_Map();  //this should not be necessary!!!!
        ?>
        <div class="wrap">
            <h2>CAP MAP - Settings and Options</h2>
            <div class="postbox-container" style="width:65%;">
                <div class="metabox-holder">
                    <div class="meta-box-sortables">
                        <form action="options.php" method="post">
                            <p>namespace:<?php //echo $this->this_plugin; ?></p>

                            <p>plugin url: <?php echo $cap_map->plugin_uri; ?></p>


                        </form>
                    </div>
                </div>
            </div>


        </div>
        <?php
    }

    /**
     * @return string
     */
    function configuration() {

        $html = <<<EOD
	<h4>CAP MAP HELP</h4>

EOD;
        return $html;
    }


    /**
     * Meta boxes for picking svg and charts
     */
    function cap_map_meta()
    {

        $cap_map = new Cap_Map();  //this should not be necessary!!!!

        if (is_admin() ) {
            wp_enqueue_style( $cap_map->namespace.'-admin', $cap_map->plugin_uri.'assets/css/admin.css');
            wp_enqueue_script( $cap_map->namespace.'-admin', $cap_map->plugin_uri.'assets/js/admin.js');
        }
        $title1        = 'SVG Maps';
        $callback1     = 'Cap_Map::cap_map_svg_callback';
        $title2        = 'Charts';
        $callback2     = 'Cap_Map::cap_map_chart_callback';
        $context       = 'normal';
        $priority      = 'high';


        add_meta_box( 'svg-meta-post', $title1, $callback1, 'page', $context, $priority);  //svg meta box for pages
        add_meta_box( 'svg-meta-page', $title1, $callback1, 'post', $context, $priority); //svg meta box for posts


        add_meta_box( 'chart-meta-post', $title2, $callback2, 'page', $context, $priority);  //chart meta box for pages
        add_meta_box( 'chart-meta-page', $title2, $callback2, 'post', $context, $priority); //chart meta box for posts
    }


    /**
     * Custom callback function for svg box
     */
    function cap_map_svg_callback($post) {

        $cap_map          = new Cap_Map();  //this should not be necessary!!!!
        $folder           = ABSPATH.$cap_map->plugin_uri.'svg/';
        $handle           = opendir($folder);
        $svg_select       = esc_attr(get_post_meta( $post->ID, 'svg_select', true ));


        wp_nonce_field( 'cap_map_meta_save', 'admin_meta_box_nonce' );

        ?>
        <p class="note">Place the following short code where you want an SVG file to appear: [cap_svg]</p>

        <ul class="list">
            <li class="svg_li">
                <p>TODO: ALLOW A USER TO UPLOAD SVG, JS, AND JSON FILES FROM HERE!  IF THAT WERE POSSIBLE, NO ROLLS NEEDED!</p>
                <span>Select SVG File</span>
                <select class="svg_select" name="svg_select">
                    <option>Select One</option>
                    <?php while (false !== ($entry = readdir($handle))): ?>
                        <?php   if ($entry != "." && $entry != ".." && !is_dir($folder.$entry)): ?>
                            <?php if($svg_select==$entry): ?>
                                <option value="<?php echo $entry; ?>" selected><?php echo $entry; ?></option>
                            <?php else: ?>
                                <option value="<?php echo $entry; ?>"><?php echo $entry; ?></option>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endwhile; closedir($handle); ?>
                </select>
            </li>



            <!--
            <li>
                <span>Header</span>
                <input type="text" id="header" name="header" placeholder="Enter Header Text" value="<?php echo $header; ?>" />
            </li>
            <li>
                <span>Paragraph</span>
                <textarea id="text" name="text"><?php echo $text; ?></textarea>
            </li>
            -->
        </ul>

        <div id="chart_live" class="h">
            <h3>Chart Results</h3>



        </div>

        <?php

    }


    /**
     * Custom callback function for CHARTS box
     */
    function cap_map_chart_callback($post) {

        $cap_map          = new Cap_Map();  //this should not be necessary!!!!
        $folder           = ABSPATH.$cap_map->plugin_uri.'charts/';
        $handle           = opendir($folder);
        $chart_select     = esc_attr(get_post_meta( $post->ID, 'chart_select', true ));


        wp_nonce_field( 'cap_map_meta_save', 'admin_meta_box_nonce' );

        $chart_types = array(
            'Doughnut',
            'Pie',
            'Line',
            'LinePie',
            'bar',
            'Radar'
        );

        ?>
        <p class="note">If you need an svg file or a chart for this page, please insert the following short code where you want it to appear: [capmap]</p>
        <ul class="list">
            <li class="chart_li">
                <p><a href="javascript:void(0);" class="create" data-type="chart_new">Create new chart</a> or select one from the list below. </p>
                <span>Select Existing Chart</span>
                <select class="chart_select" name="chart_select">
                    <option>Select One</option>
                    <?php while (false !== ($entry = readdir($handle))): ?>
                        <?php  if ($entry != "." && $entry != ".." && $entry != 'starter' && is_dir($folder.$entry)): ?>
                            <?php if($chart_select==$entry): ?>
                                <option value="<?php echo $entry; ?>" selected><?php echo $entry; ?></option>
                            <?php else: ?>
                                <option value="<?php echo $entry; ?>"><?php echo $entry; ?></option>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endwhile; closedir($handle); ?>
                </select>
                <div id="chart_new" class="h">

                    <ul>
                        <li>
                            <span>Chart Name</span>
                            <input type="text" name="chart_name" id="chart_name" placeholder="Enter a Chart Name" />

                        </li>
                        <li>
                            <span>Chart Slug</span>
                            <input type="text" name="chart_slug" id="chart_slug" placeholder="Slug contains no spaces" />

                        </li>

                        <li>
                            <span>Select Chart Type</span>
                            <select class="chart_select" name="chart_select">
                                <option>Select One</option>
                                <?php foreach ($chart_types as $c): ?>
                                    <?php if($chart_select==$c): ?>
                                        <option selected><?php echo $c; ?></option>
                                    <?php else: ?>
                                        <option><?php echo $c; ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </li>

                        <!--
                        <li>
                            <span>JSON Code</span>
                            <textarea id="chart_json" name="chart_json"></textarea>
                        </li>
                        -->
                    </ul>




                    <input type="button" id="chart_submit" value="Create New Chart" />
                </div>

            </li>

        </ul>

        <div id="chart_live" class="h">
            <h3>Chart Results</h3>



        </div>

        <?php

    }


    /**
     * Save custom meta box
     * @param $post_id
     */
    function cap_map_meta_save() {

        /*
         * We need to verify this came from our screen and with proper authorization,
         * because the save_post action can be triggered at other times.
         */

        // Check if our nonce is set.
        if ( ! isset( $_POST['admin_meta_box_nonce'] ) ) {
            //exit;
            return;
        }

        // Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $_POST['admin_meta_box_nonce'], 'cap_map_meta_save' ) ) {
            return;
        }



        // autosave
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        // make sure we have permission
        if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

            if ( ! current_user_can( 'edit_page',  $_POST['ID'] ) ) {
                return;
            }

        } else {

            if ( ! current_user_can( 'edit_post',  $_POST['ID'] ) ) {
                return;
            }
        }

        // sanitize and save data
        update_post_meta( $_POST['ID'], 'svg_select',  sanitize_text_field($_POST['svg_select']));
        update_post_meta( $_POST['ID'], 'chart_select',  sanitize_text_field($_POST['chart_select']));




    }


    /**
     * Return SVG depending on options selected for this ID
     * @param $atts
     */
    function cap_map_svg_shortcode( $atts ){
        //TODO: think about putting every fiule, json, js, css into one "package" or in ONE folder

        $cap_map  = new cap_map;
        $id       = get_the_ID();
        $svg_raw  = get_post_meta($id,'svg_select',true);
        $content  = '';

        //always include front end css
        wp_enqueue_style('capmapcss', $cap_map->plugin_uri.'assets/css/frontend.css');

        if($svg_raw != 'Select One') {
            //include js and css IF exists
            $svg_file     = ABSPATH.$cap_map->plugin_uri.'svg/'.$svg_raw;
            $custom_js    = $cap_map->plugin_uri.'assets/js/svg/'.str_replace('.svg','.js',$svg_raw);
            $custom_css   = $cap_map->plugin_uri.'assets/css/svg/'.str_replace('.svg','.css',$svg_raw);
            if(file_exists(ABSPATH.$custom_js)) {
                wp_enqueue_script('js-'.$id,  $custom_js,'','1',true);
            }

            if(file_exists(ABSPATH.$custom_css)) {
                wp_enqueue_style('css-'.$id, $custom_css);
            }

            $content .= '<div class="svg_wrap"><div class="svg_meta"></div>';
            $content .=  file_get_contents($svg_file);
            $content .=  '</div>';

        }


        $chart_page  = get_post_meta($id,'chart_select',true);
        if($svg_raw != 'Select One') {

            wp_enqueue_script('charts',  $cap_map->plugin_uri.'assets/js/common/Chart.min.js','','1',true);
            wp_enqueue_script('charts',  $cap_map->plugin_uri.'assets/js/common/charts.options.js','','1',true);


            //get chart type

            $content .= '<canvas id="c1" height="248" width="497" style="width: 497px; height: 248px;"></canvas>';



        }


        /*
        //instead of php build php here
        if(file_exists($svg_file)) {
            include($svg_file);
        } else {
            echo 'no svg file found';
        }


        echo $id;
        */


        return $content;
    }

    /**
     * Return chart depending on options selected for this ID
     * @param $atts
     */
    function cap_map_chart_shortcode( $atts ){
        //TODO: think about putting every fiule, json, js, css into one "package" or in ONE folder

        $cap_map  = new cap_map;
        $id       = get_the_ID();
        $svg_raw  = get_post_meta($id,'svg_select',true);
        $content  = '';

        //always include front end css
        wp_enqueue_style('capmapcss', $cap_map->plugin_uri.'assets/css/frontend.css');

        if($svg_raw != 'Select One') {
            //include js and css IF exists
            $svg_file     = ABSPATH.$cap_map->plugin_uri.'svg/'.$svg_raw;
            $custom_js    = $cap_map->plugin_uri.'assets/js/svg/'.str_replace('.svg','.js',$svg_raw);
            $custom_css   = $cap_map->plugin_uri.'assets/css/svg/'.str_replace('.svg','.css',$svg_raw);
            if(file_exists(ABSPATH.$custom_js)) {
                wp_enqueue_script('js-'.$id,  $custom_js,'','1',true);
            }

            if(file_exists(ABSPATH.$custom_css)) {
                wp_enqueue_style('css-'.$id, $custom_css);
            }

            $content .= '<div class="svg_wrap"><div class="svg_meta"></div>';
            $content .=  file_get_contents($svg_file);
            $content .=  '</div>';

        }


        $chart_page  = get_post_meta($id,'chart_select',true);
        if($svg_raw != 'Select One') {

            wp_enqueue_script('charts',  $cap_map->plugin_uri.'assets/js/common/Chart.min.js','','1',true);
            wp_enqueue_script('charts',  $cap_map->plugin_uri.'assets/js/common/charts.options.js','','1',true);


            //get chart type



            $content .= '<canvas id="c1" height="248" width="497" style="width: 497px; height: 248px;"></canvas>';



        }


        /*
        //instead of php build php here
        if(file_exists($svg_file)) {
            include($svg_file);
        } else {
            echo 'no svg file found';
        }


        echo $id;
        */


        return $content;
    }
}





/**
 *
 */
function init_cap_map() {

    $cap_cap = new Cap_Map();

    /*
    if(method_exists('Cap_Map', 'Cap_Map')) {
        //$cap_cap = new Cap_Map();
    }
    */
}

add_action('plugins_loaded', 'init_cap_map');
add_action( 'admin_menu', 'Cap_Map::cap_map_options_admin' );
add_action("add_meta_boxes", "Cap_Map::cap_map_meta");
add_action( 'save_post', 'Cap_Map::cap_map_meta_save' );  //this is causing problems with new post pages
add_shortcode( 'cap_svg', 'Cap_Map::cap_map_svg_shortcode' );  //register shortcode for svg
add_shortcode( 'cap_chart', 'Cap_Map::cap_map_chart_shortcode' );  //register shortcode
?>