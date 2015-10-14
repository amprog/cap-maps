<?php
if ( !defined( 'ABSPATH' ) ) die(); //keep from direct access

// Pre-2.6 compatibility
if (!defined('WP_CONTENT_URL'))
    define('WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
if (!defined('WP_CONTENT_DIR'))
    define('WP_CONTENT_DIR', ABSPATH . 'wp-content');

define('APP_CLASS_NAME', 'Cap_Map');

if (!class_exists(APP_CLASS_NAME)) {

    class Pdf_Generator
    {
        const APP_VERSION = '1.0';
        const APP_DEBUG = 0;

        const APP_LOADER = 'pdf-generator/app-loader.php';
        const APP_NAME = 'PDF Generator';
        const APP_SLUG = 'pdf-generator';
        const APP_DIR = '/pdf-generator';
        const PLUGIN_DIR = '/plugins';
        const PDF_TPL_LOCATION = 'plugin';
        const PDF_TPL_DIR = 'pdf-templates';
        const SETTINGS_SECTION_ID = 'app_main';
        const OPTIONS_PAGE = 'app_options';
        const OPTIONS_PREFIX = 'pdf_options';
        const APP_OPTION_CLASS_NAME = 'Pdf_Generator_Options';

        //settings
        const SETTINGS_PAGE_TITLE = 'PDF Generator';

        const SETTINGS_PAGE1 = 'app_settings_page1';
        const SETTINGS_PAGE_SUBTITLE1 = 'PDF Generation Options';

        var $settings, $options_page;

        function __construct()
        {
            if (is_admin()) {

                $settings_class = APP_CLASS_NAME . '_Settings';
                $options_class = APP_CLASS_NAME . '_Options';

                if (!class_exists($settings_class))
                    require(WP_CONTENT_DIR . self::PLUGIN_DIR . self::APP_DIR . '/app-settings.php');
                $this->settings = new $settings_class();

                if (!class_exists($options_class))
                    require(WP_CONTENT_DIR . self::PLUGIN_DIR . self::APP_DIR . '/app-options.php');
                $this->options_page = new $options_class();

                //action for seettings
                add_filter('plugin_row_meta', array(&$this, '_app_settings_link'), 10, 2);
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
                    // Get all blog ids
                    $blogids = $wpdb->get_col("SELECT blog_id FROM {$wpdb->blogs}");
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
            add_menu_page(self::APP_NAME, self::APP_NAME, 'manage_options', self::APP_SLUG, self::APP_OPTION_CLASS_NAME.'::option_function',  WP_CONTENT_URL.self::PLUGIN_DIR.self::APP_DIR .'/images/menu.png');

            add_submenu_page( self::APP_SLUG, 'Instructions', 'Instructions', 'manage_options', self::APP_SLUG.'-help', self::APP_OPTION_CLASS_NAME.'::instruction_submenu' );
            //add_plugins_page( self::APP_NAME, "Instructions", 'manage_options', 'pdf-instructions', self::APP_OPTION_CLASS_NAME.'::instruction_submenu');
        }
    }
}


global $pdf_generator;
if (class_exists(APP_CLASS_NAME) && !$pdf_generator) {
    $pdf_generator = new Pdf_Generator();
}
