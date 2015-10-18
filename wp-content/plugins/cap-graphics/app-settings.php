<?php
if ( !defined( 'ABSPATH' ) ) die(); //keep from direct access
if (!function_exists('is_admin')) {
    header('Status: 403 Forbidden');
    header('HTTP/1.1 403 Forbidden');
    exit();
}

if (!class_exists("Cap_Graphics_Settings")) {

    class Cap_Graphics_Settings extends Cap_Graphics
    {

        /**
         * Init class
*/
        function __construct()
        {
            if (is_admin()) {
                add_action('admin_init', array($this, 'admin_init'), 20);
            }

            //get options and pass to other methods
            $this->app_options = get_option(parent::OPTIONS_PREFIX);
            $this->app_defaults = array(
                    'pdf' => array(
                    'left_margin' => '.25in'
                )
            );
        }

        /**
         * Init admin functions
         */
        function admin_init()
        {
            register_setting(parent::OPTIONS_PAGE, parent::OPTIONS_PREFIX);

            $defaults   = $this->app_defaults; //pull DEFAULT options here, merge with options
            $db_options = $this->app_options; //pull options here, feed into args
            $options    = $this->merge_options($defaults, $db_options);
            error_log(__FILE__.':'.__LINE__.' - settings');
            //loop through template files for PDF Generation
            $theme_dir = wp_get_theme();


            $a1 = array(
                'aws'=>'Amazon AWS',
                'media'=>'Media Library',
                'plugin'=>'Plugin Directory'
            );


            add_settings_field('storage', 'Storage Options', array($this, 'render_drop_down'), parent::SETTINGS_PAGE1, parent::SETTINGS_SECTION_ID, array(
                'id' => 'storage',
                'section' => 'all',
                'selected' => $options['all']['storage'],
                'data' => $a1,
                'desc' => '<span class="description">Where should graphics packages be stored?</span>'
            ));



            add_settings_section(parent::SETTINGS_SECTION_ID, parent::SETTINGS_PAGE_SUBTITLE1, array($this, 'section_text1'), parent::SETTINGS_PAGE1);

            //wp_register_script('custom_js', plugin_dir_url(__FILE__) . 'js/custom.js');
            //wp_enqueue_script('custom_js');

            wp_register_style('custom_css', plugin_dir_url(__FILE__) . 'assets/css/frontend.css');
            wp_enqueue_style('custom_css');
        }

        /**
         * Text for top section
         */
        function section_text1()
        {
            echo '<p>Fill out plugin options.</p>';
        }

        /**
         * Render text field
         * @param $args
         */
        function render_text($args)
        {

            $section  = isset($args['section']) ? $args['section'] : null;
            $type     = isset($args['type']) ? $args['type'] : null;
            $class    = isset($args['class']) ? 'class="' . $args['class'] . '"' : null;
            $extra    = isset($args['extra']) ?  $args['extra'] : null;
            
            //check if option contained in section
            if ($section) {
                $name = parent::OPTIONS_PREFIX . '[' . $args['section'] . '][' . $args['id'] . ']';
            } else {
                $name = parent::OPTIONS_PREFIX . '[' . $args['id'] . ']';
            }


            if ($type == 'textarea') {
                echo '<textarea  name="' . $name . '" id="' . $args['id'] . '" ' . $class . ' '. $extra .'  >' . $args['value'] . '</textarea>';
            } else {
                echo '<input type="text" name="' . $name . '" id="' . $args['id'] . '" value="' . $args['value'] . '" ' . $class . ' '. $extra .' />';
            }

            if (isset($args['desc']) ? $args['desc'] : null) {
                print '<p>' . $args['desc'] . '</p>';
            }


            if ($type == 'color') {
                $hex = isset($args['value']) ? $args['value'] : null;
                ?>
                <div class="colorSelector">
                    <div style="background-color: #<?php echo $hex; ?>;"></div>
                </div>
            <?php
            }
        }

        /**
         * @param $args
         * if args['type'] == '#' then drop down is number range,
         * expects $args['start'] and $args['end']
         */
        function render_drop_down($args)
        {
            $section = isset($args['section']) ? $args['section'] : null;
            $type    = isset($args['type']) ? $args['type'] : null;

            if ($section) {
                $name = parent::OPTIONS_PREFIX . '[' . $args['section'] . '][' . $args['id'] . ']';
            } else {
                $name = parent::OPTIONS_PREFIX . '[' . $args['id'] . ']';
            }
            echo '<select class="wr_width" name="' . $name . '">';

            if ($type == '#') {
                foreach (range($args['start'], $args['end']) as $n) {

                    if ($args['selected'] == $n) {
                        $selected = 'selected="selected"';
                    } else {
                        $selected = '';
                    }
                    echo '<option value="' . $n . '"' . $selected . '>' . $n . '</option>';
                }
            } else {
                foreach ($args['data'] as $key => $value) {

                    if ($args['selected'] == $key) {
                        $selected = 'selected="selected"';
                    } else {
                        $selected = '';
                    }
                    echo '<option value="' . $key . '"' . $selected . '>' . $value . '</option>';
                }
            }
            echo '</select>';
            if (isset($args['desc']) ? $args['desc'] : null) {
                print '<p>' . $args['desc'] . '</p>';
            }
        }

        /**
         * Render checkbox
         * @param $args
         */
        function render_checkbox($args)
        {
            $options = $this->app_options;
            $id      = parent::OPTIONS_PREFIX . '[' . $args['id'] . ']';
            ?>
            <input name="<?php echo $id; ?>" type="checkbox"
                   value="<?php echo $args['value']; ?>" <?php echo isset($options[$args['id']]) ? 'checked' : ''; ?> /> <?php echo "{$args['text']}"; ?>
            <?php
            if (isset($args['desc']) ? $args['desc'] : null) {
                print $args['desc'];
            }
        }

        /*
         * Custom PUBLIC functions, to be used by other resources as necessary to merge default options with db options
         * Since merge_array() not working as expected
         *
         * Get option by setting name with default value if option does not exist
         * @param string $setting
         * @return mixed
         */
        public function get_option($setting)
        {
            if (is_array($this->_options[$setting])) {
                $options = array_merge($this->_options[$setting], get_option($setting));
            } else {
                $options = get_option($setting, $this->_options[$setting]);
            }
            return $options;
        }

        /**
         * Get array with options
         *
         * @return array
         */
        public function get_options()
        {
            $options = array();
            foreach ($this->_options as $option => $value) {
                $options[$option] = $this->get_option($option);
            }
            return $options;
        }

        /**
         * Merge options in db, with default ones
         *
         * @param array $default
         * @param array $opt
         * @return array
         */
        public function merge_options($default, $opt)
        {
            foreach ($default as $option => $values) {
                if (!empty($opt[$option])) {
                    $default[$option] = is_array($values) ? array_merge($values, $opt[$option]) : $opt[$option];
                    $default[$option] = is_array($values) ? array_intersect_key($default[$option], $values) : $opt[$option];
                }
            }
            return $default;
        }


        /**
         * Return true if $in is either a string or numeric
         * @param array $arr
         * @return bool
         */
        public function _is_string_or_numeric(array $arr)
        {
            if (is_string($arr)) {
                return false;
            } else if (is_numeric($arr)) {
                return true;
            } else {

            }
        }
    }
}
?>