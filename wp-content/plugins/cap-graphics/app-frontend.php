<?php
if ( !defined( 'ABSPATH' ) ) die(); //keep from direct access

if (!class_exists("Pdf_Generator_Frontend")) {

    class Pdf_Generator_Frontend extends Pdf_Generator
    {

        /*
         * Set variables here for use through class
         * 
         */
        function __construct()
        {
            $this->app_options = get_option(parent::OPTIONS_PREFIX);
        }

        /**
         * Runs shortcode, needs id and download flag.
         * CHECKS TO MAKE SURE CHECKBOX IS CHECKED
         * VIEW_PDF.PHP ALSO CHECKS FOR CHECKBOX, TO MAKE SURE THAT THIS PAGE IS SUPPOSED TO BE VIEWED AS PDF
         * AND TO KEEP PEOPLE FROM SCRAPING PDF FILES AND KILLING SERVER
         * @param $atts
         * @param null $content
         * @return mixed
         */
        function _pdf_shortcode( $atts, $content = null ) {

            global $post;
            $show_pdf = get_post_meta( $post->ID, 'show_pdf', true );
            
            if($show_pdf=='on') {
    
                $title = isset($atts['title'])?$atts['title']:'PDF Report';
                $download = isset($atts['download'])?$atts['download']:'1';
                $strip_img = isset($atts['strip_img'])?$atts['strip_img']:'1';
                $template = isset($atts['template'])?$atts['template']:'0';
                
                //if download set, open in new window
                if($download == '0') {
                    $target = 'target="_blank"';
                } else {
                    $target = '';
                }

                //if no template tag, then ignore
                 if($template == '0') {
                     $template_url = '';
                 }   else {
                     $template_url = '&amp;template='.$template;
                 }         
                    
                $link = '<a href="/create_pdf/?id='.$post->ID.'&amp;download='.$download.'&amp;title='.$title.'&amp;img='.$strip_img.$template_url.'" '.$target.'>
                            <img src="'.WP_CONTENT_URL.Pdf_Generator::PLUGIN_DIR.Pdf_Generator::APP_DIR .'/images/download-pdf.png" alt="Download PDF Report"/>
                        </a>';
                    
                return $link;
            } 
        }
        /**
         * Create PDF file using htmldoc
         * @param $filename
         * @param $data
         */
        function _pdf_make($filename, $data)
        {

            $id = isset($data['id'])?$data['id']:null;
            $title = isset($data['title'])?$data['title']:null;
            $download = isset($data['download'])?$data['download']:1;
            $img = isset($data['img'])?$data['img']:1;
            $template = isset($data['template'])?$data['template']:null;
            
            //set options here
            $options = get_option(parent::OPTIONS_PREFIX);
 
            //body font
            $o = '--bodyfont ' . $options['pdf']['bodyfont'];

            //font size
            $o .= '--fontsize ' . $options['pdf']['fontsize'];

            //browser width
            $o .= '--browserwidth ' . $options['pdf']['browserwidth'];

            //margins
            $o .= '--bottom ' . $options['pdf']['bottom_margin'];
            $o .= '--top ' . $options['pdf']['top_margin'];
            $o .= '--right ' . $options['pdf']['right_margin'];
            $o .= '--left ' . $options['pdf']['left_margin'];

            # Tell HTMLDOC not to run in CGI mode...
            putenv("HTMLDOC_NOCGI=1");

            # Write the content type to the client...MAKE SURE TEHRE ARE NO SPACES HERE, DO NOT DO CONTROL+alt+l
            header("Content-Type:application/pdf");
            if($download==1) {
                header('Content-Disposition: attachment;filename="'.$title.'"');
            }
            flush();
            $filename = $filename . '?id=' . $id.'&img='.$img.'&template='.$template;
            $line = "htmldoc -t pdf --jpeg=100 --no-strict --no-compression --color --webpage --no-numbered --headfootsize .1in --header ... --footer ... $o '$filename'";
            return passthru($line);
        }
        /**
         * Utility function to Clean an array depending on type
         * @param array $arr
         * @param $type
         * @return array
         */
        public function _clean_array(array $arr, $type)
        {
            if ($type == 'mysql') {
                return array_map('mysql_real_escape_string', $arr);
            } else {
                return array_map('self::_clean_html', $arr);
            }
        }

        /**
         * Utility function to clean input
         * return @input
         */
        public
        function _clean_mysql($data)
        {
            $data = trim($data);
            $data = filter_var($data, $data);
            return mysql_real_escape_string($data);
        }


        /**
         * Utility function to clean input
         * return @input
         */
        public
        function _clean_html($data)
        {
            $data = trim($data);
            $data = filter_var($data, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            return $data;
        }
    }
}