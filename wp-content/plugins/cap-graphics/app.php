<?php

//TODO: charts.json and svg.json need to go into DATABASE
//TODO: need to put everything into s3 bucket
//TODO: build option for using oEmbed
//TODO:  build lyvefire version
//TODO: add line pie
/*

,
    {
      "slug": "linepie",
      "label": "Line Pie Chart",
      "description": "Test description",
      "type": "linepie",
      "status": 1
    },
*/


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
                //error_log(__FILE__.':'.__LINE__.' - cap graphics '.$cap_graphics->APP_NAMESPACE.'-admin');


                wp_enqueue_style( self::APP_NAMESPACE.'admin',  plugin_dir_url(__FILE__) . 'assets/css/admin.css');
                wp_enqueue_style( self::APP_NAMESPACE.'animate',  plugin_dir_url(__FILE__) . 'assets/css/animate.css');
                wp_enqueue_script( self::APP_NAMESPACE.'admin', plugin_dir_url(__FILE__) . 'assets/js/admin.js');
                wp_enqueue_script( self::APP_NAMESPACE.'notify', plugin_dir_url(__FILE__) . 'assets/js/common/bootstrap-notify.min.js');


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


        /**
         *
         */
        function settings_init() {
            error_log(__FILE__.':'.__LINE__.' - frontend  ');
            //register_setting( 'wp_cap_map', 'gp_options', array(&$this, 'sanitize_settings') );
            //register_setting( 'wp_cap_map', 'gp_options', array(&$this, 'sanitize_settings') );


            register_setting('gc_chart_save_callback', self::OPTIONS_PREFIX);
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
        }

        /**
         * AJAX:  NEW Update charts, on the fly and save all data with AJAX
         * Called from template
         */
        function gc_chart_save_callback() {

            global $wpdb;

            $data          = $_POST['data'];
            $chart_action  = $_POST['chart_action']  = array_key_exists('chart_action', $data) ? $data['chart_action'] : null;
            $chart_slug    = array_key_exists('chart_slug', $data) ? $data['chart_slug'] : null;
            $chart_type    = array_key_exists('chart_type', $data) ? strtolower($data['chart_type']) : null;

            error_log(__FILE__.':'.__LINE__."- chart_action: $chart_action");          error_log(print_r($data,true));

            if(!$chart_slug) {
                $chart_slug    = array_key_exists('chart_slug_d', $data) ? $data['chart_slug_d'] : null;
            }

            //FIXME: may not even need this case statement any more
            switch ($chart_action) {
                case 'new':


                    //check to see if slug aleady exists
                    $check = self::gc_sql_get_chart($chart_slug);
                    if($check[0]['slug']==$chart_slug) {
                        $chart_slug .='_copy'.rand(13434);
                    }

                    $charts_json['charts'][]['slug']        = $chart_slug;
                    $charts_json['charts'][]['label']       = $data['chart_name'];
                    $charts_json['charts'][]['description'] = $data['chart_description'];

                    //insert into database as a new
                    $wpdb->insert(
                        '_gc_charts',
                        array(
                            'type' => $chart_type,
                            'slug' => $chart_slug,
                            'name' => $data['chart_name'],
                            'description' => $data['chart_description'],
                            'status' => 1
                        ),
                        array(
                            '%s',
                            '%s',
                            '%s',
                            '%s',
                            '%d'
                        )
                    );

                case 'copy':
                    //new and copy should essentially run the same code
                    //TODO: Instead of grabbing json from starter folder, we need to use the json php file in templates/json
                    //$package_file = ABSPATH.$this->gc_frontend->chart_folder.'/charts.json';
                    $check = self::gc_sql_get_chart($chart_slug);
                    if($check[0]['slug']==$chart_slug) {
                        $chart_slug .='_copy'.rand(13434);
                    }

                    $package      = self::get_file_location('charts',$chart_slug);
                    mkdir($package);  //make directory

                    $charts_json['charts'][]['slug']        = $chart_slug;
                    $charts_json['charts'][]['label']       = $data['chart_name'];
                    $charts_json['charts'][]['description'] = $data['chart_description'];

                    //insert into database as a new
                    $wpdb->insert(
                        '_gc_charts',
                        array(
                            'type' => $chart_type,
                            'slug' => $chart_slug,
                            'name' => $data['chart_name'],
                            'description' => $data['chart_description'],
                            'status' => 1
                        ),
                        array(
                            '%s',
                            '%s',
                            '%s',
                            '%s',
                            '%d'
                        )
                    );


                    break;
                case 'edit':
                    $check = self::gc_sql_get_chart($chart_slug);


                    //if empty, create. this could still be the case
                    if(!$check[0]['slug']) {
                        $package      = self::get_file_location('charts',$chart_slug);
                        mkdir($package);  //make directory
error_log();
                        $wpdb->insert(
                            '_gc_charts',
                            array(
                                'type' => $chart_type,
                                'slug' => $chart_slug,
                                'name' => $data['chart_name'],
                                'description' => $data['chart_description'],
                                'status' => 1
                            ),
                            array(
                                '%s',
                                '%s',
                                '%s',
                                '%s',
                                '%d'
                            )
                        );
                        error_log(__FILE__.':'.__LINE__."- 1");
                    } else if($check[0]['slug']==$chart_slug) {
                        $chart_slug .='_copy'.rand(13434);
                        $package      = self::get_file_location('charts',$chart_slug);
                        mkdir($package);  //make directory
                        $wpdb->update(
                            '_gc_charts',
                            array(
                                'type' => $chart_type,
                                'slug' => $chart_slug,
                                'name' => $data['chart_name'],
                                'description' => $data['chart_description'],
                                'status' => 1
                            ),
                            array(
                                '%s',
                                '%s',
                                '%s',
                                '%s',
                                '%d'
                            )
                        );
                        error_log(__FILE__.':'.__LINE__."- 2");
                    } else {
                        $wpdb->update(
                            '_gc_charts',
                            array(
                                'type' => $chart_type,
                                'slug' => $chart_slug,
                                'name' => $data['chart_name'],
                                'description' => $data['chart_description'],
                                'status' => 1
                            ),
                            array(
                                '%s',
                                '%s',
                                '%s',
                                '%s',
                                '%d'
                            )
                        );
                        error_log(__FILE__.':'.__LINE__."- 3");
                    }

                    error_log("edit: ".$chart_slug);

                    error_log("check: ".$check[0]['slug']);





                    break;
                default:

                    error_log(__FILE__.':'.__LINE__."- DEFAULT");
            }


            $save['options']['chart_type']          = array_key_exists('chart_type', $data) ? ucwords(sanitize_text_field($data['chart_type'])) : null;
            $save['options']['chart_name']          = array_key_exists('chart_name', $data) ? sanitize_text_field($data['chart_name']) : null;
            $save['options']['segmentStrokeColor']  = array_key_exists('segmentStrokeColor', $data) ? $data['segmentStrokeColor'] : null;
            $save['options']['chart_source']        = array_key_exists('chart_source', $data) ? $data['chart_source'] : null;
            $save['options']['legend']              = array_key_exists('legend', $data) ? $data['legend'] : null;
            $save['options']['source']              = array_key_exists('source', $data) ? $data['source'] : null;
            $save['options']['name']                = array_key_exists('name', $data) ? $data['name'] : null;
            $save['options']['width']               = array_key_exists('width', $data) ? $data['width'] : null;
            $save['options']['height']              = array_key_exists('height', $data) ? $data['height'] : null;
            $animateRotate                          = array_key_exists('animateRotate', $data) ? $data['animateRotate']  : null;

            //build options and most of json
            $chart_array_data = '{
"options": {';

            foreach($save['options'] as $key=>$option) {
                if(is_numeric($option)) {
                    $chart_array_data .= '"'.$key.'": '.$option.',';
                } else {
                    $chart_array_data .= '"'.$key.'": "'.$option.'",';
                }
            }

            if($animateRotate==0) {
                $chart_array_data .= '"animateRotate": false'; //no comma!
            } else {
                $chart_array_data .= '"animateRotate": true'; //no comma!
            }

            $chart_array_data .= '
},
"data_array": [
{
"chart_data": [';

            //FIXME: to fix count issue, need to pull current json, turn into array, and count
            //use session to do this

            $jsonfile      = self::get_file_location('charts',$chart_slug).'/index.json';
            $existing_data = json_decode(file_get_contents($jsonfile),true);
            error_log("jsonfile: $jsonfile");
            $count = count($existing_data['data_array'][0]['chart_data']);

            error_log(print_r($existing_data,true));
            error_log("newcount: $count");

            for ($i = 0; $i <= 10; $i++) {
                if(empty($data['chart_data['.$i])) {
                    continue;
                } else {
                    $chart_array_data .= '{';
                    foreach($data['chart_data['.$i] as $k=>$v) {
                        if(is_numeric($v)) {
                            $chart_array_data .= '"'.$k.'": '.$v.',';
                        } else {
                            $chart_array_data .= '"'.$k.'": "'.$v.'",';
                        }
                    }
                    $chart_array_data = rtrim($chart_array_data, ","); //cut that last comma off
                    $chart_array_data .= '},';
                }
            }
            $chart_array_data = rtrim($chart_array_data, ","); //cut that last comma off
            $chart_array_data .= ']
}
]
}';

            $save_result = self::gc_save_file(self::get_file_location('charts',$chart_slug).'/index.json',$chart_array_data);
            $new         = json_decode($chart_array_data,true);  //unfortunately need data for gc_side
            $html        = self::gc_side($new,$chart_slug,$save['options']['chart_type']);


            //return html as chart update
            $return = array(
                'html'=>$html,
                'save_result'=>$save_result,
                'chart_array_data'=>$chart_array_data,
                'shortcode'=>"[cap_chart chart='$chart_slug']", //in case there is a change
                'chart_slug'=>$chart_slug
            );

            wp_send_json($return);
            wp_die();

        }


        /**
         * Save input on autoupdate
         * TODO: return javascript to run an update
         */
        function gc_chart_save_input_callback()
        {

            error_log('id: '.$_POST['id']);

            switch ($_POST['id']) {
                case 'chart_name':

                    break;
                case 1:
                    echo "i equals 1";
                    break;
                case 2:
                    echo "i equals 2";
                    break;
            }

            //TODO: need function for adding and subtracting from array, and saving json file



            $return = array(
                'post'=>$_POST,
                'script'=>$script
            );

            wp_send_json($return);
            wp_die();

        }

        /**
         * Takes a json file, and an array
         * Takes array and saves to json
         * @param $jsonfile
         * @param $data
         * @return mixed
         */
        function gc_save_array_to_json($jsonfile,$data) {
            $fh = fopen($jsonfile, "w");
            if ($fh == false) {
                $saveresult = 0;
            } else {
                fputs($fh, $data);
                fclose($fh);
                $saveresult = 1;
            }

            return $saveresult;
        }

            /**
         * Get FOLDER LOCATION.  not the file since files may change to index.json
         * @param $type
         * @param $slug
         * @return mixed
         */
        public static function get_file_location($type,$slug) {
            global $options;

            //TODO: returns local package library for now. But needs to go into media library. check options here

            //error_log('get_file_location: '.plugin_dir_path( __FILE__ ).'packages/'.$type.'/'.$slug.'/');

            //$settings = new Cap_Graphics_Settings();
            //$options = Cap_Graphics_Settings::merge_options($settings->$app_defaults, $settings->$app_options);

            //error_log(print_r($options,true));

            $upload_dir = wp_upload_dir();
            //error_log(print_r($upload_dir,true));

            return plugin_dir_path( __FILE__ ).'packages/'.$type.'/'.$slug.'/';
        }

        /**
         * Return the package URI
         * @return string
         */
        public static function get_package_uri($type,$slug) {
            return plugin_dir_url( __FILE__ ).'packages/'.$type.'/'.$slug;
        }

        /**
         * Get just the filename
         * @param $type
         * @return string
         */
        public static function gc_get_package_file($type) {
            return plugin_dir_path(__FILE__).'/packages/'.$type.'.json';  //TODO: needs to pull from options and then media library or local
        }

        /**
         * Get proper location, either in local or media library, then return charts.
         * @param $type
         * @return string
         */
        public static function gc_get_package($type) { error_log("gc_get_package: ".self::gc_get_package_file($type));
            return file_get_contents(self::gc_get_package_file($type));
        }

        /**
         * Get charts or svg from database
         * @param $type
         * @param $status
         * @return array|null|object
         */
        public static function gc_get_package_db($type,$status) {

            global $wpdb;

            if($type=='svg') {
                $sql =  "SELECT * FROM _gc_svg WHERE status = $status;";
            } else {
                $sql =  "SELECT * FROM _gc_charts WHERE status = $status;";
            }


            $results = $wpdb->get_results($sql, ARRAY_A );
            return $results;
        }

        public static function gc_item_status_callback() {

            $slug       = array_key_exists('slug', $_POST) ? $_POST['slug'] : null;
            $type       = array_key_exists('type', $_POST) ? $_POST['type'] : null;
            $status     = array_key_exists('status', $_POST) ? intval($_POST['status']) : null;
            $result     = self::gc_item_status($type,$slug,$status);

            $return   = array(
                'result'=> $result
            );

            wp_send_json($return);
            wp_die();
        }

        /**
         * Change status of an item
         * @param $type
         * @param $slug
         * @param $status
         * @return array|null|object
         */
        public static function gc_item_status($type,$slug,$status) {

            global $wpdb;

            if($type=='svg') {
                $table = '_gc_svg';
            } else {
                $table = '_gc_charts';
            }

            $results = $wpdb->update(
                $table,
                array(
                    'status' => $status
                ),
                array( 'slug' => $slug ),
                array(
                    '%d'
                ),
                array( '%s' )
            );
            return $results;
        }

        /**
         * TODO: this used to save meta box, but now we need to save charts outside of post system
         * THIS FUNCTION DECOM NO LONGER USED
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

            //$gc      = new Cap_Map();


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
                    $file         = ABSPATH.$this->gc_frontend->chart_folder.$chart_slug.'/index.json';
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
                    self::gc_save_json_file($package_file,$charts_json);
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

                $save_result = self::gc_save_json_file($file,$save);
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

            error_log("780: $file");
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
            $fh = fopen($file, "w+");
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

            wp_enqueue_style('gc', plugin_dir_url(__FILE__).'assets/css/frontend.css');

            $content     = '';
            $id          = get_the_ID();
            $svg_slug    = $atts['svg'];
            $package_dir = self::get_file_location('svg',$svg_slug);
            $package_uri = self::get_package_uri('svg',$svg_slug);
            $custom_js   = $package_uri . '/index.js';
            $custom_css  = $package_uri. '/index.css';
            $svg_file    = $package_dir. '/index.svg';
            $db          = self::gc_sql_get_svg_files($svg_slug); //firstget this svg from database, so we know if we include d3

            if($db[0]) {
                $files = str_getcsv($db[0]->extra_files);

                $i = 1;
                foreach($files as $file) {
                    wp_enqueue_script('gc-'.$i,  plugin_dir_url(__FILE__).'/assets/js/common/'.trim($file));
                    $i++;
                }
            }

            //if no svg, error out
            if (file_exists($svg_file)) {
                $data['svg_data'] =  file_get_contents($svg_file);
            }

            //include js and css IF exists
            if (file_exists($package_dir.'index.js')) {
                wp_enqueue_script('js-' . $id, $custom_js, '', '1', true);
            }

            if (file_exists($package_dir.'index.css')) {
                wp_enqueue_style('css-' . $id, $custom_css);
            }
            $data['svg_slug'] = $svg_slug;


            $content          .= self::gc_get_template($data,'svg-view.php');

            return $content;
        }

        /**
         * Return chart depending on options selected for this ID
         * @param $atts
         * @return string
         */
        public static function gc_chart_shortcode( $atts ){
            $content = $legend_html = $legend_inner = $canvas_html = '';

            $id         = get_the_ID();
            $chart_slug = $atts['chart'];

            //get frontend css and charts scripts
            wp_enqueue_style('gc', plugin_dir_url(__FILE__).'assets/css/frontend.css');
            wp_enqueue_script('charts',  plugin_dir_url(__FILE__).'assets/js/common/Chart.min.js','','1',true);
            wp_enqueue_script('charts',  plugin_dir_url(__FILE__).'assets/js/common/charts.options.js','','1',true);

            //instead of all this , just call new function
            $json_file    = self::get_file_location('charts',$chart_slug).'/index.json';
            $jsonfile_uri = self::get_package_uri('charts',$chart_slug).'/index.json';
            $data         = json_decode(file_get_contents($json_file),true);
            $html         = self::gc_side($data,$chart_slug,$data['options']['chart_type']);
            $html        .= <<< EOS
<script>
 jQuery(document).ready(function($) {
    $.getJSON("$jsonfile_uri").done(function( json ) {
        var str = json.options.chart_type.toString();
        var c   = document.getElementById("c1").getContext("2d");
        c.canvas.width = 300;
        c.canvas.height = 300;
        new Chart(c)[str](json.data_array[0].chart_data,json.options);

    })
    .fail(function( jqxhr, textStatus, error ) {
        var err = textStatus + ", " + error;
               console.log(1225);
        console.log( "Request Failed: " + err );
    });
});
</script>
EOS;

            return $html;
        }


        /**
         * AJAX: handles all chart actions for EDIT,COPY and NEW
         *
         */
        function gc_chart_action_callback() {

            $list = $disable = '';

            $chart_slug         = array_key_exists('chart_slug', $_POST) ? $_POST['chart_slug'] : null;
            $chart_action       = array_key_exists('chart_action', $_POST) ? $_POST['chart_action'] : null;
            error_log("chart_action: $chart_action");
            //messages and whether or not we pull json from starter file should be switched here
            switch ($chart_action) {
                case 'copy':
                    $d_place      = '';
                    $chart_slug_d = $chart_slug.'_copy';
                    $jsonfile           = self::get_file_location('charts',$chart_slug).'/index.json';
                    $jsonfile_uri       = self::get_package_uri('charts',$chart_slug).'/index.json';
                    $db_data            = self::gc_sql_get_chart($chart_slug);
                    $chart_name         = $db_data[0]['name'];  //should come from DB
                    $chart_type         = $db_data[0]['type']; //should come from DB
                    $chart_description  = $db_data[0]['description']; //should come from DB
                    break;
                case 'edit':
                    $d_place            = 'Cannot change slug: '.$chart_slug;
                    $disable            = 'disabled';
                    $jsonfile           = self::get_file_location('charts',$chart_slug).'/index.json';
                    $jsonfile_uri       = self::get_package_uri('charts',$chart_slug).'/index.json';
                    $db_data            = self::gc_sql_get_chart($chart_slug);
                    $chart_name         = $db_data[0]['name'];  //should come from DB
                    $chart_type         = $db_data[0]['type']; //should come from DB
                    $chart_description  = $db_data[0]['description']; //should come from DB
                    break;
                case 'new':
                    //pull json from starter package which is ALWAYS LOCAL, depending on type
                    $chart_type         = array_key_exists('chart_type', $_POST) ? $_POST['chart_type'] : null;
                    $jsonfile           = plugin_dir_path( __FILE__ ).'assets/chart_starter/'.$chart_type.'.json';  error_log("jsonfile: $jsonfile");
                    $jsonfile_uri       = plugin_dir_url( __FILE__ ).'assets/chart_starter/'.$chart_type.'.json';  error_log("jsonfile_uri: $jsonfile_uri");

                    $d_place            = 'Enter a Slug with Underscores Not Spaces';
                    break;
                default:
                    $d_place  = 'Enter a Slug with Underscores Not Spaces';
            }




            $json               = file_get_contents($jsonfile); //error_log(print_r($json,true));
            $data               = json_decode($json,true);
            $chart_source       = isset($data['options']['chart_source']) ? $data['options']['chart_source']  : null;
            $segmentStrokeColor = isset($data['options']['segmentStrokeColor']) ? $data['options']['segmentStrokeColor']  : null;
            $width              = isset($data['options']['width']) ? $data['options']['width']  : null;
            $height             = isset($data['options']['height']) ? $data['options']['height']  : null;
            $source             = isset($data['options']['source']) ? $data['options']['source']  : null;
            $legend             = isset($data['options']['legend']) ? $data['options']['legend']  : null;
            $name               = isset($data['options']['name']) ? $data['options']['name']  : null;
            $chart_data         = self::get_chart_data($chart_type,'',$data);  //error_log("chart_data:"); error_log($chart_data);
            $chart_type_camel   = ucwords($chart_type);




            if($data['options']['animateRotate']=='1') {
                $animateRotate  = 1;
            } else {
                $animateRotate  = 0;
            }

            //get default values for switches
            switch ($name) {
                case 0:
                    $name_disable    = 'selected';
                    $name_enable     = '';
                    $name_1          = '';
                    $name_2          = 'checked';
                    $show_chart_name = '';
                    break;
                case 1:
                    $name_enable     = 'selected';
                    $name_disable    = '';
                    $name_1          = 'checked';
                    $name_2          = '';
                    $show_chart_name = $chart_name;
                    break;
                default:
                    $name_disable    = 'selected';
                    $name_enable     = '';
                    $name_1          = '';
                    $name_2          = 'checked';
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
                    $legend_disable  = 'selected';
                    $legend_enable   = '';
                    $legend_1        = '';
                    $legend_2        = 'checked';
                    break;
                case 1:
                    $legend_enable   = 'selected';
                    $legend_disable  = '';
                    $legend_1        = 'checked';
                    $legend_2        = '';
                    break;
                default:
                    $legend_disable  = 'selected';
                    $legend_enable   = '';
                    $legend_1        = '';
                    $legend_2        = 'checked';
            }

            switch ($animateRotate) {
                case false:
                    $animateRotate_disable  = 'selected';
                    $animateRotate_enable   = '';
                    $animateRotate_1        = '';
                    $animateRotate_2        = 'checked';
                    break;
                case true:
                    $animateRotate_enable   = 'selected';
                    $animateRotate_disable  = '';
                    $animateRotate_1        = 'checked';
                    $animateRotate_2        = '';
                    break;
                default:
                    $animateRotate_disable  = 'selected';
                    $animateRotate_enable   = '';
                    $animateRotate_1        = '';
                    $animateRotate_2        = 'checked';
            }


            //get from template function
            $side = self::gc_side($data,$chart_slug,$chart_type,1);

            $html = <<< EOS
<div class="left">
    <form method="post" id="frm_chart_update">
        <ul class="sub">
            <li>
                <dd>Chart Slug</dd>
                <input type="text" name="chart_slug_d" id="chart_slug_d" value="$chart_slug_d" placeholder="$d_place" $disable />
            </li>
            <li>
                <dd>Chart Name</dd>
                <input type="text" class="autoupdate" name="chart_name" id="chart_name" placeholder="Enter a Chart Name with No Special Characters" value="$chart_name" />
            </li>
            <li>
                <dd>Data Source</dd>
                <input type="text" class="autoupdate" name="chart_source" id="chart_source" placeholder="Enter a url for the source of this data" value="$chart_source" />
            </li>
            <li>
                <dd>Description</dd>
                <textarea class="autoupdate text" name="chart_description" id="chart_description">$chart_description</textarea>
            </li>
            <li>
                <dd>Line Color</dd>
                <input type="text" class="colorpicker autoupdate" id="segmentStrokeColor" name="segmentStrokeColor" value="$segmentStrokeColor" required />
            </li>
            <li>
                <dd>Chart Height</dd>
                <input type="number"  class="c_1_4" name="height" id="height" placeholder="Enter height" value="$height" />
            </li>
            <li>
                <dd>Chart Width</dd>
                <input type="number" class="c_1_4" name="width" id="width" placeholder="Enter width" value="$width" />
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
            <li><div class="note">NOTE:  The legend is only for Doughnut and Pie Charts</div><h4><a href="javascript:void(0);" class="add-field" data-type="$chart_type">Add Line to Chart</a></h4></li>
            <li>
                <input type="hidden" id="chart_action" name="chart_action" value="$chart_action" />
                <input type="hidden" id="chart_slug" name="chart_slug" value="$chart_slug" />
                <input type="hidden" id="chart_type" name="chart_type" value="$chart_type" />
            </li>
            <ul class="chart_data_wrap">
                <li>$chart_data</li>
            </ul>
        </ul>
    </form>
</div>
<div class="left">
    $side
</div>
EOS;

            $html .= <<< EOS
<script>
 jQuery(document).ready(function($) {
    $.getJSON("$jsonfile_uri").done(function( json ) {
        new Chart(document.getElementById('c1').getContext('2d')).$chart_type(json.data_array[0].chart_data,json.options);
    })
    .fail(function( jqxhr, textStatus, error ) {
        var err = textStatus + ", " + error;
               console.log(1225);
        console.log( "Request Failed: " + err );
    });
});
</script>
EOS;

            $return = array(
                'html'=>$html,
                'json'=> $json,
                'data'=> print_r($data,true),
                'chart_name' =>$chart_name,
                'chart_data' =>$chart_data,
                'json_file'  => $jsonfile
            );

            wp_send_json($return);
            wp_die();
        }


        /**
         * @param $data
         * @return string
         */
        public static function gc_side($data,$chart_slug,$chart_type,$admin = NULL) {
            if($data['options']['name']) {
                $chart_name = '<h3>'.$data['options']['chart_name'].'</h3>';
            } else {
                $chart_name = '';
            }

            if($data['options']['source']) {
                $chart_source = '<p class="source">'.$data['options']['chart_source'].'</p>';
            } else {
                $chart_source = '';
            }
            $l_type = strtolower($chart_type);

            if($l_type=='pie' || $l_type=='doughnut') {
                $legend_inner = $canvas_type = '';
                if($data['options']['legend']==1) {
                    $legend_inner = '<div class="left">
                        <div class="legend">
                            <ul>';
                    foreach ($data['data_array'][0]['chart_data'] as $c) {
                        $legend_inner .= '
                            <li>
                                <div style="background-color: ' . $c['color'] . '"></div>
                                <p>' . $c['label'] . '</p>
                            </li>';
                    }
                    $legend_inner .= '</ul>
                        </div>
                    </div>';
                    $canvas_type = 'class="left"';
                }
            }


            if($admin) {
                $canvas_html = '<canvas id="c1" width="300" height="300" style="width: 300px; height: 300px;"></canvas>';  //on admin page, keep chart small
                $admin_html = <<<EOS
<div class="clear"> 
    <div class="short-cnt">
        <input type="text" value="[cap_chart chart='$chart_slug']" class="shortcode" />
    </div> 
    <div class="float">
        <input type="button" class="button button-primary goback" value="go back" data-url="/wp-admin/admin.php?page=cap-graphics-charts" />
    </div> 
    <div class="float">
        <input type="button" class="button button-2 chart-update" name="save_options" value="save" />
    </div> 
</div>
EOS;
            } else {
                $canvas_html = '<canvas id="c1" width="'.$data['options']['width'].'" height="'.$data['options']['height'].'"></canvas>';
                $admin_html = '';
            }

                return  <<< EOS
            <div class="chart $chart_slug $chart_type">
                {$chart_name}
                <div $canvas_type>$canvas_html {$chart_source}</div>
                $legend_inner
            </div>
            $admin_html
EOS;

        }

        /**
         * AJAX: get a line and prepend depending on chart type
         */
        function gc_chart_action_line_callback() {

            $chart_type = array_key_exists('chart_type', $_POST) ? $_POST['chart_type'] : null;
            $number     = array_key_exists('number', $_POST) ? $_POST['number'] : null;
            $id         = $number;  //don't add a 1 because the array starts at 0
            $chart_data = self::get_chart_data($chart_type,$id);

            $return = array(
                'id'=>$id,
                'post'=>$_POST,
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
        function get_chart_data($chart_type,$id,$data = NULL) {

            error_log(__LINE__." - chart_type: $chart_type");
            $chart_type = strtolower($chart_type);
            $chart_data = '';
            switch ($chart_type) {
                case 'doughnut':


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
                                    <input type="number" class="c_1_4" name="chart_data[$k][value]" value="{$v['value']}" />
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
                        <input type="number" class="c_1_4" name="chart_data[$id][value]" value="10" />
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
                case 'pie':


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
                                    <dd>Label</dd>
                                    <input type="text" name="chart_data[$k][label]" value="{$v['label']}" />
                                </li>
                                <li>
                                    <dd>Value</dd>
                                    <input type="number" class="c_1_4" name="chart_data[$k][value]" value="{$v['value']}" />
                                </li>
                                <li>
                                    <dd>Color</dd>
                                    <input type="text" class="colorpicker" name="chart_data[$k][color]" value="{$v['color']}" data-default-color="{$v['color']}" />
                                </li>
                                <li>
                                    <dd>Highlight</dd>
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
                        <dd>Label</dd>
                        <input type="text" name="chart_data[$id][label]" value="Enter Label" />
                    </li>
                    <li>
                        <dd>Value</dd>
                        <input type="number" class="c_1_4" name="chart_data[$id][value]" value="10" />
                    </li>
                    <li>
                        <dd>Color</dd>
                        <input type="text" class="colorpicker" name="chart_data[$id][color]" value="#ba575a" data-default-color="#ba575a" />
                    </li>
                    <li>
                        <dd>Highlight</dd>
                        <input type="text" class="colorpicker" name="chart_data[$id][highlight]" value="#a92d31"  data-default-color="#a92d31" />
                    </li>
                </ul>
            </li>

EOS;

                    }
                        break;


                default:
                    error_log("out default case: $chart_type");
            }

            $chart_data.= <<<E_ALL

<script>

jQuery(document).ready(function($) {
   checkColor();
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
            //$svg = $js = $css = $json = $html = '';
            $html        = '';
            $time_start  = self::timer();
            $id          = array_key_exists('id', $_POST) ? $_POST['id'] : null;
            $svg_slug    = array_key_exists('svg_slug', $_POST) ? $_POST['svg_slug'] : null;
            $svg_action  = array_key_exists('svg_action', $_POST) ? $_POST['svg_action'] : null;

            $package_dir = self::get_file_location('svg',$svg_slug);
            $package_uri = self::get_package_uri('svg',$svg_slug);
            $results     = self::gc_sql_get_graphic($svg_slug);               error_log(print_r($results,true));   //get name and desc from db

            switch ($svg_action) {
                case 'copy':
                    $data['svg_admin'] = '<ul class="sub"><li><dd>SVG Slug</dd><input type="text" value="'.$svg_slug.'_copy" id="svg_slug" /><div class="note">Click on any of the save icons to save a new SVG Graphic</div></li>';
                    break;
                case 'edit':
                    $data['svg_admin'] = '<ul class="sub"><li><input type="hidden" id="svg_slug" name="svg_slug" value="'.$svg_slug.'" /></li>';



                    break;
                case 2:

                    break;
            }


            //name and description
            $data['svg_admin'] .= <<<NCURSES_KEY_EOS
        <li>
            <dd>SVG Name</dd>
            <input type="text" id="svg_name" value="{$results[0]['name']}" />
        </li>
        <li>
            <dd>SVG Description</dd>
            <textarea id="svg_description" class="text">{$results[0]['description']}</textarea>
        </li>
        <li>
            <dd>Extra Files</dd>
            <input type="text" id="extra_files" value="{$results[0]['extra_files']}" />
            <p class="note">Enter comma separated filepaths to extra files needed by this graphic</p>
        </li>
        </ul>
NCURSES_KEY_EOS;



            //set up the d3 support switches DO NOT build this in
/*
            switch ($d3) {
                case 0:
                    $d3_disable    = 'selected';
                    $d3_enable     = '';
                    $d3_1          = '';
                    $d3_2          = 'checked';
                    break;
                case 1:
                    $d3_enable     = 'selected';
                    $d3_disable    = '';
                    $d3_1          = 'checked';
                    $d3_2          = '';
                    break;
                default:
                    $d3_disable    = 'selected';
                    $d3_enable     = '';
                    $d3_1          = '';
                    $d3_2          = 'checked';
            }



            $data['svg_admin'] .= <<<NCURSES_KEY_EOS
        <ul>
            <li class="switch">
                <div>Enable D3 Support</div>
                <label class="cb-enable $d3_enable" data-class="name"><span>Yes</span></label> <input type="checkbox" name="name" value="1" id="name_enabled" $d3_1 />
                <label class="cb-disable $d3_disable" data-class="name"><span>No</span></label> <input type="checkbox" name="name" value="0" id="name_disabled" $d3_2 />
            </li>
        </ul>
NCURSES_KEY_EOS;
*/




            $js_file = $package_dir.'index.js';
            if(file_exists($js_file)) {
                $data['js'] = file_get_contents($js_file);
            }

            $css_file = $package_dir.'index.css';
            if(file_exists($css_file)) {
                $data['css'] = file_get_contents($css_file);
            }

            $svg_file = $package_dir.'index.svg';
            if(file_exists($svg_file)) {
                $data['svg'] = file_get_contents($svg_file);
            }

            $json_file = $package_dir.'index.json';
            if(file_exists($json_file)) {
                $data['json'] = file_get_contents($json_file);
            }

            //keep preview simple
            //error_log("package_uri: $package_uri");
            //wp_enqueue_style( $svg_slug,  $package_uri . '/index.css');
            //wp_enqueue_script( $svg_slug, $package_uri. '/index.js');
            //enqueue files
            $data['svg_slug']   = $svg_slug;
            $data['svg_action'] = $svg_action;
            $data['svg_data']   = '<div id="svg-'.$svg_slug.'" class="svg-wrap">
<div class="svg_pre"></div>
<div class="svg_wrap">'.$data['svg'].'</div>
<div class="svg_post_meta"></div>
<div class="svg_post"></div>
</div>';




            $html = self::gc_svg_tpl($data);
            error_log("data");
            //$html = self::gc_get_template($data,'admin/svg-edit.php');  //doesn't work
            //$html = self::gc_get_template($data,'admin/svg-edit.php');

            error_log($data);
            error_log("after data");
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
        public static function gc_svg_tpl($data) {

            return <<<NCURSES_KEY_EOS
<div class="meta-top">
    <div class="left">
        <h3>SVG Graphic:  {$data['svg_slug']} </h3>
        {$data['svg_admin']}
    </div>
    <div class="left r">
        <input type="button" class="button button-primary goback" value="go back" data-url="/wp-admin/admin.php?page=cap-graphics-svg" />
        <input type="button" class="button button-2 save-svg" value="save" />
    </div>
</div>


<ul class="sub svg_edit">
<li>
    <span>SVG File</span>
    <textarea name="svg_data" id="svg_data">{$data['svg']}</textarea>
</li>
<li>
    <span>Javascript Code</span>
    <textarea name="js_data" id="js_data">{$data['js']}</textarea>
</li>
<li>
    <span>CSS Styles</span>
    <textarea name="css_data" id="css_data">{$data['css']}</textarea>
</li>
<li>
    <span>JSON Data</span>
    <textarea name="json_data" id="json_data">{$data['json']}</textarea>
</li>
<li><input type="hidden" id="svg_action" name="svg_action" value="{$data['svg_action']}" /></li>
</ul>
<div id="svg-preview"><h3>SVG Preview:  {$data['svg_slug']}</h3><p>This is meant to be a simple preview of the SVG file.  </p>{$data['svg_data']}</div>
NCURSES_KEY_EOS;


        }


        /**
         * AJAX: view file in admin
         */
        function gc_chart_view_callback()
        {

            $return   = array(
                'post'=> $_POST
            );

            wp_send_json($return);
            wp_die();
        }

        /**
         * AJAX: save file from textarea
         */
        function gc_file_save_action_callback()
        {
            global $wpdb;

            $svg_action      = array_key_exists('svg_action', $_POST) ? $_POST['svg_action'] : null;
            $svg_slug        = array_key_exists('svg_slug', $_POST) ? $_POST['svg_slug'] : null;
            $svg_name        = array_key_exists('svg_name', $_POST) ? $_POST['svg_name'] : null;
            $svg_action      = array_key_exists('svg_action', $_POST) ? $_POST['svg_action'] : null;
            $svg_description = array_key_exists('svg_description', $_POST) ? $_POST['svg_description'] : null;
            $extra_files     = array_key_exists('extra_files', $_POST) ? $_POST['extra_files'] : null;
            $svg_data        = array_key_exists('svg_data', $_POST) ? stripslashes($_POST['svg_data']) : null;  //use strip slashes because of MAGIC QUOTES
            $js_data         = array_key_exists('js_data', $_POST) ? stripslashes($_POST['js_data']) : null;  //use strip slashes because of MAGIC QUOTES
            $css_data        = array_key_exists('css_data', $_POST) ? stripslashes($_POST['css_data']) : null;  //use strip slashes because of MAGIC QUOTES
            $json_data       = array_key_exists('json_data', $_POST) ? stripslashes($_POST['json_data']) : null;  //use strip slashes because of MAGIC QUOTES
            $folder          = self::get_file_location('svg',$svg_slug);

            switch ($svg_action) {
                case 'copy':
                    //save in database, but may already exist

                    $select = self::gc_sql_get_graphic($svg_slug);

                    if($select) {
                        $svg_slug = $svg_slug.'_copy'.rand(77); //error_log("new svg");  error_log(print_r($select,true));
                        $svg_new  = '1';
                    } else {
                        $svg_new  = '0';
                    }

                    //insert into database as a new
                    $wpdb->insert(
                        '_gc_svg',
                        array(
                            'type' => 'svg',
                            'slug' => $svg_slug,
                            'name' => $svg_name,
                            'description' => $svg_description,
                            'status' => 1,
                            'extra_files' => $extra_files
                        ),
                        array(
                            '%s',
                            '%s',
                            '%s',
                            '%s',
                            '%s',
                            '%s'
                        )
                    );

                    break;
                case 'edit':

                    $wpdb->update(
                        '_gc_svg',
                        array(
                            'type' => 'svg',
                            'slug' => $svg_slug,
                            'name' => $svg_name,
                            'description' => $svg_description,
                            'status' => 1,
                            'extra_files' => $extra_files
                        ),
                        array('slug'=>$svg_slug),
                        array(
                            '%s',
                            '%s',
                            '%s',
                            '%s',
                            '%s',
                            '%s'
                        )
                    );

                    break;
                case 2:
                    echo "i equals 2";
                    break;
            }

            if(!file_exists($folder)) {
                mkdir($folder);
            }

            //save all files regardless
            $file_types = array('svg'=>$svg_data,'js'=>$js_data,'css'=>$css_data,'json'=>$json_data);

            foreach($file_types as $file=>$data) {
                $fh = fopen($folder.'index.'.$file, "w");
                if ($fh == false) {
                    $result = 0;
                } else {
                    fputs($fh, $data);
                    fclose($fh);
                    $result = 1;
                }
            }

            $return   = array(
                'result'=> $result,
                'svg_slug'=> $svg_slug,
                'svg_new'=>$svg_new
            );

            wp_send_json($return);
            wp_die();
        }

        /**
         * Return just the file, to include on front end
         * @param $slug
         * @return array|null|object
         * @internal param $id
         */
        public static function gc_sql_get_svg_files($slug) {

            global $wpdb;

            $sql =  "SELECT extra_files FROM _gc_svg WHERE slug = '$slug';";
            $results = $wpdb->get_results($sql, OBJECT );
            return $results;
        }

        /**
         * Simple get query, returns all
         * @param $svg_slug
         * @return array|null|object
         */
        public static function gc_sql_get_graphic($svg_slug) {

            global $wpdb;

            $sql =  "SELECT * FROM _gc_svg WHERE slug = '$svg_slug';";
            $results = $wpdb->get_results($sql, ARRAY_A );
            return $results;
        }

        /**
         * Simple get query, returns all
         * @return array|null|object
         */
        public static function gc_sql_get_all_graphics() {
            global $wpdb;

            $sql =  'SELECT * FROM _gc_svg;';
            $results = $wpdb->get_results($sql, OBJECT );
            return $results;
        }

        /**
         * Get a chart
         * @param $slug
         * @return array|null|object
         */
        public static function gc_sql_get_chart($slug) {

            global $wpdb;

            $sql =  "SELECT * FROM _gc_charts WHERE slug = '$slug';";
            $results = $wpdb->get_results($sql, ARRAY_A );
            return $results;
        }

        /**
         * DECOM: no longer rewriting json files
         * change status of item.

        public static function gc_item_status_callback() {

            $slug       = array_key_exists('slug', $_POST) ? $_POST['slug'] : null;
            $type       = array_key_exists('type', $_POST) ? $_POST['type'] : null;
            $status     = array_key_exists('status', $_POST) ? intval($_POST['status']) : null;
            $file       = self::gc_get_package_file($type); error_log("file: $file");
            $packages   = self::gc_get_package($type);
            $json       = json_decode($packages,true);

            //rearrange array
            $new = array();
            foreach($json[$type] as $k=>$v) {
                if($v['slug']==$slug) {
                    $v['status'] = $status;
                }
                $new[$type][] = $v;
            }

            //save new array as json
            $result = self::gc_save_array_to_json($file,json_encode($new));

            $return   = array(
                'new'=> $new,
                'result'=> $result
            );

            wp_send_json($return);
            wp_die();
        }
    */
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
            include(WP_CONTENT_DIR . self::PLUGIN_DIR . self::APP_DIR.'/assets/templates/'.$template); //error_log(WP_CONTENT_DIR . self::PLUGIN_DIR . self::APP_DIR.'/assets/templates/'.$template);
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

        //svg
        add_action( 'wp_ajax_gc_svg_action', 'Cap_Graphics::gc_svg_action_callback' );  //ajax for new svg
        add_action( 'wp_ajax_nopriv_gc_svg_action', 'Cap_Graphics::gc_svg_action_callback' );   //ajax for new svg
        add_action( 'wp_ajax_gc_file_save_action', 'Cap_Graphics::gc_file_save_action_callback' );  //ajax for saving files
        add_action( 'wp_ajax_nopriv_gc_file_save_action', 'Cap_Graphics::gc_file_save_action_callback' );   //ajax for saving files

        //charts
        add_action( 'wp_ajax_gc_chart_action', 'Cap_Graphics::gc_chart_action_callback' );  //ajax for new chart
        add_action( 'wp_ajax_nopriv_gc_chart_action', 'Cap_Graphics::gc_chart_action_callback' );   //ajax for new chart

        add_action( 'wp_ajax_gc_chart_line_action', 'Cap_Graphics::gc_chart_action_line_callback' );  //ajax for adding a line to new chart
        add_action( 'wp_ajax_nopriv_gc_chart_line_action', 'Cap_Graphics::gc_chart_action_line_callback' );   //ajaxfor adding a line to new chart

        add_action( 'wp_ajax_gc_chart_save', 'Cap_Graphics::gc_chart_save_callback' );  //save chart
        add_action( 'wp_ajax_nopriv_gc_chart_save', 'Cap_Graphics::gc_chart_save_callback' );   //save chart

        add_action( 'wp_ajax_gc_item_status', 'Cap_Graphics::gc_item_status_callback' );  //save chart
        add_action( 'wp_ajax_nopriv_gc_item_status', 'Cap_Graphics::gc_item_status_callback' );   //save chart


        //TODO: phase ii add autoupdate on input
        //add_action( 'wp_ajax_gc_chart_save_input', 'Cap_Graphics::gc_chart_save_input_callback' );  //save chart input box
        //add_action( 'wp_ajax_nopriv_gc_chart_save_input', 'Cap_Graphics::gc_chart_save_input_callback' );   //save chart input box

        //TODO: phase ii may want to also add view button
        //add_action( 'wp_ajax_gc_chart_view', 'Cap_Graphics::gc_chart_view_callback' );  //view chart
        //add_action( 'wp_ajax_nopriv_gc_chart_view', 'Cap_Graphics::gc_chart_view_callback' );   //view chart

    } else {
        add_shortcode( 'cap_svg', 'Cap_Graphics::gc_svg_shortcode' );  //register shortcode for svg
        add_shortcode( 'cap_chart', 'Cap_Graphics::gc_chart_shortcode' );  //register shortcode
    }

}
