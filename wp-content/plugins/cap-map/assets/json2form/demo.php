<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once '../_header.php';
if ($_POST) {
    error_log("writing POST");
    jsonReader::writeSimple($_POST, "test.json");
}


//$json = jsonReader::read("config-simple.json");
//print jsonReader::jsonToFormSimple($json, 'install');
?>
<div class="main-container">
    <div class="main wrapper clearfix">

        <article>
            <header>
                <?php if ($_POST): ?>
                    <div class="success">JSON File saved! <a href="#data">Scroll down</a> to view your changes.</div>
                <?php endif; ?>
                <h1>Example of Usage</h1>
                <p>You will see the output of the jsonToFormSimple() method below.  Make a few changes, save the form and this page will refresh.   You will see your changes at the bottom of the page.</p>
            </header>

            <section>
                <h2>Demo Config Form</h2>
                <?php
                $demo_json = jsonReader::read("test.json");
                jsonReader::jsonToFormSimple($demo_json);
                ?>
            </section>

            <section>
                <h1 id="data">File Data</h1>
                <p>The raw contents of the test.json file are below.  You can see your new values below after submitting the form.</p>
                <pre>
                <code>
                        <?php print file_get_contents('test.json');?>
                </code>
                </pre>
            </section>
            <footer>
                <h3>Conclusion</h3>
                <p>And that's about it! I kept this as simple as possible for now, but there are features that I will be adding at some point.  </p>
                <p>This script is great for CMS systems and was created to install the ZEND Framework from an installation form.</p>
                <p>It's also used to set up this mini site I use for my scripts. All I have to do, is add a new class/script,readme.md file, and configure a JSON file.  The data for the pages are automatically loaded where they should be.</p>
                <p>Those familiar with GUI have a way to modify JSON config files from a form and programmers can edit the file directly. Best of both worlds! </p>
            </footer>
        </article>

        <?php require_once '../_side.php'; ?>

    </div> <!-- #main -->
</div> <!-- #main-container -->
<?php require_once("../_footer.php"); ?>