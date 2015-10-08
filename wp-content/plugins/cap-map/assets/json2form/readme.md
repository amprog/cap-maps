Introduction
================================

The json2Form class was originally coded to progmatically install the ZEND Framework.  The ability to also use JSON to program the form tag and submit button was added shortly after. 
The use of these features is optional, but you will have to manually add these tags yourself if you want your form to do anything.  

The class uses static functions for simplicity and interoperability.  Please view the [demo](http://portfolio.amir-meshkin.com/scripts/json2form/demo.php "json2Form Demo Page") page to see exactly how it works.


Usage
------------------------

To display a form, you need to simply call 2 static functions. The first line simply reads the JSON, and the second line prints out an html form. 

    $json = jsonReader::read("test.json");
    jsonReader::jsonToFormSimple($json);

The script that will process the form will write the data to the JSON file with one line. The writeSimple($data,$file = NULL) method accepts an array for data, usually _POST.

    jsonReader::writeSimple($_POST,"config-simple.json");

JSON File Format
------------------------

Please use the following conventions when writing your JSON config files.

* The top part of the JSON file can contain an optional form key, with attributes, submit data, and options.
* The fieldsets and top part are read only, and can only be changed by modifying the JSON files directly. 
* Use underscores with the name of the fieldsets, not spaces.
* The fieldset labels will be used for the input names and ID's, so stay away from funky characters!
* Always use UTF-8 characters, although non UTF-8 characters will be converted to UTF-8.
* The input field names are under the fieldset keys. Simple key value type config file with the name of the field on the left, and the value of the form on the right. 


Security
------------------------

The included .htaccess file will keep users from viewing your JSON files from their browsers.

CSRF ATTACKS
Setting the following keys in the JSON file will protect your site against CSRF attacks

* csrf should be set to 1 to turn this feature on.
* csrf_timeout should be set the minutes you want the nonce to expire. So if you have a 15 there, the user has 15 minutes to submit the form before it becomes invalid and will no longer work.
* csrf_key should also be changed, and kept secret for maximum protection.


Features to Add
------------------------

* Add a more complicated method for parsing robust JSON files into forms
* Allow inline AJAX editing of forms by automatically adding jQuery code
* Allow user to choose a Captcha plugin


Requirements
------------------------

You must have at least php 5.2 or newer for this script to function.

This class works best with php 5.4.  If you do not have php 5.4, the class will automatically fall back to an older version of json_encode() 

The script will still work, but your JSON wont look as pretty when saved into the file. And more importantly, SLASHES WILL NOT BE ESCAPED!

Older versions of IE do not always play so well with JSON.  We suggest you upgrade your browser, or use [html5 Boilerplate](http://html5boilerplate.com "html5 Boilerplate")



Notes
------------------------



This class was created for simple config files, and wasn't coded with use in complex CMS systems in mind.  However, I will probably add another method of building complex forms using JSON with full attributes, and customization.

I also do not like large config files, as you see in systems like Magento.

However, it can easily be customized so that multiple files can be displayed and saved, without having to change the class.

This will keep the files small, and allow for easier customization.

If anyone is interested in getting porting this to other CMS Systems and Frameworks, then contact me

amir.meshkin AT gmail.com
