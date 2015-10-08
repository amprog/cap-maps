<?php

/*
 * Name: json2Form Class
 * Created By: Amir Meshkin (http://www.amir-meshkin.com)
 * Created On: February 2013
 * Last Modified On: February 10, 2013
 * Last Modified By: Amir Meshkin (amir.meshkin@gmail.com)
 * Version: 1.0
 */

/*
  Copyright 2013 Amir Meshkin

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

class jsonReader {
    /*
     * Configuration for this class.  This info is in the json file that runs this site
     * But I wanted to put it in here anyway, so that this class doesn't depend on any
     * extra JSON config files
     * CSRF_ATTACK_ERR_MSG = Message to show when a CSRF NONCE value is invalid, or has timed out
     * WRITE_FAILED = Error to show if file is not readable, check permissions, user/group
     */

    const CSRF_ATTACK_ERR_MSG = "Possible CSRF Attack!";

    const WRITE_FAILED = "Unable to create file";

    /**
     * Read JSON file
     * @return type 
     */
    static function read($file) {
        $datastring = file_get_contents($file);
        //convert to utf-8
        $datastring = mb_convert_encoding($datastring, 'UTF-8', 'ASCII,UTF-8,ISO-8859-1');
        if (substr($datastring, 0, 3) == pack("CCC", 0xEF, 0xBB, 0xBF)) {
            $datastring = substr($datastring, 3);
        }
        return json_decode($datastring, true);
    }

    /**
     * Build form tag
     * @param type $data
     * @return type 
     */
    static function formTag($data) {

        $formAtr = '';
        foreach ($data['attributes'] as $key => $attributes) {
            $formAtr .= ' ' . $key . '="' . $attributes . '"';
        }

        $submitAtr = '';
        foreach ($data['submit'] as $key => $attributes) {
            $submitAtr .= ' ' . $key . '="' . $attributes . '"';
        }

        $form = '<form' . $formAtr . '>';
        $submit = '<input' . $submitAtr . '>';
        return array('form' => $form, 'submit' => $submit, 'options' => $data['options']);
    }

    /**
     * create form from simple json
     * @param stdClass $json
     * @return type 
     */
    static function jsonToFormSimple($json) {
        foreach ($json as $key => $s) {
            //output form field first, if user wants to set form tag manually
            //then there should not be any values for form in JSON
            if ($key == 'form') {
                $formData = self::formTag($s);
                print $formData['form'];
            } else {
                print self::startFieldset($key, array("id" => "legend_" . $key));
                //for ($i = 1; $i <= count($s); $i++) {
                foreach ($json[$key] as $k2 => $value) {
                    print '<li><label>' . self::formatLabel($k2) . '</label><input type="text" name="' . $key . '[' . $k2 . ']" id="' . $key . '_' . $k2 . '" value="' . $value . '" /></li>';
                }
                print self::endFieldset();
            }
        }

        $csrf_flag = isset($formData['options']['csrf']) ? $formData['options']['csrf'] : null;

        error_log("csrf_flag help: " . $csrf_flag);
        //csrf if enabled
        if ($csrf_flag == 1) {
            print self::csrfInput($formData['options']);
        }

        //print submit button if enabled
        if (isset($formData['submit']) || $formData['submit'] !== '') {
            print $formData['submit'];
        }
    }

    /**
     * Write a Simple CONFIG Type JSON file
     * @param type $data
     * @param type $file 
     */
    static function writeSimple($data, $file) {
        $file_content = self::read($file);
        //check for csrf
        $csrf_flag = isset($file_content['form']['options']['csrf']) ? $file_content['form']['options']['csrf'] : null;
        error_log("writeSimple csrf_flag: " . $csrf_flag);

        if ($csrf_flag == '1') {
            error_log("csrf_flag is 1: " . $csrf_flag);
            $nonce = isset($_SESSION['json2form']['nonce']) ? $_SESSION['json2form']['nonce'] : null;
            $nonce_set = isset($_SESSION['json2form']['nonce_set']) ? $_SESSION['json2form']['nonce_set'] : null;

            $now = time();

            error_log("nonce: " . $nonce);
            error_log("nonce_set: " . $nonce_set);
            $timeout = self::calculateMin($file_content['form']['options']['csrf_timeout']);
            $diff = $now - $timeout;
            error_log("if time(" . $now . ") MINUS timeout($timeout) EQUAL TO " . $diff . " IS LESS THAN " . $nonce_set);


            if ($data['nonce'] == $nonce && $now - $timeout < $nonce_set) {  //nonce matches session, and still fresh!
                error_log("nonce is GOOD!");
            } else {
                error_log("nonce is BAD!");
                die(self::CSRF_ATTACK_ERR_MSG);
            }
        }
        if (!isset($file_content['form']) || $file_content['form'] === '') {
            $save = $data;
        } else {
            error_log("form exists: " . $file_content['form']['attributes']['action']);
            $keep['form'] = $file_content['form'];
            $save = array_merge($keep, $data);
        }

        if ($csrf_flag == '1') {
            error_log("unsetting nonce");
            unset($save['nonce']);
        }

        //JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES require php 5.4.0
        $v = explode('.', PHP_VERSION);


        if ($v[0] == 5 && $v[1] < 2) {
            die("You need to have at least PHP 5.2 installed to use this. You currently have " . PHP_VERSION);
        } elseif ($v[0] == 5 && $v[1] < 4) {
            $newdata = json_encode($save);
        } else {
            $newdata = json_encode($save, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        }


        $fh = fopen($file, "w");
        if ($fh == false) {
            error_log("write failed");
            die(self::WRITE_FAILED);
        }
        fputs($fh, $newdata);
        fclose($fh);
    }

    /**
     * create form from complicated json
     * @param stdClass $json
     * @return type 
     */
    static function jsonToFormComp($json) {
        
    }

    /**
     * Print fieldset
     * @param type $title
     * @param array $attributes
     * @return string 
     */
    static function startFieldset($title, array $attributes = NULL) {
        if ($attributes) {
            $atr = '';
            foreach ($attributes as $key => $a) {
                $atr .= ' ' . $key . '="' . $a . '"';
            }
        }
        $data = '<fieldset>';
        $data .= "<legend $atr>" . self::formatLabel($title) . "</legend><ol>";
        return $data;
    }

    /**
     * Simple format function for labels
     * @param type $str
     * @return type 
     */
    static function formatLabel($str) {
        return ucfirst(str_replace("_", " ", $str));
    }

    /**
     *
     * @return type 
     */
    static function endFieldset() {
        return "</ol></fieldset>";
    }

    /**
     *
     * @return type 
     */
    static function csrfInput($csrf) {
        $timeout = self::calculateMin($csrf['csrf_timeout']);
        $nonce = self::generateNonce($csrf['csrf_key']);
        error_log("nonce aftr gneration: " . $nonce);
        /*
          setcookie("json2form_nonce", $nonce, time() + $timeout, "", "amir-meshkin.com", 1);
          $cookie =  $_COOKIE['json2form_nonce']!='' ? $_COOKIE['json2form_nonce'] : '0';
         * 
         */
        $now = time();
        $_SESSION['json2form']['nonce'] = $nonce;
        $_SESSION['json2form']['nonce_set'] = $now;

        error_log("nonce has been set: " . $_SESSION['json2form']['nonce_set']);
        error_log("timeout: " . $timeout);
        error_log("session: " . $_SESSION['json2form']['nonce']);
        return '<input type="hidden" id="nonce" name="nonce" value="' . $nonce . '" />';
    }

    /**
     * Take minutes, and return microseconds for csrf
     * @param type $minutes
     * @return type 
     */
    static function calculateMin($minutes) {
        $microseconds = $minutes * 60 * 1000;
        return $microseconds;
    }

    static function generateNonce($salt) {
        $i = rand(1, 17);
        error_log("i: " . $i);
        return md5($i . $salt);
    }

}

?>
