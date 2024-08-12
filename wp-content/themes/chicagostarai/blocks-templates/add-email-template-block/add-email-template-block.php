<?php
$url_label = get_field('url_label');
$url_placeholder = get_field('url_placeholder');
$button_text = get_field('button_text');
$max_number_url = get_field('max_number_url');
echo '<script>';
echo 'var maxNumberUrl = ' . json_encode($max_number_url) . ';';
// echo 'console.log("maxNumberUrl",maxNumberUrl)';
echo '</script>';
?>

<div class="add-email source-input">
    <div class="input-form main-form">
        <div class="remove-source text-end">
            <button class="btn btn-dashboard remove-source-form"><i class="fa-solid fa-trash-can"></i></button>
        </div>
        <div class="mb-3">
            <label for="newsFeedLink" class="form-label">
                <?php echo $url_label; ?>
            </label>
            <input type="email" class="form-control" name="newsFeedLink[]" aria-describedby="emailHelp">
            <div id="emailHelp" class="form-text">
                <?php echo $url_placeholder; ?>
            </div>
        </div>
    </div>
</div>
<div class="btn-module mb-5">
    <a class="btn btn-dashboard add-feed-button" type="button">
        <?php echo $button_text; ?>
    </a>
</div>
<script
    src="<?php echo get_template_directory_uri() . "/blocks-templates/add-email-template-block/add-email-template-block.js"; ?>"></script>