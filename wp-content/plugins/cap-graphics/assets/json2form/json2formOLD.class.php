<?php

/* read

 * 
 * *
 */

class jsonReader {

    /**
     * Read JSON file
     * @return type 
     */
    static function read($file) {
        $datastring = file_get_contents($file);

        /*
          print '<pre>';
          print_r(json_decode($datastring));
          exit;
          //return json_decode($datastring);
         * 
         */
        return json_decode($datastring, true);
    }

    static function formTag($data) {

        foreach ($data['attributes'] as $key => $attributes) {
            $formAtr .= ' ' . $key . '="' . $attributes . '"';
        }

        foreach ($data['submit'] as $key => $attributes) {
            $submitAtr .= ' ' . $key . '="' . $attributes . '"';
        }

        $form = '<form' . $formAtr . '>';
        $submit = '<input' . $submitAtr . '>';
        return array('form' => $form, 'submit' => $submit);
    }

    /**
     * create form from simple json
     * @param stdClass $json
     * @return type 
     */
    static function jsonToFormSimple($json, $section) {
        foreach ($json[$section] as $key => $s) {
            //output form field first, if user wants to set form tag manually
            //then there should not be any values for form in JSON
            if ($key == 'form') {
                $formData = self::formTag($s);
                error_log("form: " . $formData['form']);
                print $formData['form'];
            } else {
                print self::startFieldset($key, array("id" => "legend_" . $key));
                //for ($i = 1; $i <= count($s); $i++) {
                foreach ($json[$section][$key] as $k2 => $value) {
                    //print '<li><label>' . self::formatLabel($k2) . '</label><input type="text" name="' . $key . ':' . $k2 . '" id="' . $k2 . '" value="' . $value . '" /></li>';
                    print '<li><label>' . self::formatLabel($k2) . '</label><input type="text" name="' . $key . '[' . $k2 . ']" id="' . $k2 . '" value="' . $value . '" /></li>';
                }
                print self::endFieldset();
            }
        }

        if ($formData['submit']) {
            error_log("submit: " . $formData['submit']);
            print $formData['submit'];
        }
    }

    /**
     * create form from complicated json
     * @param stdClass $json
     * @return type 
     */
    static function jsonToFormComp($json, $section) {
        
    }

    static function startFieldset($title, array $attributes = NULL) {


        if ($attributes) {
            foreach ($attributes as $key => $a) {
                $atr .= ' ' . $key . '="' . $a . '"';
            }
        }


        $data = '<fieldset>';
        $data .= "<legend $atr>" . self::formatLabel($title) . "</legend><ol>";
        return $data;
    }

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
     * Write a Simple CONFIG Type JSON file
     */
    static function writeSimple($data, $file) {
        //take flat _POST and turn into 3d array, and then turn into JSON
        print '<pre>';
        print_r($data);
        
        $num = count($data);
        error_log($num);
        $i = 0;
        foreach ($data as $key => $d) {
            $field = explode(":", $key);
            $fieldset = $field[0];
            $name = $field[1];

            $fieldsets[] = $fieldset;
            $keys[] = $name;
            $values[$name] = $d;



            //too difficult with array to json
            //$arr[$fieldset][] = array($name => $d);  //creates extra level
            //$arr[] = array($fieldset=>array($name => $d));


            $i++;
        }


        $fieldsets = array_unique($fieldsets);



        /*
          foreach ($fieldsets as $f) {
          $data .= '"' . $fieldset . '": {';

          foreach ($values as $key => $d) {
          error_log("key: " . $key);
          error_log("fieldset: " . $fieldset);
          error_log("d: " . $d);



          $data .= '"' . $name . '": "' . $d . '"';
          $data .=($i < $num) ? ',' : ',';
          }
          }

         */
        foreach ($values as $v) {
            //foreach ($values as $v) {
            foreach ($fieldsets as $f) {
                $final[$f] = $values;
            }
        }


        print_r($fieldsets);
        print_r($keys);
        print_r($values);
        print_r($final);


        $fh = fopen($file, "w");
        if ($fh == false)
            die("unable to create file");



        fputs($fh, json_encode($final, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        fclose($fh);
    }

    /**
     * Set a single element attribute
     *
     * @param  string $key
     * @param  mixed  $value
     * @return Element|ElementInterface
     */
    public function setAttribute($key, $value) {
        // Do not include the value in the list of attributes
        if ($key === 'value') {
            $this->setValue($value);
            return $this;
        }
        $this->attributes[$key] = $value;
        return $this;
    }

    /**
     * Retrieve a single element attribute
     *
     * @param  $key
     * @return mixed|null
     */
    public function getAttribute($key) {
        if (!array_key_exists($key, $this->attributes)) {
            return null;
        }
        return $this->attributes[$key];
    }

    /**
     * Set the element value
     *
     * @param  mixed $value
     * @return Element
     */
    public function setValue($value) {
        $this->value = $value;
        return $this;
    }

    /**
     * Retrieve the element value
     *
     * @return mixed
     */
    public function getValue() {
        return $this->value;
    }

}

/* object
  //static function jsonToForm(stdClass $json, $section) {


  foreach ($json->$section as $key => $s) {
  $data = self::startFieldset($section, array("id" => "legend_" . $section));
  error_log("key: " . $key);
  //error_log("s: " . $s[0]);

  var_dump($s);
  foreach ($json->$section->$key as $k2 => $value) {
  error_log("k2: " . $k2);
  error_log("value: " . $value);

  $data .= '<input type="text" id="' . $key . ' value="' . $s . '" />';
  }
  //$form->addElement('text', 'foo', array('label' => 'Foo:'));
  $data .= self::endFieldset();
  }

 */
?>
