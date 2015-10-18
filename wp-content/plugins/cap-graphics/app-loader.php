<?php
/*
  Plugin Name: CAP Graphics
  Plugin URI: http://americanprogress.org
  Description: Create SVG Maps and Charts
  Version: 1.0
  Author: Amir Meshkin
  Author URI: http://portfolio.amir-meshkin.com
  License: ?
 */

/*  Copyright 2015  Amir Meshkin  (email : amir.meshkin@gmail.com)
    CAP - Center for American Progress
 */
if ( !defined( 'ABSPATH' ) ) die(); //keep from direct access

if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

register_activation_hook(__FILE__, 'cap_graphics_activate');

// display error message to users
if (array_key_exists('action', $_GET) && $_GET['action'] == 'error_scrape') {
    //die("This PLUGIN requires PHP 5.0 or higher. Please deactivate.");
}

function cap_graphics_activate()
{
    if (version_compare(phpversion(), '5.0', '<')) {
        trigger_error('', E_USER_ERROR);
    }
}

// require this plugin in back end, only captcha class needed in front end
if (version_compare(phpversion(), '5.0', '>=')) {
    define('WR_LOADER', __FILE__);
    require_once(dirname(__FILE__) . '/app.php');
}

?>