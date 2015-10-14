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

        var $page    = '';
        var $message = 0;

        function __construct()
        {
            add_action('admin_menu', array($this, 'init'));
        }

        function init()
        {
            if (!current_user_can('update_plugins'))
                return;
            
            // Add a new submenu
            /*
            $this->page = $page = add_options_page(
                __(parent::SETTINGS_PAGE_TITLE, parent::APP_SLUG), __(parent::SETTINGS_PAGE_TITLE, parent::APP_SLUG), 'administrator', parent::APP_SLUG, array($this, 'option_function'));
*/
            //$this->page = $page = add_options_page(
                //__(parent::SETTINGS_PAGE_TITLE, parent::APP_SLUG), __(parent::SETTINGS_PAGE_TITLE, parent::APP_SLUG), 'administrator', parent::APP_SLUG,'Pdf_Generator_Options::option_function');

            //$this->page = $page = add_options_page(
             //   __(parent::SETTINGS_PAGE_TITLE, parent::APP_SLUG), __(parent::SETTINGS_PAGE_TITLE, parent::APP_SLUG), 'administrator', parent::APP_SLUG, array($this, 'option_function'));
            
            
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
            <p class="note"><?php print parent::APP_NAME; ?> by Amir Meshkin version <?php print parent::APP_VERSION; ?></p>
            <span> debug: <?php print parent::APP_DEBUG; ?></span>
        </div>
        <?php
        }


        /**
        * Instructions page
         */
        function instruction_submenu()
        {
            ?>
            <div class="wrap">
                <h2>CAP GRAPHICS INSTRUCTIONS</h2>
                <p></p>

            </div>
            <?php
        }

        /**
         *
         */
        function svg_submenu() {
            //TODO:  use templating system here in admin folder
            ?>
            <div class="wrap">
                <h2>SVG Maps and Graphics</h2>
                <p></p>

            </div>
            <?php
        }


    }
}
?>