<?php
if ( !defined( 'ABSPATH' ) ) die(); //keep from direct access
if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

if (!class_exists("Pdf_Generator_Options")) {

    class Pdf_Generator_Options extends Pdf_Generator
    {

        var $page = '';
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
            <?php screen_icon(); ?>
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

                <h2>PDF GENERATOR INSTRUCTIONS</h2>
                
                <p>There are two steps in getting a pdf icon to show up on certain pages.  To keep people from "scraping" our site, we have to tell wordpress which pages are allowed a pdf version.</p>
    
                <p>And to control the exact location of the pdf icon, we have to also use a "short code" to get the icon to show up.</p>
    
    
                <p>On your post or page, you should see a box on the lower right hand side which says "Show PDF Icon".  If you do not, then you may need to click on SCREEN OPTIONS at the very top, and make sure "Show PDF Icon" is checked.</p>
    
                <p>Checking "Show PDF Button on this Page" for a post or page, will simply tell Wordpress that this page can have a PDF icon.</p>
    
                <p>You will still need to enter the following short code, somewhere in the post content.</p>
                
                <p>Short tag without options</p>
                <code>[download_pdf]</code>
                
                <p>With optional variables set.</p>
                <code>[download_pdf title="Test Title" download="0"  strip_img="1" template="report-pdf.php"]</code>
    
                <h4>The options explained:</h4>
    
                <p><strong>title:</strong>  The name of the PDF file, which the user will download</p>

    
                <p><strong>download:</strong> Either a 0, or a 1.</br>
                A 0 makes it so that a user can click on the PDF icon, and the PDF file opens in a new tab.   A 1 in this field makes it so that the PDF file is downloaded to the user's computer.
                </p>
    
                 <p><strong>strip_img:</strong> Either a 0, or a 1.</br>
                This plugin uses a code library called "htmldoc" and there seems to be an undiagnosed bug, where only PNG files properly show up in the PDF file.  JPG and GIF files will create an empty space in the PDF file.</p>
    
                   <p><strong>template:</strong> Name of php file.</br>
                       In the options you can set the default php file to use as a PDF template, but you can override it here by entering the full name of the php file.</p>
    
                <h4>Plugin Setup:</h4>
                <p>You also need to create two pages in the ADMIN, for this plugin to function correctly.  </p>
                <p>Create a page called "Create PDF" with the url "create_pdf", which uses the "PDF Create" template. </p>
                <p>Create a page called "View PDF" with the url "view_pdf", which uses the "PDF View" template. </p>
                
                
                
                <h4>Notes:</h4>
                <p>Although the plugin is designed to keep non standard characters, such as curly quotes out, please try not to use them.  The htmldoc library cannot handle non utf characters and they will show up as question marks in your pdf file.</p>
                 
                <p>The htmldoc library cannot handle any images in the PDF file, other than PNG files.  As of now, there is no fix.  So either strip images out, or use PNG files if you want them to show up in your PDF.</p>
                
                <p>The htmldoc library only works with basic html. Any fancy javascript, or collapsing boxes, will not show up in the pdf file.  Links should work fine.</p>
                
            </div>
            <?php
        }
    }
}
?>