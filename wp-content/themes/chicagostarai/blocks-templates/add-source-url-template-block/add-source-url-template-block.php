<?php
$source_heading = get_field('source_heading');
$source_title = get_field('source_title');
$source_text = get_field('source_text');
$source_url = get_field('source_url');
$button_text = get_field('button_text');
$source_maximum_url = get_field('source_maximum_url');
echo '<script>';
echo 'var maxSourceNumberUrl = ' . json_encode($source_maximum_url) . ';';
echo '</script>';
?>
<div class="add-url-source add-source-input">
    <div class="input-form add-source-main-form">
        <div class="my-2">
            <div class="block-header d-flex align-items-center justify-content-between">
                <h4 class="block-head mb-3">
                    <?php echo $source_heading; ?>
                </h4>
                <div class="remove-source">
                    <button class="btn  btn-dashboard remove-source-form mt-3"><i
                            class="fa-solid fa-trash-can"></i></button>
                </div>
            </div>
            <label for="sourceTitle" class="form-label">
                <?php echo $source_title; ?>
            </label>
            <input type="email" class="form-control" name="sourceTitle[]" aria-describedby="emailHelp"
                placeholder="Enter the source title">
        </div>
        <div class="my-2">
            <label for="sourceText" class="form-label">
                <?php echo $source_text; ?>
            </label>
            <textarea placeholder="Enter source body text" class="form-control mb-3" id="textarea"
                name="sourceText[]"></textarea>
        </div>
        <div class="my-2">
            <label for="sourceUrl" class="form-label">
                <?php echo $source_url; ?>
            </label>
            <input type="email" class="form-control" name="sourceUrl[]" id="exampleInputEmail1"
                aria-describedby="emailHelp" placeholder="Enter Source Link">
        </div>
    </div>
</div>
<div class="btn-module mb-5">
    <a href="" class="btn btn-dashboard add-source-button" type="button">
        <?php echo $button_text; ?>
    </a>
</div>
<script
    src="<?php echo get_template_directory_uri() . "/blocks-templates/add-source-url-template-block/add-source-url-template-block.js" ?>"></script>