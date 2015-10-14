<?php require_once '../_header.php'; ?>
<div class="main-container">
    <div class="main wrapper clearfix">

        <article>
            <?php echo passthru('perl ../../Markdown.pl --html4tags readme.md'); ?>
        </article>

        <?php require_once '../_side.php'; ?>

    </div> <!-- #main -->
</div> <!-- #main-container -->
<?php require_once("../_footer.php"); ?>