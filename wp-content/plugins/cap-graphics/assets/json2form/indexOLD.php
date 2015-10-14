<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'json2form.class.php';
require_once '_header.php';
?>
<div class="main-container">
    <div class="main wrapper clearfix">

        <article>
            <header>
                <h1>Display Form</h1>
                <p>To display a form, you need to simply call 2 static functions. The first line simply reads the JSON, and the second line prints out an html form. </p>
                <pre>
                        <code>
$json = jsonReader::read("config-simple.json");
print jsonReader::jsonToFormSimple($json, 'install');</code>
                </pre>

                <p>The script that will process the form will write the data to the JSON file with one line. The <code>writeSimple($data,$file = NULL)</code> method accepts an array for data, usually _POST. The <code>$file</code> variable is an optional string that will allow you to save the contents to the file you specify.</p>
                <pre>
                        <code>
jsonReader::writeSimple($_POST,"config-simple.json");
                        </code>
                </pre>


            </header>
            <section>
                <h2>Features to ADD</h2>
                <ul>
                    <li>AJAX Enabled</li>
                    <li>Choice of Captcha Device</li>
                    <li>Protection from CSRF Attacks</li>
                </ul>
            </section>
            <section>
                <h2>article section h2</h2>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam sodales urna non odio egestas tempor. Nunc vel vehicula ante. Etiam bibendum iaculis libero, eget molestie nisl pharetra in. In semper consequat est, eu porta velit mollis nec. Curabitur posuere enim eget turpis feugiat tempor. Etiam ullamcorper lorem dapibus velit suscipit ultrices. Proin in est sed erat facilisis pharetra.</p>
            </section>
            <footer>
                <h3>article footer h3</h3>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam sodales urna non odio egestas tempor. Nunc vel vehicula ante. Etiam bibendum iaculis libero, eget molestie nisl pharetra in. In semper consequat est, eu porta velit mollis nec. Curabitur posuere enim eget turpis feugiat tempor.</p>
            </footer>
        </article>

        <?php require_once '_side.php'; ?>

    </div> <!-- #main -->
</div> <!-- #main-container -->
<?php require_once("_footer.php"); ?>