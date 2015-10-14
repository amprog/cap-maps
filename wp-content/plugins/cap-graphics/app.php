<?php

if ( !defined( 'ABSPATH' ) ) die(); //keep from direct access

define('APP_CLASS_NAME', 'Cap_Graphics');

if (!class_exists(APP_CLASS_NAME)) {

    class Cap_Graphics
    {
        const APP_VERSION             = '1.0';
        const APP_DEBUG               = 1;
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

        var $settings, $options_page;

        function __construct()
        {

            if (is_admin()) {

                $settings_class = APP_CLASS_NAME . '_Settings';
                $options_class  = APP_CLASS_NAME . '_Options';
                //error_log(__FILE__.' - here');
                if (!class_exists($settings_class))
                    require(WP_CONTENT_DIR . self::PLUGIN_DIR . self::APP_DIR . '/app-settings.php');
                $this->settings = new $settings_class();

                if (!class_exists($options_class))
                    require(WP_CONTENT_DIR . self::PLUGIN_DIR  . self::APP_DIR . '/app-options.php');
                $this->options_page = new $options_class();

                //action for seettings
                add_filter('plugin_row_meta', array(&$this, '_app_settings_link'), 10, 2);
                //error_log(__FILE__.' - here');
            } else {
                require(WP_CONTENT_DIR . self::PLUGIN_DIR . self::APP_DIR . '/app-frontend.php');
            }

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
            add_menu_page(self::APP_NAME, self::APP_NAME, 'manage_options', self::APP_SLUG, self::APP_OPTION_CLASS_NAME.'::option_function',  WP_CONTENT_URL.self::PLUGIN_DIR.self::APP_DIR .'/assets/images/icon.png');
            add_submenu_page( self::APP_SLUG, 'Charts', 'Charts', 'manage_options', self::APP_SLUG.'-charts', self::APP_OPTION_CLASS_NAME.'::charts_submenu' );
            add_submenu_page( self::APP_SLUG, 'SVG', 'SVG', 'manage_options', self::APP_SLUG.'-svg', self::APP_OPTION_CLASS_NAME.'::svg_submenu' );
            add_submenu_page( self::APP_SLUG, 'Instructions', 'Instructions', 'manage_options', self::APP_SLUG.'-help', self::APP_OPTION_CLASS_NAME.'::instruction_submenu' );

            //add_plugins_page( self::APP_NAME, "Instructions", 'manage_options', 'pdf-instructions', self::APP_OPTION_CLASS_NAME.'::instruction_submenu');
        }

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
    }
}

global $cap_graphics;
if (class_exists(APP_CLASS_NAME) && !$cap_graphics) {
    $cap_graphics = new Cap_Graphics();

    error_log(__FILE__.':'.__LINE__);

    if ( is_admin() ) {
        add_action( 'admin_menu', 'Cap_Graphics_Frontend::gc_options_admin' );  //options page, TODO: perhaps put chart options here
        add_action( 'add_meta_boxes', "Cap_Graphics_Frontend::gc_meta"); //meta box
        add_action( 'save_post', 'Cap_Graphics_Frontend::gc_meta_save' );  //this is causing problems with new post pages

        //svg
        add_action( 'wp_ajax_cap_map_svg_action', 'Cap_Graphics_Frontend::gc_svg_action_callback' );  //ajax for new svg
        add_action( 'wp_ajax_nopriv_cap_map_svg_action', 'Cap_Graphics_Frontend::gc_svg_action_callback' );   //ajax for new svg
        add_action( 'wp_ajax_cap_map_file_save_action', 'Cap_Graphics_Frontend::gc_file_save_action_callback' );  //ajax for saving files
        add_action( 'wp_ajax_nopriv_cap_map_file_save_action', 'Cap_Graphics_Frontend::gc_file_save_action_callback' );   //ajax for saving files

        //charts
        add_action( 'wp_ajax_cap_map_chart_action', 'Cap_Graphics_Frontend::gc_chart_action_callback' );  //ajax for new chart
        add_action( 'wp_ajax_nopriv_cap_map_chart_action', 'Cap_Graphics_Frontend::gc_chart_action_callback' );   //ajax for new chart
        add_action( 'wp_ajax_cap_map_chart_line_action', 'Cap_Graphics_Frontend::gc_chart_action_line_callback' );  //ajax for adding a line to new chart
        add_action( 'wp_ajax_nopriv_cap_map_chart_line_action', 'Cap_Graphics_Frontend::gc_chart_action_line_callback' );   //ajaxfor adding a line to new chart

    } else {
        add_shortcode( 'cap_svg', 'Cap_Graphics_Frontend::gc_svg_shortcode' );  //register shortcode for svg
        add_shortcode( 'cap_chart', 'Cap_Graphics_Frontend::gc_chart_shortcode' );  //register shortcode
    }

}


