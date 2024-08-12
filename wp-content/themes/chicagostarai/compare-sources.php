<?php

/**
 * Template Name: Compare Sources
 */

// Check if the 'type' parameter is set in the URL
if (isset($_GET['type']) && function_exists('get_field')) {
    $post_type = sanitize_text_field($_GET['type']);
    $post_id = sanitize_text_field($_GET['id']);

    // One GPT Post has One Custom Post. Retrieve Custom Post ID
    $custom_post_id = get_field('custom_post_id', $post_id);

    if ($custom_post_id) {
        $source_repeater = get_field('source_repeater', $custom_post_id);
    }

    if (isset($source_repeater)) {
        // Loop through each row in the repeater
        foreach ($source_repeater as $row) {
            // Retrieve data from each sub-field
            $custom_source_url = $row['custom_source_url'];
            $custom_source_title = $row['custom_source_title'];
            $custom_source_content = $row['custom_source_content'];
        }
    }
} else {
    // Handle the case when 'type' parameter is not set
    echo 'Post type not specified in the URL.';
}
get_header();
?>

<section class="blog-inner-page">
    <div class="container-fluid gx-4">
        <div class="row dashboard-content g-5 mb-5">
            <div class="col-12">
                <div class="content-body mt-5">

                    <div class="generated-artical recent">
                        <h3 class="block-heading">Custom Sources</h3>
                        <?php
                        if (isset($source_repeater)) {
                            foreach ($source_repeater as $repeater_item) {
                                $source_title = $repeater_item['custom_source_title'];
                                $source_content = $repeater_item['custom_source_content'];
                                $custom_source_url = $repeater_item['custom_source_url'];
                        ?>

                                <div class="generated-artical-desc">
                                    <div class="source-block mb-4">
                                        <h4><?php echo $source_title; ?></h4>
                                        <p><?php echo ($source_content); ?></p>
                                    </div>
                                    <?php if (!empty($custom_source_url)) : ?>
                                        <b>This article is copied from URL:</b>
                                        <br>
                                        <a href="<?php echo $custom_source_url; ?>" target="_blank">
                                            <?php echo $custom_source_url; ?>
                                        </a>
                                    <?php endif; ?>
                                </div>

                        <?php
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>