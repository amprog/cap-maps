<?php

if ( !defined( 'ABSPATH' ) ) die(); //keep from direct access

define('APP_CLASS_NAME', 'Cap_Graphics');

if (!class_exists(APP_CLASS_NAME)) {

    class Cap_Graphics
    {
        const APP_VERSION             = '1.0';
        const APP_DEBUG               = 1;
        const APP_NAMESPACE           = 'gc_';
        const APP_LOADER              = 'cap-graphics/app-loader.php';
        const APP_NAME                = 'CAP Graphics';
        const APP_SLUG                = 'cap-graphics';
        const PLUGIN_DIR              = '/plugins';
        //const APP_DIR                 = ABSPATH.self::PLUGIN_DIR.'/cap-graphics';
        const APP_DIR                 = '/cap-graphics';
        const SETTINGS_SECTION_ID     = 'cg_main';
        const OPTIONS_PAGE            = 'cg_options_page';
        const OPTIONS_PREFIX          = 'cg_options';
        const APP_OPTION_CLASS_NAME   = 'Cap_Graphics_Options';
        const SETTINGS_PAGE_TITLE     = 'CAP Graphics Options';
        const SETTINGS_PAGE1          = 'cg_settings';
        const SETTINGS_PAGE_SUBTITLE1 = '';
        //const APP_SVG_FOLDER          = '/cap-graphics/';  //TODO:  get this from options
        //const APP_CHART_FOLDER        = '';


        var $settings, $options_page;

        function __construct()
        {

            if (is_admin()) {

                $settings_class  = APP_CLASS_NAME . '_Settings';
                $options_class   = APP_CLASS_NAME . '_Options';


                error_log(__FILE__.':'.__LINE__.' - here');

               if (!class_exists($settings_class))
                   require(WP_CONTENT_DIR . self::PLUGIN_DIR . self::APP_DIR . '/app-settings.php');
               $this->settings = new $settings_class();

               if (!class_exists($options_class))
                   require(WP_CONTENT_DIR . self::PLUGIN_DIR  . self::APP_DIR . '/app-options.php');
               $this->options_page = new $options_class();



                //action for seettings
                add_filter('plugin_row_meta', array(&$this, '_app_settings_link'), 10, 2);

                wp_enqueue_style( 'wp-color-picker' );
                wp_enqueue_script( 'wp-color-picker');
                error_log(__FILE__.':'.__LINE__.'  did we make it');
                //error_log(__FILE__.':'.__LINE__.' - cap graphics '.$cap_graphics->APP_NAMESPACE.'-admin');

                $plugin_path = plugin_dir_url(__FILE__);

                wp_enqueue_style( self::APP_NAMESPACE.'admin',  $plugin_path . 'assets/css/admin.css');
                wp_enqueue_script( self::APP_NAMESPACE.'admin', $plugin_path . 'assets/js/admin.js');


            } else {
                //require(WP_CONTENT_DIR . self::PLUGIN_DIR . self::APP_DIR . '/app-frontend.php');
            }

            $this->app_defaults = array(
                'all' => array(
                    'storage' => 'media'
                )
            );

            add_action('init', array($this, 'init'));
            add_action('admin_init', array($this, 'admin_init'));
            add_action('admin_menu', array($this, 'admin_menu'));
        }

        /**
         * Settings link
         * @param $links
         * @param $file
         * @return mixed
         */
        public function _app_settings_link($links, $file)
        {
            if ($file != self::APP_LOADER) return $links;
            $settings_link = '<a href="options-general.php?page=' . self::APP_SLUG . '">Settings</a>';
            //$view_link = '<a href="/cpa-verify-search" target="new">View Page</a>';
            //array_unshift($links, $settings_link, $view_link);
            return $links;
        }


        function network_propagate($pfunction, $networkwide)
        {
            global $wpdb;

            if (function_exists('is_multisite') && is_multisite()) {
                // check if it is a network activation - if so, run the activation function 
                // for each blog id
                if ($networkwide) {
                    $old_blog = $wpdb->blogid;
                    $blogids  = $wpdb->get_col("SELECT blog_id FROM {$wpdb->blogs}");
                    foreach ($blogids as $blog_id) {
                        switch_to_blog($blog_id);
                        call_user_func($pfunction, $networkwide);
                    }
                    switch_to_blog($old_blog);
                    return;
                }
            }
            call_user_func($pfunction, $networkwide);
        }

        function activate($networkwide)
        {
            $this->network_propagate(array($this, '_activate'), $networkwide);
        }

        function deactivate($networkwide)
        {
            $this->network_propagate(array($this, '_deactivate'), $networkwide);
        }


        function _activate()
        {

        }

        function _deactivate()
        {

        }

        function init()
        {

        }

        function admin_init()
        {

        }

        function admin_menu()
        {
 
            //convention over configuration, assume menu.png is name of menu icon
            add_menu_page(self::APP_NAME, self::APP_NAME, 'manage_options', self::APP_SLUG, self::APP_OPTION_CLASS_NAME.'::instruction_submenu',  WP_CONTENT_URL.self::PLUGIN_DIR.self::APP_DIR .'/assets/images/icon.png');
            add_submenu_page( self::APP_SLUG, 'Charts', 'Charts', 'manage_options', self::APP_SLUG.'-charts', self::APP_OPTION_CLASS_NAME.'::charts_submenu' );
            add_submenu_page( self::APP_SLUG, 'New Chart', 'New Chart', 'manage_options', self::APP_SLUG.'-new-chart', self::APP_OPTION_CLASS_NAME.'::charts_new' );
            add_submenu_page( self::APP_SLUG, 'SVG', 'SVG', 'manage_options', self::APP_SLUG.'-svg', self::APP_OPTION_CLASS_NAME.'::svg_submenu' );
            add_submenu_page( self::APP_SLUG, 'New SVG', 'New SVG', 'manage_options', self::APP_SLUG.'-new-svg', self::APP_OPTION_CLASS_NAME.'::svg_new' );
            add_submenu_page( self::APP_SLUG, 'Options', 'Options', 'manage_options', self::APP_SLUG.'-help', self::APP_OPTION_CLASS_NAME.'::option_function' );

            //add_plugins_page( self::APP_NAME, "Instructions", 'manage_options', 'pdf-instructions', self::APP_OPTION_CLASS_NAME.'::instruction_submenu');
        }

        /**
         * UTILITY: Error output
         * @param $input
         */
        function _e($input) {

            if(self::APP_DEBUG) {
                if(is_object($input) || is_array($input)) {
                    echo '<pre>';
                    print_r($input);
                    echo '</pre>';
                } else {
                    error_log($input);
                }
            }
        }


        //TODO:  Also put visualizations in media library, just allow picking and link to edit screen
        //TODO: turn svg=slug to id=10
        //TODO: no more saving slugs in the database, everything is in media library

        function settings_init() {
            //error_log(__FILE__.':'.__LINE__.' - frontend  ');
            register_setting( 'wp_cap_map', 'gp_options', array(&$this, 'sanitize_settings') );
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
                $settings_link = '<a href="' . admin_url( 'options-general.php?page=gp_options' ) . '">' . __('Settings', 'gp_options') . '</a>';
                array_unshift( $links, $settings_link ); // before other links
            }
            return $links;
        }

        /**
         *

        function gc_options_admin() {
        add_options_page('Cap Map', 'Cap Map', 'administrator', 'gp_options','Cap_Map::cap_map_admin');
        }
         */
        /**
         *
         */
        function gc_admin() {
            //$gc = new Cap_Map();  //this should not be necessary!!!!


            $html = self::gc_get_template(NULL,'assets/admin.php');
            return $html;
        }

        /**
         * @return string
         */
        function configuration() {

            $html = <<<EOD
	<h4>CAP Graphics Help Page</h4>

EOD;
            return $html;
        }


        /**
         * Meta boxes for picking svg and charts
         */
        function gc_meta()
        {
            /* TODO: no more meta boxes
            global $cap_graphics;


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

            */
        }


        /**
         * Custom callback function for svg box
         */
        function gc_svg_callback($post) {

            global $frontend_class;

            //$folder           = ABSPATH.$this->gc_frontend->svg_folder;
            $svg_select       = esc_attr(get_post_meta( $post->ID, 'svg_select', true ));
            $package_json     = file_get_contents($folder.'svg.json');
            $packages         = json_decode($package_json);

            //if there is already a chart selected, show this chart
            if($svg_select!='' || $svg_select!='Select One') {

                //instead of using javascript to pull existing data, get that data here
                $data['svg']  = file_get_contents($folder.$svg_select.'/'.$svg_select.'.svg', "w");
                $data['js']   = file_get_contents($folder.$svg_select.'/'.$svg_select.'.js', "w");
                $data['css']  = file_get_contents($folder.$svg_select.'/'.$svg_select.'.css', "w");
                $data['json'] = file_get_contents($folder.$svg_select.'/'.$svg_select.'.json', "w");
                //$svg_new      = $this->gc_frontend->cap_map_svg_tpl('update',$svg,$js,$css,$json);
            }



            return self::gc_get_template($data,'admin/svg_edit.php');

            /*
             *    <?php while (false !== ($entry = readdir($handle))): ?>
                                        <?php  if ($entry != "." && $entry != ".." && $entry != 'starter' && is_dir($folder.$entry)): ?>
                                            <?php if($svg_select==$entry): ?>
                                                <option value="<?php echo $entry; ?>" selected><?php echo $entry; ?></option>
                                            <?php else: ?>
                                                <option value="<?php echo $entry; ?>"><?php echo $entry; ?></option>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    <?php endwhile; closedir($handle); ?>
             */

            //wp_nonce_field( 'cap_map_meta_save', 'admin_meta_box_nonce' );


        }


        /**
         * Custom callback function for CHARTS box
         */
        function gc_chart_callback($post) {

            $gc          = new Cap_Map();  //this should not be necessary!!!!
            $data['folder']           = ABSPATH.$this->gc_frontend->chart_folder;
            $data['package_json']       = file_get_contents($folder.'charts.json');
            $data['packages']          = json_decode($package_json);
            $data['chart_select']       = esc_attr(get_post_meta( $post->ID, 'chart_select', true ));

            //if there is already a chart selected, show this chart
            if($chart_select!='' || $chart_select!='Select One') {
                //TODO: get data here instead of javascript

            }


            wp_nonce_field( 'cap_map_meta_save', 'admin_meta_box_nonce' );

            return self::gc_get_template($data,'admin/gc_chart_callback.php');

        }


        /**
         * Save custom meta box
         * @internal param $post_id
         */
        function gc_meta_save() {

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

            $gc      = new Cap_Map();
            $chart_action = array_key_exists('chart_action', $_POST) ? $_POST['chart_action'] : null;

            //if chart_action is set, then we are editing an existing chart
            if($chart_action) {

                $save['options']['chart_type']         = array_key_exists('chart_type', $_POST) ? sanitize_text_field($_POST['chart_type']) : null;
                $save['options']['chart_name']         = array_key_exists('chart_name', $_POST) ? sanitize_text_field($_POST['chart_name']) : null;
                $save['options']['segmentStrokeColor'] = array_key_exists('segmentStrokeColor', $_POST) ? $_POST['segmentStrokeColor'] : null;
                $save['options']['chart_source']       = array_key_exists('chart_source', $_POST) ? $_POST['chart_source'] : null;
                $save['options']['legend']             = array_key_exists('legend', $_POST) ? $_POST['legend'] : null;
                $save['options']['source']             = array_key_exists('source', $_POST) ? $_POST['source'] : null;
                $save['options']['name']               = array_key_exists('name', $_POST) ? $_POST['name'] : null;
                $save['options']['width']              = array_key_exists('width', $_POST) ? $_POST['width'] : null;
                $save['options']['height']             = array_key_exists('height', $_POST) ? $_POST['height'] : null;
                $save['data_array'][0]['chart_data']   = $_POST['chart_data'];

                if($chart_action=='new') {
                    $chart_slug   = array_key_exists('chart_slug', $_POST) ? sanitize_text_field($_POST['chart_slug']) : null;
                    $file         = ABSPATH.$this->gc_frontend->chart_folder.$chart_slug.'/'.$chart_slug.'.json';
                    //TODO: rewrite json file of packages
                    $package_file = ABSPATH.$this->gc_frontend->chart_folder.'/charts.json';
                    $charts_json  = json_decode(file_get_contents($package_file),true);

                    mkdir(ABSPATH.$this->gc_frontend->chart_folder.$chart_slug.'/');  //make directory
                    update_post_meta( $_POST['ID'], 'chart_select',  $chart_slug);  //update meta so we know what chart is associated with this post

                    //TODO: add new chart to array and rewrite
                    $charts_json['charts'][]['slug']        = $chart_slug;
                    $charts_json['charts'][]['label']       = $save['options']['chart_name'];
                    $charts_json['charts'][]['description'] =  'New Chart: '.$save['options']['chart_type'];

                    //rewrite json file
                    $this->gc_frontend->cap_map_save_json_file($package_file,$charts_json);
                } else {
                    $chart_slug   = $save['options']['chart_slug']  = array_key_exists('chart_slug', $_POST) ? $_POST['chart_slug'] : null;
                    $chart_select = $save['options']['chart_select']  = array_key_exists('chart_select', $_POST) ? $_POST['chart_select'] : null;

                    //if chart slug, differs from chart_select then rename file!
                    if($chart_slug!=$chart_select) {
                        $file                          = ABSPATH.$this->gc_frontend->plugin_uri.'/charts/'.$chart_slug.'/'.$chart_slug.'.json';
                        //$del                           = self::deleteDir(ABSPATH.$this->gc_frontend->plugin_uri.'/charts/'.$chart_select.'/');
                        system("rm -rf ".escapeshellarg(ABSPATH.$this->gc_frontend->plugin_uri.'/charts/'.$chart_select.'/'));  //system works better than php

                        mkdir(ABSPATH.$this->gc_frontend->plugin_uri.'/charts/'.$chart_slug.'/');  //make directory
                    } else {
                        $file                          = ABSPATH.$this->gc_frontend->plugin_uri.'/charts/'.$chart_slug.'/'.$chart_slug.'.json';
                    }
                    update_post_meta( $_POST['ID'], 'chart_select',  sanitize_text_field($_POST['chart_select']));  //update meta so we know what chart is associated with this post

                }

                if($_POST['animateRotate']=='1') {
                    $save['options']['animateRotate'] = true;
                } else {
                    $save['options']['animateRotate'] = false;
                }

                $save_result = self::cap_map_save_json_file($file,$save);
            }

            //if svg_action then save files
            $svg_action = array_key_exists('svg_action', $_POST) ? $_POST['svg_action'] : null;
            $svg_select = array_key_exists('svg_select', $_POST) ? $_POST['svg_select'] : null;
            $svg_slug   = array_key_exists('svg_slug', $_POST) ? $_POST['svg_slug'] : null;


            echo "<h1>svgaction: $svg_action</h1>";

            echo "<h1>svg_select: $svg_select</h1>";
            echo "<h1>svg_slug: $svg_slug</h1>";


            if($svg_action) {

                if($svg_action=='new') { //new svg file
                    mkdir(ABSPATH.$this->gc_frontend->svg_folder.$svg_slug.'/');  //make directory
                    $svg       = array_key_exists('svg', $_POST) ? $_POST['svg'] : null;
                    $js        = array_key_exists('js', $_POST) ? $_POST['js'] : null;
                    $css       = array_key_exists('css', $_POST) ? $_POST['css'] : null;
                    $json      = array_key_exists('json', $_POST) ? $_POST['json'] : null;

                    $file  = ABSPATH.$this->gc_frontend->svg_folder.$svg_slug.'/'.$svg_slug;

                    //need to save all files, if there is content
                    if($svg) {
                        $svg_save  = self::cap_map_save_file($file.'.svg',$svg);
                    }

                    if($js) {
                        $js_save   = self::cap_map_save_file($file.'.js',$js);
                    }

                    if($css) {
                        $css_save  = self::cap_map_save_file($file.'.css',$css);
                    }

                    if($json) {
                        $json_save = self::cap_map_save_file($file.'.json',$json);
                    }

                    update_post_meta( $_POST['ID'], 'svg_select',  sanitize_text_field($svg_slug));

                } else {
                    $chart_slug                        = $save['options']['chart_slug']  = array_key_exists('chart_slug', $_POST) ? $_POST['chart_slug'] : null;
                    //if chart slug, differs from chart_select then rename file!
                    if($chart_slug!=$chart_select) {
                        $file                          = ABSPATH.$this->gc_frontend->plugin_uri.'/charts/'.$chart_slug.'/'.$chart_slug.'.json';
                        //$del                           = self::deleteDir(ABSPATH.$this->gc_frontend->plugin_uri.'/charts/'.$chart_select.'/');
                        system("rm -rf ".escapeshellarg(ABSPATH.$this->gc_frontend->plugin_uri.'/charts/'.$chart_select.'/'));  //system works better than php

                        mkdir(ABSPATH.$this->gc_frontend->plugin_uri.'/charts/'.$chart_slug.'/');  //make directory
                    } else {
                        $file                          = ABSPATH.$this->gc_frontend->plugin_uri.'/charts/'.$chart_slug.'/'.$chart_slug.'.json';
                    }
                    update_post_meta( $_POST['ID'], 'chart_select',  sanitize_text_field($_POST['chart_select']));  //update meta so we know what chart is associated with this post

                }
                $save_result = self::cap_map_save_file($file,$save);
            }

            echo '<pre>';
            print_r($_POST);
            echo '</pre>';

            exit;

        }

        /**
         * UTILITY: save file
         * @param $file
         * @param $save
         * @return int
         */
        function gc_save_json_file($file,$save) {
            $v = explode('.', PHP_VERSION);  //JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES require php 5.4.0
            if ($v[0] == 5 && $v[1] < 2) {
                die("You need to have at least PHP 5.2 installed to use this. You currently have " . PHP_VERSION);
            } elseif ($v[0] == 5 && $v[1] < 4) {
                $newdata = json_encode($save,JSON_NUMERIC_CHECK);
            } else {
                $newdata = json_encode($save, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES  |  JSON_NUMERIC_CHECK);
            }

            $fh = fopen($file, "w");
            if ($fh == false) {
                return 0;
            } else {
                fputs($fh, $newdata);
                fclose($fh);
                return 1;
            }
        }

        /**
         * @param $file
         * @param $save
         * @return int
         */
        function gc_save_file($file,$save) {
            $fh = fopen($file, "w");
            if ($fh == false) {
                return 0;
            } else {
                fputs($fh, $save);
                fclose($fh);
                return 1;
            }
        }

        /**
         * Return SVG depending on options selected for this ID
         * @param $atts
         * @return string
         */
        function gc_svg_shortcode( $atts ){
            //TODO: think about putting every fiule, json, js, css into one "package" or in ONE folder

            $id       = get_the_ID();

            //TODO: if short code is [cap_svg svg="slug"]
            if($atts['svg']) {
                $svg_raw  = $atts['svg'];
            } else {
                $svg_raw  = get_post_meta($id,'svg_select',true);
            }
            $content  = '';
            echo dirname(__FILE__).'/assets/css/frontend.css<br><Br>';
            //always include front end css
            wp_enqueue_style('capmapcss', dirname(__FILE__).'/assets/css/frontend.css');

            if($svg_raw != 'Select One') {
                //$svg_file   = self::APP_FRONTEND->svg_folder.$svg_raw.'/'.$svg_raw.'.svg';
                $custom_js  = $svg_file . $svg_raw . 'js';
                $custom_css = $svg_file . $svg_raw . 'css';

                //if no svg, error out
                if (file_exists(ABSPATH . $svg_file)) {
                    $svg_data =  file_get_contents(ABSPATH.$svg_file);
                }

                //include js and css IF exists
                if (file_exists(ABSPATH . $custom_js)) {
                    wp_enqueue_script('js-' . $id, $custom_js, '', '1', true);
                }

                if (file_exists(ABSPATH . $custom_css)) {
                    wp_enqueue_style('css-' . $id, $custom_css);
                }

                $content .= '<div class="svg_wrap"><div class="svg_meta"></div>';
                $content .= $svg_data;
                $content .= '</div>';

            }
            return $content;
        }

        /**
         * Return chart depending on options selected for this ID
         * @param $atts
         * @return string
         */
        function gc_chart_shortcode( $atts ){
            $content = $legend_html = $legend_inner = $canvas_html = '';

            $gc     = new Cap_Map();
            $id          = get_the_ID();
            $chart_slug  = get_post_meta($id,'chart_slug',true);


            //get frontend css and charts scripts
            wp_enqueue_style('capmapcss', $this->gc_frontend->plugin_uri.'assets/css/frontend.css');
            wp_enqueue_script('charts',  $this->gc_frontend->plugin_uri.'assets/js/common/Chart.min.js','','1',true);
            wp_enqueue_script('charts',  $this->gc_frontend->plugin_uri.'assets/js/common/charts.options.js','','1',true);


            /*
             *        $('#import_export_line').appear(function() {
                        new Chart(document.getElementById('import_export_line').getContext('2d')).Line(json.data_array,json.options);
                    },{accX: 0, accY: -200});
             */

            //not as efficient, but for total control from json pull json in php as well
            $json_file = $this->gc_frontend->plugin_uri."charts/$chart_slug/$chart_slug.json";
            $json      = json_decode(file_get_contents(ABSPATH.$json_file),true);
            $legend    = $json['options']['legend'];
            $name      = $json['options']['name'];
            $source    = $json['options']['source'];

            if($name) {
                $chart_name = '<h3>'.$json['options']['chart_name'].'</h3>';
            } else {
                $chart_name = '';
            }

            if($source) {
                $chart_source = '<p class="chart_source">'.$json['options']['chart_source'].'</p>';
            } else {
                $chart_source = '';
            }

            //print_r($json);

            $canvas_html = '<canvas id="c1" width="'.$json['options']['width'].'" height="'.$json['options']['height'].'"></canvas>';
            if($legend=='1') {


                foreach($json['data_array'][0]['chart_data'] as $c) {
                    $legend_inner .= '
                        <li>
                            <div style="background-color: '.$c['color'].'"></div>
                            <p>'.$c['label'].'</p>
                        </li>';


                }

                $legend_html = <<< EOS

            <div class="c_1_1 $chart_slug">
                $chart_name
                <div class="left">$canvas_html $chart_source</div>
                <div class="left">
                    <div class="legend">
                        <ul>
                            $legend_inner
                        </ul>
                    </div>
                </div>
            </div>

EOS;


            } else {

                $legend_html = <<< EOS

            <div class="c_1_1">
                $chart_name
                $canvas_html
                $chart_source
            </div>

EOS;
            }

            $content = <<< EOS

        $legend_html

        <script>
            jQuery(document).ready(function($) {
                $.getJSON( "$json_file").done(function( json ) {
                    var str = json.options.chart_type.toString();
                    new Chart(document.getElementById('c1').getContext('2d'))[str](json.data_array[0].chart_data,json.options);
                    console.dir(json);
                })
                .fail(function( jqxhr, textStatus, error ) {
                    var err = textStatus + ", " + error;
                    console.log( "Request Failed: " + err );
                });
            });
        </script>

EOS;





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
         * AJAX: show template box, either with data or blank
         */
        function gc_chart_action_callback() {
            //TODO: leave this out of templating system for now


            $list = $disable = '';
            $app_options  = get_option(self::OPTIONS_PREFIX);
            $cap_graphics = new Cap_Graphics();
            $app_defaults = $cap_graphics->app_defaults;  //what is wrong with this
            $options      = Cap_Graphics_Settings::merge_options($app_defaults, $app_options);




            $chart_type = array_key_exists('chart_type', $_POST) ? $_POST['chart_type'] : null;  //proper way of getting variables without notice errors
            $d_place    = 'Enter a Slug with Underscores Not Spaces';
            $chart_data = self::get_chart_data(NULL,$chart_type,'');

            $return = array(
                'html'=>$_POST,
                'options'=>$options,
                'storage'=>$options['all']['storage']
            );

            wp_send_json($return);
            wp_die();


            //if there is a chart type, then drop down selected so we grab data
            if($options['all']['storage'] == 'media') {
                $package = $this->gc_frontend->plugin_uri.'charts/'.$chart_slug;
            } else {
                $package = $this->gc_frontend->plugin_uri.'charts/'.$chart_slug;
            }





            $jsonfile           = $package.'/'.$chart_slug.'.json';
            $json               = file_get_contents(ABSPATH.$jsonfile);
            $data               = json_decode($json,true);

            //form values
            $chart_name         = isset($data['options']['chart_name']) ? $data['options']['chart_name'] : null;
            $chart_type         = isset($data['options']['chart_type']) ? $data['options']['chart_type']  : null;
            $chart_source       = isset($data['options']['chart_source']) ? $data['options']['chart_source']  : null;
            $segmentStrokeColor = isset($data['options']['segmentStrokeColor']) ? $data['options']['segmentStrokeColor']  : null;
            $width              = isset($data['options']['width']) ? $data['options']['width']  : null;
            $height             = isset($data['options']['height']) ? $data['options']['height']  : null;
            $source             = isset($data['options']['source']) ? $data['options']['source']  : null;
            $legend             = isset($data['options']['legend']) ? $data['options']['legend']  : null;
            $name               = isset($data['options']['name']) ? $data['options']['name']  : null;

            if($data['options']['animateRotate']=='1') {
                $animateRotate = 1;
            } else {
                $animateRotate = 0;
            }

            $chart_action       = 'update';
            $chart_data         = self::get_chart_data($data,$chart_type,'');


            $chart_types = array(
                'Doughnut',
                'Pie',
                'Line',
                'LinePie',
                'Bar',
                'Radar'
            );
            $list .= self::gc_get_dropdown($chart_types,$chart_type);


            //get default values for switches
            switch ($name) {
                case 0:
                    $name_disable = 'selected';
                    $name_enable  = '';
                    $name_1        = '';
                    $name_2        = 'checked';
                    break;
                case 1:
                    $name_enable = 'selected';
                    $name_disable  = '';
                    $name_1        = 'checked';
                    $name_2        = '';
                    break;
                default:
                    $name_disable = 'selected';
                    $name_enable  = '';
                    $name_1        = '';
                    $name_2        = 'checked';
            }

            switch ($source) {
                case 0:
                    $source_disable  = 'selected';
                    $source_enable   = '';
                    $source_1        = '';
                    $source_2        = 'checked';
                    break;
                case 1:
                    $source_enable   = 'selected';
                    $source_disable  = '';
                    $source_1        = 'checked';
                    $source_2        = '';
                    break;
                default:
                    $source_disable  = 'selected';
                    $source_enable   = '';
                    $source_1        = '';
                    $source_2        = 'checked';
            }

            switch ($legend) {
                case 0:
                    $legend_disable = 'selected';
                    $legend_enable  = '';
                    $legend_1       = '';
                    $legend_2       = 'checked';
                    break;
                case 1:
                    $legend_enable  = 'selected';
                    $legend_disable = '';
                    $legend_1       = 'checked';
                    $legend_2       = '';
                    break;
                default:
                    $legend_disable = 'selected';
                    $legend_enable  = '';
                    $legend_1       = '';
                    $legend_2       = 'checked';
            }

            switch ($animateRotate) {
                case false:
                    $animateRotate_disable = 'selected';
                    $animateRotate_enable  = '';
                    $animateRotate_1        = '';
                    $animateRotate_2        = 'checked';
                    break;
                case true:
                    $animateRotate_enable  = 'selected';
                    $animateRotate_disable = '';
                    $animateRotate_1        = 'checked';
                    $animateRotate_2        = '';
                    break;
                default:
                    $animateRotate_disable = 'selected';
                    $animateRotate_enable  = '';
                    $animateRotate_1        = '';
                    $animateRotate_2        = 'checked';
            }


            $html = <<< EOS
                        <ul class="sub">
                            <li>
                                <span>Chart Slug</span>
                                <input type="text" name="chart_slug" id="chart_slug" value="$chart_slug" placeholder="$d_place" required />
                            </li>
                            <li>
                                <span>Chart Type</span>
                                <select class="chart_type" name="chart_type" id="chart_type_anchor">
                                    <option>Select One</option>
                                    $list
                                </select>
                            </li>
                            <li>
                                <span>Chart Name</span>
                                <input type="text" name="chart_name" id="chart_name" placeholder="Enter a Chart Name with No Special Characters" value="$chart_name" />

                            </li>
                            <li>
                                <span>Data Source</span>
                                <input type="text" name="chart_source" id="chart_source" placeholder="Enter a url for the source of this data" value="$chart_source" />
                            </li>
                            <li>
                                <span>Line Color</span>
                                <input type="text" class="colorpicker" id="segmentStrokeColor" name="segmentStrokeColor" value="$segmentStrokeColor" required />
                            </li>
                            <li>
                                <span>Chart Height</span>
                                <input type="number" name="height" id="height" placeholder="Enter a height for this chart (defaut: 300)" value="$height" />
                            </li>
                            <li>
                                <span>Chart Width</span>
                                <input type="number" name="width" id="width" placeholder="Enter a width for this chart (defaut: 300)" value="$width" />
                            </li>
                            <li class="switch">
                                <div>Show Name</div>
                                <label class="cb-enable $name_enable" data-class="name"><span>Yes</span></label> <input type="checkbox" name="name" value="1" id="name_enabled" $name_1 />
                                <label class="cb-disable $name_disable" data-class="name"><span>No</span></label> <input type="checkbox" name="name" value="0" id="name_disabled" $name_2 />
                            </li>
                            <li class="switch">
                                <div>Show Source</div>
                                <label class="cb-enable $source_enable" data-class="source"><span>Yes</span></label> <input type="checkbox" name="source" value="1" id="source_enabled" $source_1 />
                                <label class="cb-disable $source_disable" data-class="source"><span>No</span></label> <input type="checkbox" name="source" value="0" id="source_disabled" $source_2 />
                            </li>
                            <li class="switch">
                                <div>Show Legend</div>
                                <label class="cb-enable $legend_enable" data-class="legend"><span>Yes</span></label> <input type="checkbox" name="legend" value="1" id="legend_enabled" $legend_1 />
                                <label class="cb-disable $legend_disable" data-class="legend"><span>No</span></label> <input type="checkbox" name="legend" value="0" id="legend_disabled" $legend_2 />
                            </li>
                            <li class="switch">
                                <div>Animate</div>
                                <label class="cb-enable $animateRotate_enable" data-class="animateRotate"><span>Yes</span></label> <input type="checkbox" name="animateRotate" value="1" id="animateRotate_enabled" $animateRotate_1 />
                                <label class="cb-disable $animateRotate_disable" data-class="animateRotate"><span>No</span></label> <input type="checkbox" name="animateRotate" value="0" id="animateRotate_disabled" $animateRotate_2 />
                            </li>
                            <li><div class="note">NOTE:  The legend is only for Doughnut and Pie Charts</div><h4><a href="javascript:void(0);" class="add_field" data-type="$chart_type">Add Line to Chart</a></h4></li>

                            <ul class="chart_data_wrap">
                                <li>$chart_data</li>
                            </ul>
                            <li><input type="hidden" id="chart_action" name="chart_action" value="$chart_action" /></li>
                        </ul>
EOS;


            $html .= <<< EOS
<script>

</script>
EOS;


            $return = array(
                'html'=>$html,
                'json'=> $json,
                'data'=> $data,
                'chart_name' =>$chart_name,
                'chart_data' =>$chart_data
            );

            wp_send_json($return);
            wp_die();
        }



        /**
         * AJAX: show template box, either with data or blank
         */
        function gc_chart_action_callbackOLD() {
            //TODO: leave this out of templating system for now





            $list = $disable = '';
            $chart_slug = array_key_exists('chart_slug', $_POST) ? $_POST['chart_slug'] : null;  //proper way of getting variables without notice errors
            $d_place    = 'Enter a Slug with Underscores Not Spaces';


            $return = array(
                'html'=>$_POST,
                'options'=>$this->options_page,
            );

            wp_send_json($return);
            wp_die();


            //if there is a chart type, then drop down selected so we grab data

            //TODO: where to get packages
            $package            = $this->gc_frontend->plugin_uri.'charts/'.$chart_slug;




            $jsonfile           = $package.'/'.$chart_slug.'.json';
            $json               = file_get_contents(ABSPATH.$jsonfile);
            $data               = json_decode($json,true);

            //form values
            $chart_name         = isset($data['options']['chart_name']) ? $data['options']['chart_name'] : null;
            $chart_type         = isset($data['options']['chart_type']) ? $data['options']['chart_type']  : null;
            $chart_source       = isset($data['options']['chart_source']) ? $data['options']['chart_source']  : null;
            $segmentStrokeColor = isset($data['options']['segmentStrokeColor']) ? $data['options']['segmentStrokeColor']  : null;
            $width              = isset($data['options']['width']) ? $data['options']['width']  : null;
            $height             = isset($data['options']['height']) ? $data['options']['height']  : null;
            $source             = isset($data['options']['source']) ? $data['options']['source']  : null;
            $legend             = isset($data['options']['legend']) ? $data['options']['legend']  : null;
            $name               = isset($data['options']['name']) ? $data['options']['name']  : null;

            if($data['options']['animateRotate']=='1') {
                $animateRotate = 1;
            } else {
                $animateRotate = 0;
            }

            $chart_action       = 'update';
            $chart_data         = self::get_chart_data($data,$chart_type,'');


            $chart_types = array(
                'Doughnut',
                'Pie',
                'Line',
                'LinePie',
                'Bar',
                'Radar'
            );
            $list .= self::gc_get_dropdown($chart_types,$chart_type);


            //get default values for switches
            switch ($name) {
                case 0:
                    $name_disable = 'selected';
                    $name_enable  = '';
                    $name_1        = '';
                    $name_2        = 'checked';
                    break;
                case 1:
                    $name_enable = 'selected';
                    $name_disable  = '';
                    $name_1        = 'checked';
                    $name_2        = '';
                    break;
                default:
                    $name_disable = 'selected';
                    $name_enable  = '';
                    $name_1        = '';
                    $name_2        = 'checked';
            }

            switch ($source) {
                case 0:
                    $source_disable  = 'selected';
                    $source_enable   = '';
                    $source_1        = '';
                    $source_2        = 'checked';
                    break;
                case 1:
                    $source_enable   = 'selected';
                    $source_disable  = '';
                    $source_1        = 'checked';
                    $source_2        = '';
                    break;
                default:
                    $source_disable  = 'selected';
                    $source_enable   = '';
                    $source_1        = '';
                    $source_2        = 'checked';
            }

            switch ($legend) {
                case 0:
                    $legend_disable = 'selected';
                    $legend_enable  = '';
                    $legend_1       = '';
                    $legend_2       = 'checked';
                    break;
                case 1:
                    $legend_enable  = 'selected';
                    $legend_disable = '';
                    $legend_1       = 'checked';
                    $legend_2       = '';
                    break;
                default:
                    $legend_disable = 'selected';
                    $legend_enable  = '';
                    $legend_1       = '';
                    $legend_2       = 'checked';
            }

            switch ($animateRotate) {
                case false:
                    $animateRotate_disable = 'selected';
                    $animateRotate_enable  = '';
                    $animateRotate_1        = '';
                    $animateRotate_2        = 'checked';
                    break;
                case true:
                    $animateRotate_enable  = 'selected';
                    $animateRotate_disable = '';
                    $animateRotate_1        = 'checked';
                    $animateRotate_2        = '';
                    break;
                default:
                    $animateRotate_disable = 'selected';
                    $animateRotate_enable  = '';
                    $animateRotate_1        = '';
                    $animateRotate_2        = 'checked';
            }


            $html = <<< EOS
                        <ul class="sub">
                            <li>
                                <span>Chart Slug</span>
                                <input type="text" name="chart_slug" id="chart_slug" value="$chart_slug" placeholder="$d_place" required />
                            </li>
                            <li>
                                <span>Chart Type</span>
                                <select class="chart_type" name="chart_type" id="chart_type_anchor">
                                    <option>Select One</option>
                                    $list
                                </select>
                            </li>
                            <li>
                                <span>Chart Name</span>
                                <input type="text" name="chart_name" id="chart_name" placeholder="Enter a Chart Name with No Special Characters" value="$chart_name" />

                            </li>
                            <li>
                                <span>Data Source</span>
                                <input type="text" name="chart_source" id="chart_source" placeholder="Enter a url for the source of this data" value="$chart_source" />
                            </li>
                            <li>
                                <span>Line Color</span>
                                <input type="text" class="colorpicker" id="segmentStrokeColor" name="segmentStrokeColor" value="$segmentStrokeColor" required />
                            </li>
                            <li>
                                <span>Chart Height</span>
                                <input type="number" name="height" id="height" placeholder="Enter a height for this chart (defaut: 300)" value="$height" />
                            </li>
                            <li>
                                <span>Chart Width</span>
                                <input type="number" name="width" id="width" placeholder="Enter a width for this chart (defaut: 300)" value="$width" />
                            </li>
                            <li class="switch">
                                <div>Show Name</div>
                                <label class="cb-enable $name_enable" data-class="name"><span>Yes</span></label> <input type="checkbox" name="name" value="1" id="name_enabled" $name_1 />
                                <label class="cb-disable $name_disable" data-class="name"><span>No</span></label> <input type="checkbox" name="name" value="0" id="name_disabled" $name_2 />
                            </li>
                            <li class="switch">
                                <div>Show Source</div>
                                <label class="cb-enable $source_enable" data-class="source"><span>Yes</span></label> <input type="checkbox" name="source" value="1" id="source_enabled" $source_1 />
                                <label class="cb-disable $source_disable" data-class="source"><span>No</span></label> <input type="checkbox" name="source" value="0" id="source_disabled" $source_2 />
                            </li>
                            <li class="switch">
                                <div>Show Legend</div>
                                <label class="cb-enable $legend_enable" data-class="legend"><span>Yes</span></label> <input type="checkbox" name="legend" value="1" id="legend_enabled" $legend_1 />
                                <label class="cb-disable $legend_disable" data-class="legend"><span>No</span></label> <input type="checkbox" name="legend" value="0" id="legend_disabled" $legend_2 />
                            </li>
                            <li class="switch">
                                <div>Animate</div>
                                <label class="cb-enable $animateRotate_enable" data-class="animateRotate"><span>Yes</span></label> <input type="checkbox" name="animateRotate" value="1" id="animateRotate_enabled" $animateRotate_1 />
                                <label class="cb-disable $animateRotate_disable" data-class="animateRotate"><span>No</span></label> <input type="checkbox" name="animateRotate" value="0" id="animateRotate_disabled" $animateRotate_2 />
                            </li>
                            <li><div class="note">NOTE:  The legend is only for Doughnut and Pie Charts</div><h4><a href="javascript:void(0);" class="add_field" data-type="$chart_type">Add Line to Chart</a></h4></li>

                            <ul class="chart_data_wrap">
                                <li>$chart_data</li>
                            </ul>
                            <li><input type="hidden" id="chart_action" name="chart_action" value="$chart_action" /></li>
                        </ul>
EOS;


            $html .= <<< EOS
<script>

</script>
EOS;


            $return = array(
                'html'=>$html,
                'json'=> $json,
                'data'=> $data,
                'chart_name' =>$chart_name,
                'chart_data' =>$chart_data
            );

            wp_send_json($return);
            wp_die();
        }



        /**
         * AJAX: get a line and prepend depending on chart type
         */
        function gc_chart_action_line_callback() {

            $chart_data = '';

            //count current entries
            $chart_type = array_key_exists('chart_type', $_POST) ? $_POST['chart_type'] : null;
            $number     = array_key_exists('number', $_POST) ? $_POST['number'] : null;
            $id         = $number+1;

            $chart_data .= self::get_chart_data('',$chart_type,$id);

            $return = array(
                'chart_data'=>$chart_data
            );

            wp_send_json($return);
            wp_die();

        }

        /**
         * Depending on chart type, take data and return in form format
         * @param $data
         * @param $chart_type
         * @return mixed
         */
        function get_chart_data($data,$chart_type,$id) {

            $chart_data = '';
            switch ($chart_type) {
                case 'Doughnut':


                    //always show add line to chart
                    $chart_data .= <<< EOS

EOS;

                    //if there is data, then plug it in
                    if($data) {
                        foreach($data['data_array'][0]['chart_data'] as $k=>$v) {
                            $chart_data .= <<< EOS
                        <li>
                            <ul class="chart_data_inner" id="data-$k">
                                <li class="btns_data">
                                    <a href="javascript:void(0);" class="btn delete" data-id="$k">delete</a>
                                </li>
                                <li>
                                    <span>Label</span>
                                    <input type="text" name="chart_data[$k][label]" value="{$v['label']}" />
                                </li>
                                <li>
                                    <span>Value</span>
                                    <input type="text" name="chart_data[$k][value]" value="{$v['value']}" />
                                </li>
                                <li>
                                    <span>Color</span>
                                    <input type="text" class="colorpicker" name="chart_data[$k][color]" value="{$v['color']}" data-default-color="{$v['color']}" />
                                </li>
                                <li>
                                    <span>Highlight</span>
                                    <input type="text" class="colorpicker" name="chart_data[$k][highlight]" value="{$v['highlight']}"  data-default-color="{$v['color']}" />
                                </li>
                            </ul>
                        </li>
EOS;
                        }
                        $chart_data .= '</ul>';

                    } else {
                        $chart_data .= <<< EOS

            <li>
                <ul class="chart_data_inner" id="data-$id">
                    <li class="btns_data">
                        <a href="javascript:void(0);" class="btn delete" data-id="$id">delete</a>
                    </li>
                    <li>
                        <span>Label</span>
                        <input type="text" name="chart_data[$id][label]" value="Enter Label" />
                    </li>
                    <li>
                        <span>Value</span>
                        <input type="text" name="chart_data[$id][value]" value="10" />
                    </li>
                    <li>
                        <span>Color</span>
                        <input type="text" class="colorpicker" name="chart_data[$id][color]" value="#ba575a" data-default-color="#ba575a" />
                    </li>
                    <li>
                        <span>Highlight</span>
                        <input type="text" class="colorpicker" name="chart_data[$id][highlight]" value="#a92d31"  data-default-color="#a92d31" />
                    </li>
                </ul>
            </li>

EOS;

                    }

                    break;
                case 1:
                    echo "i equals 1";
                    break;
                case 2:
                    echo "i equals 2";
                    break;
            }


            $chart_data.= <<<E_ALL
<script>
jQuery(document).ready(function($) {
    if(typeof myfunc == 'wpColorPicker'){
        console.log("exist");
    }else{
        $(".colorpicker").wpColorPicker();
    }
});
</script>
E_ALL;


            return $chart_data;
        }


        /**
         * AJAX: show template box, either with data or blank
         */
        function gc_svg_action_callback()
        {
            $svg = $js = $css = $json = '';
            //TODO: best way to do this, no example. leave that for print preview.  show 3 boxes allow edit of json, svg, and js
            //TODO: may need upload for svg, large ones could cause problems for text edit box
            $time_start = self::timer();
            $html       = '';
            $gc    = new Cap_Map();
            $svg_slug   = array_key_exists('svg_slug', $_POST) ? $_POST['svg_slug'] : null;
            $folder     = ABSPATH.$this->gc_frontend->svg_folder.$svg_slug.'/';


            if($svg_slug) {
                $svg_action = 'update';

                $js_file = $folder.$svg_slug.'.js';
                if(file_exists($js_file)) {
                    $js = file_get_contents($js_file);
                }

                $css_file = $folder.$svg_slug.'.css';
                if(file_exists($css_file)) {
                    $css = file_get_contents($css_file);
                }

                $svg_file = $folder.$svg_slug.'.svg';
                if(file_exists($svg_file)) {
                    $svg = file_get_contents($svg_file);
                }

                $json_file = $folder.$svg_slug.'.json';
                if(file_exists($json_file)) {
                    $json = file_get_contents($json_file);
                }

            } else {

                $svg_action = 'new';
            }

            $html = $this->gc_frontend->cap_map_svg_tpl($svg_action,$svg,$js,$css,$json);



            //$html = $this->gc_frontend->cap_map_svg_tpl($svg_action,$svg,$js,$css,$json);
            $time_end = self::timer();

            $return = array(
                'html'=>$html,
                'loading' => $time_end - $time_start
            );

            wp_send_json($return);
            wp_die();
        }

        /**
         * Template for svg edit boxes
         * @param $svg_action
         * @param $svg
         * @param $js
         * @param $css
         * @param $json
         * @return string
         */
        function gc_svg_tpl($svg_action,$svg,$js,$css,$json) {

            return <<<NCURSES_KEY_EOS



        <ul class="sub svg_edit">
<li>
    <span>SVG File</span>
     <div class="btns_data">
        <a href="javascript:void(0);" class="btn save" data-file="svg" id="btn_svg">save</a>
    </div>
    <textarea name="svg">$svg</textarea>
</li>
<li>
    <span>Javascript Code</span>
    <div class="btns_data">
        <a href="javascript:void(0);" class="btn save" data-file="js" id="btn_js">save</a>
    </div>
    <textarea name="js">$js</textarea>
</li>
<li>
    <span>CSS Styles</span>
    <div class="btns_data">
        <a href="javascript:void(0);" class="btn save" data-file="css" id="btn_css">save</a>
    </div>
    <textarea name="css">$css</textarea>
</li>
<li>
    <span>JSON Data</span>
    <div class="btns_data">
        <a href="javascript:void(0);" class="btn save" data-file="json" id="btn_json">save</a>
    </div>
    <textarea name="json">$json</textarea>
</li>
<li><input type="hidden" id="svg_action" name="svg_action" value="$svg_action" /></li>
</ul>
NCURSES_KEY_EOS;


        }

        /**
         * AJAX: save file from textarea
         */
        function gc_file_save_action_callback($post)
        {
            $time_start = self::timer();
            $gc    = new Cap_Map();
            $ID         = array_key_exists('ID', $_POST) ? $_POST['ID'] : null;
            $svg_slug   = array_key_exists('svg_slug', $_POST) ? $_POST['svg_slug'] : null;
            $data       = array_key_exists('data', $_POST) ? stripslashes($_POST['data']) : null;  //use strip slashes because of MAGIC QUOTES
            $file       = array_key_exists('file', $_POST) ? $_POST['file'] : null;
            $folder     = ABSPATH.$this->gc_frontend->svg_folder.$svg_slug.'/';
            $filename   = $folder.$svg_slug.'.'.$file;

            if(!file_exists($folder)) {
                mkdir($folder);
            }

            $fh = fopen($filename, "w");
            if ($fh == false) {
                $result = 0;
            } else {
                fputs($fh, $data);
                fclose($fh);
                update_post_meta($ID, 'svg_select',  sanitize_text_field($svg_slug));
                $result = 1;
            }
            $time_end = self::timer();
            $return   = array(
                'result'=> $result,
                'file'=> $filename,
                'svg_slug'=> $svg_slug,
                'timer'=> $time_end - $time_start,
                'ID'=>$ID
            );

            wp_send_json($return);
            wp_die();
        }

        /**
         * UTILITY: Delete a directory with files
         * @param $path
         * @return bool
         */
        function deleteDir($path) {
            return is_file($path) ?
                unlink($path) :
                array_map(__FUNCTION__, glob($path.'/*')) == rmdir($path);
        }

        /**
         * UTILITY: timer script
         * @return float
         */
        function timer()
        {
            list($usec, $sec) = explode(" ", microtime());
            return ((float)$usec + (float)$sec);
        }

        /**
         * Simply open a teplate TODO: not working, try with file get contents and get variables working!
         * @param $data
         * @param $template
         */
        function gc_get_template($data,$template) {
            include(WP_CONTENT_DIR . self::PLUGIN_DIR . self::APP_DIR.'/assets/templates/'.$template);
        }

        function gc_get_template2($data,$template) {
            echo file_get_contents(WP_CONTENT_DIR . self::PLUGIN_DIR . self::APP_DIR.'/assets/templates/'.$template);
        }


        /**
         * UTILITY: build dropdown
         * @param $options
         * @param null $selected
         * @return string
         */
        function gc_get_dropdown($options,$selected = NULL) {

            $list = '';
            foreach ($options as $c) {
                if($selected==$c) {
                    $list .= "<option selected>$c</option>";
                } else {
                    $list .= "<option>$c</option>";
                }
            }
            return $list;
        }
    }

}

global $cap_graphics;
if (class_exists(APP_CLASS_NAME) && !$cap_graphics) {
    $cap_graphics = new Cap_Graphics();



    if ( is_admin() ) {
        add_action( 'admin_menu', 'Cap_Graphics::gc_options_admin' );  //options page, TODO: perhaps put chart options here
        add_action( 'add_meta_boxes', "Cap_Graphics::gc_meta"); //meta box
        add_action( 'save_post', 'Cap_Graphics::gc_meta_save' );  //this is causing problems with new post pages

        //svg
        add_action( 'wp_ajax_cap_map_svg_action', 'Cap_Graphics::gc_svg_action_callback' );  //ajax for new svg
        add_action( 'wp_ajax_nopriv_cap_map_svg_action', 'Cap_Graphics::gc_svg_action_callback' );   //ajax for new svg
        add_action( 'wp_ajax_cap_map_file_save_action', 'Cap_Graphics::gc_file_save_action_callback' );  //ajax for saving files
        add_action( 'wp_ajax_nopriv_cap_map_file_save_action', 'Cap_Graphics::gc_file_save_action_callback' );   //ajax for saving files

        //charts
        add_action( 'wp_ajax_cap_map_chart_action', 'Cap_Graphics::gc_chart_action_callback' );  //ajax for new chart
        add_action( 'wp_ajax_nopriv_cap_map_chart_action', 'Cap_Graphics::gc_chart_action_callback' );   //ajax for new chart
        add_action( 'wp_ajax_cap_map_chart_line_action', 'Cap_Graphics::gc_chart_action_line_callback' );  //ajax for adding a line to new chart
        add_action( 'wp_ajax_nopriv_cap_map_chart_line_action', 'Cap_Graphics::gc_chart_action_line_callback' );   //ajaxfor adding a line to new chart

    } else {
        add_shortcode( 'cap_svg', 'Cap_Graphics::gc_svg_shortcode' );  //register shortcode for svg
        add_shortcode( 'cap_chart', 'Cap_Graphics::gc_chart_shortcode' );  //register shortcode
    }

}


