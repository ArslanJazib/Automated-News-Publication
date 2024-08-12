<?php

$url_label = get_field('url_label');
$url_placeholder = get_field('url_placeholder');
$button_text = get_field('button_text');
$term_slug = '';
$field_newsFeedLinks = '';
$fetchUrlLinks = '';
$fetchUrlTitles = '';
$fetchUrlContents = '';
$field_customtxt_url = '';
$field_customtxt_title = '';
$field_customtext_txtarea = '';
$field_subject = '';
$field_questions = '';
$scrapped_post_data = [];
$scrapped_post_ids = [];
$allFieldsEmpty = true;
$gpt_post_id = '';
$custom_post_id = '';
$scrapped_post_ids_json = '';
$base_url = get_home_url();
$scrapped_titles = [];
$scrapped_contents = [];
$scrapped_urls = [];
$length = '';
$scrapped_revision_divs = '';
$revision_divs = '';
$custom_revision_divs = '';

$get_parameter = isset($_GET['post_id']) ? $_GET['post_id'] : '';

if (isset($_GET['post_id'])) {

    $gpt_post_id = $_GET['post_id'];
    $used_input_values = get_field('fetch_url_gpt_response' , $gpt_post_id);
    $content_post = get_post($gpt_post_id);
    $content = $content_post->post_content;
    
    $categories = get_the_terms($gpt_post_id, 'gpt_categories');
    if ($categories) {
        foreach ($categories as $category) {
            $term_slug = $category->slug;
        }
    }
    $scrapped_post_ids = [];
    $post_content = get_post_field('post_content', $gpt_post_id);
    $custom_post_id = get_field('custom_post_id', $gpt_post_id);
    $scrapped_data = get_scrapped_data($custom_post_id);
    if (!empty($scrapped_data['content'])) {
        foreach ($scrapped_data['content'] as $post_data) {
            $scrapped_titles[] = $post_data['post_title'];
            $scrapped_contents[] = $post_data['post_content'];
            $scrapped_urls[] = $post_data['source_url'];
            $scrapped_post_ids[] = $post_data['ID']; 
        }
    }
    if (is_array($scrapped_titles)) {
        $length = count($scrapped_titles);
    }
    for ($i = 0; $i < $length; $i++) {
        $post = [
            "title" => $scrapped_titles[$i],
            "content" => $scrapped_contents[$i],
            "url" => $scrapped_urls[$i]
        ];
        array_push($scrapped_post_data, $post);
    }
    if ($custom_post_id) {
        $source_repeater = get_field('source_repeater', $custom_post_id);
        if ($source_repeater) {
            foreach ($source_repeater as $item) {
                $field_customtxt_url = is_array($item['custom_source_url']) ? implode(",", $item['custom_source_url']) : $item['custom_source_url'];
                $field_customtxt_title = is_array($item['custom_source_title']) ? implode(",", $item['custom_source_title']) : $item['custom_source_title'];
                $field_customtext_txtarea = is_array($item['custom_source_content']) ? implode(",", $item['custom_source_content']) : $item['custom_source_content'];
            }
        }
    }
    $field_subject = get_field('field_subject', $gpt_post_id);
    $field_questions = get_field('field_questions', $gpt_post_id);

    $scrapped_post_ids_json = json_encode($scrapped_post_ids);
    
    $revisions = wp_get_post_revisions($gpt_post_id);

    $revision_divs = '';

    if ($revisions) {

        $previous_revision_content = '';
        $counter = 0;

        foreach ($revisions as $revision) {
            if ($counter >= 5) {
                break;
            }

            $revision_content = $revision->post_content;

            if (!empty($previous_revision_content)) {
                $before_revision_content = strip_tags($previous_revision_content);
                $after_revision_content = strip_tags($revision_content);

                $revision_divs .= '<div class="input-form mt-1 d-flex">';
                $revision_divs .= '<div class="mb-1 me-2">';
                $revision_divs .= '<h5 class="module-heading mb-2">Before Revision</h5>';
                $revision_divs .= '<textarea class="form-control revisionsfields" name="before_revision_txtarea_' . $counter . '" id="before_revision_txtarea_' . $counter . '" cols="50" rows="10">' . htmlspecialchars($before_revision_content) . '</textarea>';
                $revision_divs .= '</div>';
                $revision_divs .= '<div class="mb-1 ms-2">';
                $revision_divs .= '<h5 class="module-heading mb-2">After Revision</h5>';
                $revision_divs .= '<textarea class="form-control revisionsfields" name="after_revision_txtarea_' . $counter . '" id="after_revision_txtarea_' . $counter . '" cols="50" rows="10">' . htmlspecialchars($after_revision_content) . '</textarea>';
                $revision_divs .= '</div>';
                $revision_divs .= '</div>';
            }

            $previous_revision_content = $revision_content;
            $counter++;
        }
    } else {
        $revision_divs = '<p>No revisions found for this post.</p>';
    }

    foreach ($scrapped_post_ids as $post_id) {

        $revisions = wp_get_post_revisions($post_id);

        if ($revisions) {

            $previous_revision_content = '';
            $counter = 0;

            foreach ($revisions as $revision) {
                if ($counter >= 5) {
                    break;
                }

                $revision_content = $revision->post_content;
                $revision_content = str_replace('&nbsp;', '', $revision_content);

                if (!empty($previous_revision_content)) {
                    $before_revision_content = strip_tags($previous_revision_content);
                    $after_revision_content = strip_tags($revision_content);

                    $scrapped_revision_divs .= '<div class="input-form mt-1 d-flex">';
                    $scrapped_revision_divs .= '<div class="mb-1 me-2">';
                    $scrapped_revision_divs .= '<h5 class="module-heading mb-2">Before Revision (Post ID: ' . $post_id . ')</h5>';
                    $scrapped_revision_divs .= '<textarea class="form-control revisionsfields" name="before_revision_txtarea_' . $post_id . '_' . $counter . '" id="before_revision_txtarea_' . $post_id . '_' . $counter . '" cols="50" rows="10">' . htmlspecialchars($before_revision_content) . '</textarea>';
                    $scrapped_revision_divs .= '</div>';
                    $scrapped_revision_divs .= '<div class="mb-1 ms-2">';
                    $scrapped_revision_divs .= '<h5 class="module-heading mb-2">After Revision (Post ID: ' . $post_id . ')</h5>';
                    $scrapped_revision_divs .= '<textarea class="form-control revisionsfields" name="after_revision_txtarea_' . $post_id . '_' . $counter . '" id="after_revision_txtarea_' . $post_id . '_' . $counter . '" cols="50" rows="10">' . htmlspecialchars($after_revision_content) . '</textarea>';
                    $scrapped_revision_divs .= '</div>';
                    $scrapped_revision_divs .= '</div>';
                }

                $previous_revision_content = $revision_content;
                $counter++;
            }
        } else {
            $scrapped_revision_divs .= '<p>No revisions found for post ID: ' . $post_id . '.</p>';
        }
    }
    
} else if (isset($_SESSION['field_fetchUrlLinks']) || isset($_SESSION['field_fetchUrlTitles']) || isset($_SESSION['field_fetchUrlContents']) || isset($_SESSION['field_customtxt_url']) || isset($_SESSION['field_customtxt_title']) || isset($_SESSION['field_customtext_txtarea']) || isset($_SESSION['field_subject']) || isset($_SESSION['field_questions']) || isset($_SESSION['field_newsFeedLinks']) || isset($_SESSION['field_customPostIdFetch']) || isset($_SESSION['field_scrapedPostIdsFetch'])){

    $field_newsFeedLinks = $_SESSION['field_newsFeedLinks'];
    $fetchUrlLinks = $_SESSION['field_fetchUrlLinks'];
    $fetchUrlTitles = $_SESSION['field_fetchUrlTitles'];
    $fetchUrlContents = $_SESSION['field_fetchUrlContents'] ?? '';
    $field_customtxt_url = $_SESSION['field_customtxt_url'];
    $field_customtxt_title = $_SESSION['field_customtxt_title'];
    $field_customtext_txtarea = $_SESSION['field_customtext_txtarea'];
    $field_subject = $_SESSION['field_subject'];
    $field_questions = $_SESSION['field_questions'];
    $custom_post_id = $_SESSION['field_customPostIdFetch'] ?? null;
    $scrapped_post_ids_json_new = $_SESSION['field_scrapedPostIdsFetch'] ?? null;

    if (is_array($scrapped_post_ids_json_new)) {
        $scrapped_post_ids_json = json_encode($scrapped_post_ids_json_new);
    } else {
        $scrapped_post_ids_json = null; 
    }

    if(is_array($fetchUrlLinks) || is_array($fetchUrlTitles) || is_array($fetchUrlContents) || is_array($field_customtxt_url) || is_array($field_customtxt_title) || is_array($field_customtext_txtarea) || is_array($field_subject) || is_array($field_questions) || is_array($field_newsFeedLinks)){

        $length = count($field_newsFeedLinks);
        for ( $i =0 ; $i < $length ; $i++) {
            if (isset($fetchUrlLinks[$i], $fetchUrlTitles[$i], $fetchUrlContents[$i] , $field_newsFeedLinks[$i])) {
            $url = $fetchUrlLinks[$i];
            $title = stripslashes_deep($fetchUrlTitles[$i]);
            $content = stripslashes_deep(trim($fetchUrlContents[$i]));
            if (!empty($url) || !empty($title) || (!empty($content))) {
                $allFieldsEmpty = false;
            }

            $post = [
                'newsFeedLinks' => isset($field_newsFeedLinks[$i]) ? $field_newsFeedLinks[$i] : '',
                'url' => $url,
                'title' => $title,
                'content' => $content
            ];
            array_push($scrapped_post_data, $post);
        }
    }

        $field_customtxt_url = implode(',', $field_customtxt_url);
        $field_customtxt_title_splash = implode(',', $field_customtxt_title);
        $field_customtxt_title = stripslashes($field_customtxt_title_splash);
        $field_customtext_txtarea_splash = implode(',', $field_customtext_txtarea);
        $field_customtext_txtarea = stripslashes($field_customtext_txtarea_splash);

        $field_subject_splash = implode(',', $field_subject);
        $field_subject = stripslashes($field_subject_splash);
        $field_questions_splash = implode(',', $field_questions);
        $field_questions = stripslashes($field_questions_splash);
    
    }
}

$field_fetchUrlLinks = isset($_SESSION['field_fetchUrlLinks']) ? $_SESSION['field_fetchUrlLinks'] : null;
$field_newsFeedLinks = isset($_SESSION['field_newsFeedLinks']) ? $_SESSION['field_newsFeedLinks'] : null;

?>
<script>
    var termSlug = '<?php echo isset($term_slug) ? $term_slug : ''; ?>';
    var scrappedPostData = <?php echo json_encode($scrapped_post_data); ?>;
    var fieldFetchUrlLinks = <?php echo json_encode($field_fetchUrlLinks); ?>;
    var fieldNewsFeedLinks = <?php echo json_encode($field_newsFeedLinks); ?>;
    var baseUrl = <?php echo json_encode($base_url) ?>;
    var getParameter = "<?php echo $get_parameter; ?>";

    if(termSlug == 'save-archive-articles-as-draft'){
        var gptPostId = "<?php echo $gpt_post_id; ?>";
        var customPostId = "<?php echo $custom_post_id; ?>";
    }
     
</script>
<script src="https://cdn.ckeditor.com/ckeditor5/41.2.1/classic/ckeditor.js"></script>

<div class="btn-module mb-3 d-flex justify-content-end">
    <a class="btn btn-sm <?php if ($term_slug === 'save-article-as-sample') { ?> disabled <?php } ?>" id="reset-post" type="button" onclick="reset_article()" style="background-color: #dc3545!important;">RESET ARTICLE</a>
</div>
<form id="fetch-url-form">
    <div class="add-email source-input">
        <div class="input-form main-form mb-1">
            <div class="mb-1 url_input">
                <div class="instruction-block mb-3">
                    <div class="instructions w-100 d-flex align-items-center justify-content-between mb-1">
                        <h3 class="module-heading">Content Extraction Tool</h3>
                        <!-- <button type="button" class="btn btn-dashboard btn-instruction btn-sm">
                            Instructions </button> -->
                    </div>
                    <div class="instructions-list active">
                        <p class="Note-for-instructions mb-0 text-muted fst-italic">Copy and paste hyperlinks to your sources in the fields below. You can enter one URL per field, with a maximum of three URLs. Clicking "EXTRACT TEXT" will retrieve the text from these links for you to review and edit in the section below.
                            Alternatively, copy and past your text directly into the Custom Text windows below.
                        </p>
                    </div>
                </div>
                <div class="block-header d-flex align-items-center justify-content-between">
                    <label for="newsFeedLink" class="form-label">
                        <?php echo $url_label; ?>
                    </label>
                </div>


                <!-- <input type="text" class="form-control " name="newsFeedLink[]" id="newsFeedLink"
                    aria-describedby="emailHelp"> -->
                <!-- <div id="emailHelp" class="form-text">
                    <?php echo $url_placeholder; ?>
                </div> -->
            </div>


            <div class="btn-module mb-1 d-flex justify-content-end">
                <a class="btn btn-dashboard add-feed-button btn-sm disabled" onclick="addInput(this)" type="button">
                    Add URL
                </a>
                <button type="button" class="btn btn-dashboard remove-source-form btn-sm <?php if ($term_slug === 'save-article-as-sample') { ?> disabled <?php } ?>" style="padding: 7px 8px;
    min-height: auto; margin-left:20px;" onclick="removeInput(this)"><i class="fs-6 fa-solid fa-trash-can"></i>
                </button>

            </div>

        </div>

    </div>

    <div class="d-flex justify-content-between">
        <div class="btn-module mb-3">
            <a class="btn btn-dashboard <?php if ($term_slug === 'save-article-as-sample') { ?> disabled <?php } ?>" id="fetch-button" type="button">
                <?php echo $button_text ?>
            </a>
        </div>
    </div>
</form>


<div id="fetchdata" class="">


    <div class="input-form main-form d-flex flex-column <?php if ($term_slug === 'save-article-as-sample' && !empty($post_data['source_url'])) { ?> d-block <?php } else if ($term_slug === 'save-archive-articles-as-draft' && !empty($post_data['source_url'])) { ?> d-block <?php } else if (!$allFieldsEmpty) { 
    ?> d-block <?php } else { ?> d-none <?php } ?>" id="content_div">
        <div class="instruction-block mb-1">
            <h4 class="block-head">Extracted Text </h4>
            <div class="instructions w-100 d-flex align-items-center justify-content-end mb-1">
                <!-- <button type="button" class="btn btn-dashboard btn-instruction btn-sm">
                    Instructions </button> -->
            </div>
            <div class="instructions-list active">
                <p class="Note-for-instructions mb-0 text-muted fst-italic">Copy and paste hyperlinks to your news sources in the fields below. You can enter one URL per field, with a maximum of three URLs. Clicking "EXTRACT TEXT" will retrieve the text from these links for you to review and edit in the section below.</p>
            </div>
        </div>
        <div id="data_content">

        </div>
    </div>


    <div class="input-form" id="custom_text_div">
        <div class="add-prompt mt-0">
            <h3 class="module-heading">Custom Text <span class="text-muted h6">(optional)</span></h3>
            <div class="input-form mt-1">
                <div class="mb-1">
                    <input class="form-control sessionFields" name="customtxt_url" id="customtxt_url" placeholder="Source" value="<?php echo $field_customtxt_url ?>"
                        <?php 
                        if ($term_slug === 'save-article-as-sample') { ?>
                            disabled ;
                       <?php } 
                        ?>>
                </div>
                <div class="mb-1">
                    <input class="form-control sessionFields" name="customtxt_title" id="customtxt_title" placeholder="Title" value="<?php echo $field_customtxt_title ?>" <?php if ($term_slug === 'save-article-as-sample') { ?> disabled <?php } ?>>
                </div>
                <div class="mb-1">
                    <textarea class="form-control sessionFields" onkeyup="number_of_words()" name="customtext_txtarea" id="customtext_txtarea" cols="30" rows="5" placeholder="Input your own text or additional material." <?php if ($term_slug === 'save-article-as-sample') { ?> readonly <?php } ?>><?php echo htmlspecialchars($field_customtext_txtarea); ?></textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="input-form">
        <div class="add-prompt mt-1">
            <div class="instruction-block mb-3">
                <div class="instructions w-100 d-flex align-items-center justify-content-between mb-1">
                    <h3 class="module-heading">Subject <span class="text-muted h6">(optional)</span></h3>
                    <!-- <button type="button" class="btn btn-dashboard btn-instruction btn-sm">
                        Instructions </button> -->
                </div>
                <div class="instructions-list active">
                    <p class="Note-for-instructions mb-0 text-muted fst-italic">In the space below, clearly define the focus of your desired content. The more detailed and explicit you are about your topic, the better tailored the resulting content will be.</p>
                </div>
            </div>
            <div class="input-form mt-1">
                <div class="mb-1">
                    <input type="text" id="subject" class="form-control sessionFields" placeholder='eg. Write a story about "Alzheimer"s Disease"' name="subject" value="<?php echo htmlspecialchars($field_subject) ?>" <?php if ($term_slug === 'save-article-as-sample') { ?> disabled <?php  } ?>>
                </div>
            </div>
        </div>


        <div class="add-prompt mt-1">
            <div class="instruction-block mb-3">
                <div class="instructions w-100 d-flex align-items-center justify-content-between mb-1">
                    <h3 class="module-heading">Questions <span class="text-muted h6">(optional)</span> </h3>
                    <!-- <button type="button" class="btn btn-dashboard btn-instruction btn-sm">
                        Instructions </button> -->
                </div>
                <div class="instructions-list active">
                    <p class="Note-for-instructions mb-0 text-muted fst-italic">List specific questions you want your content to answer related to your chosen subject. These questions will guide the content generation process to ensure it covers the aspects you deem important.</p>
                </div>
            </div>
            <div class="input-form mt-1">
                <div class="mb-1">
                <textarea class="form-control border-0" name="questions" id="questions" cols="30" rows="7" placeholder='eg.
                    1. "What is Alzheimer"s Disease"
                    2. "What are the symptoms of Alzheimer"s Disease"
                    3. "What are the treatments for Alzheimer"s Disease"
                    4. "Why are blood tests useful for diagnosing Alzheimer"s"
                    5. "What are the drawbacks of using blood tests to diagnose Alzheimer"s"' <?php if ($term_slug === 'save-article-as-sample') { ?> readonly <?php } ?>><?php echo htmlspecialchars($field_questions) ?></textarea>
                </div>
            </div>
        </div>
        <!-- Fetch URL Prompt Field -->

        <?php
        global $chat_gpt_rephraser;

        $prompt_heading = get_field("prompt_heading");
        $prompt_textarea = $chat_gpt_rephraser->get_prompt();

        ?>

        <div class="add-prompt mt-1  <?php if (!current_user_can('administrator')) { ?> d-none <?php } ?>">
            <div class="instruction-block mb-3">
                <div class="instructions w-100 d-flex align-items-center justify-content-between mb-1">
                    <h3 class="module-heading">Prompt</h3>
                    <!-- <button type="button" class="btn btn-dashboard btn-instruction btn-sm">
                            Instructions </button> -->
                </div>
                <div class="instructions-list active">
                    <p class="Note-for-instructions mb-0 text-muted fst-italic">Below is a pre-written prompt that the AI will use to generate
                        your content. This prompt is designed based on proven structures to ensure the
                        output is coherent, engaging, and aligned with your subject and questions. You
                        generally shouldn't need to alter this prompt. However, if there's a specific angle or
                        perspective you want to emphasize, feel free to make slight modifications.</p>
                </div>
            </div>
            <div class="input-form mt-1">
                <div class="mb-1">
                <textarea class="form-control border-0 sessionFields" name="prompt_textarea" id="prompt_textarea" cols="30" rows="7" <?php if ($term_slug === 'save-article-as-sample') { ?> readonly <?php } ?>><?php echo $prompt_textarea; ?></textarea>
                </div>
            </div>
        </div>


        <!-- Fetch URL Button Field -->
        <?php
        global $chat_gpt_rephraser;
        ?>
        <div class="ai-selector" <?php if (current_user_can('administrator') && $term_slug === 'save-article-as-sample') { ?> style="display: none;" <?php } else if (current_user_can('administrator') && $term_slug === 'save-archive-articles-as-draft') { ?> style="display: none;" <?php } else if (current_user_can('administrator')) { ?> style="display: block;" <?php  } else { ?> style="display: none;" <?php }  ?>>
            <div class="instruction-block mb-3">
                <div class="instructions w-100 d-flex align-items-center justify-content-between mb-1">
                    <h3 class="module-heading"><?php echo "GPT Model" ?></h3>
                    <!-- <button type="button" class="btn btn-dashboard btn-instruction btn-sm">
                            Instructions </button> -->
                </div>
                <div class="instructions-list active">
                    <p class="Note-for-instructions mb-0 text-muted fst-italic">Select the AI model you prefer. The default option is
                        typically the most versatile and capable of handling a broad spectrum of topics and
                        styles.</p>
                </div>
            </div>
            <div class="selector-dashboard mt-1">
                <div class="mb-1">
                    <?php
                    $gpt_dropdown = $chat_gpt_rephraser->get_gpt_models(); ?>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-between">
        <div class="btn-module mb-1">
            <a class="btn btn-dashboard <?php if ($term_slug === 'save-article-as-sample') { ?> disabled <?php } ?>" id="fetch-button2" type="button" data-custom-post-id='' data-scraped-post-ids='' 
                <?php 
                $scrapped_post_ids_json = !empty($scrapped_post_ids_json) ? $scrapped_post_ids_json : '[]';
                if ($term_slug === 'save-archive-articles-as-draft') { ?>
                    onclick='update_scrapped_post_and_call_gpt("<?php echo $custom_post_id; ?>", <?php echo $scrapped_post_ids_json; ?>, true, false)'
                <?php } else { ?>
                    onclick='update_scrapped_post_and_call_gpt("<?php echo $custom_post_id; ?>", <?php echo $scrapped_post_ids_json; ?>, true, false)'
                <?php } ?>>
                Send To Chicagostar AI
            </a>
            <?php if ($term_slug !== 'save-article-as-sample') { ?>
                <a class="btn btn-dashboard" id="savePostAsDraft" type="button" data-custom-post-id='' data-scraped-post-ids=''
                    <?php if ($term_slug === 'save-archive-articles-as-draft') { ?>
                        onclick='update_scrapped_post_and_call_gpt("<?php echo $custom_post_id; ?>", <?php echo $scrapped_post_ids_json; ?>, true, true)'
                    <?php } else { ?>
                        onclick='update_scrapped_post_and_call_gpt("<?php echo $custom_post_id; ?>", <?php echo $scrapped_post_ids_json; ?>, true, true)'
                    <?php } ?>>
                    Save Post As Draft
                </a>
            <?php } ?>
        </div>
            <div>
                <b>
                    <p id="word_count">0/0</p>
                </b>
            </div>
        </div>

    </div>

</div>


<div class="element-wrapper <?php if ($term_slug === 'save-article-as-sample' || ($term_slug === 'save-archive-articles-as-draft') && !empty($content)) { ?> d-block <?php } else { ?> d-none <?php } ?>">
    <div id="content_block">
        <?php
        // Editor settings
        $editor_id = 'p_response';
        $settings = array(
            'media_buttons' => false,
            'textarea_name' => 'my_custom_editor',
            'editor_height' => 500,
        );

        if ($term_slug === 'save-article-as-sample') {
            if (current_user_can('administrator')) {
                $settings['tinymce'] = array(
                    'readonly' => true,
                );
            }
            wp_editor($used_input_values, $editor_id, $settings);
        } else if($term_slug === 'save-archive-articles-as-draft') {
            wp_editor($used_input_values, $editor_id, $settings);
        } else {
            wp_editor($content, $editor_id, $settings);
        }
        ?>
    </div>

    <div class="d-flex flex-wrap mt-3">

        <!-- Helper Text -->
        <p class="col-md-12 text-capitalize mb-1">
            <strong>Notes to the writer:</strong> Ai does not replace a human writer. <br />
            Please check that:<br />
        </p>
        <ul class="helper-text">
            <li>Your text is original</li>
            <li>all your sources are properly cited</li>
            <li>quotes are correctly attributed to sources</li>
        </ul>

        <?php if (current_user_can('administrator')) { ?>
            <div class="btn-module me-3 mb-2">
                <button class="btn btn-dashboard" id="save-article-as-sample" type="button" onclick="create_gpt_post('Save Article As Sample' , 'From Fetch from URL' , false , '<?php echo $gpt_post_id ?>' , '<?php echo $custom_post_id ?>' )" <?php if ($term_slug === 'save-article-as-sample') { ?> disabled <?php } ?>>SAVE ARTICLE AS SAMPLE
                </button>
            </div>
        <?php } ?>

        <div class="btn-module me-3 mb-2">
            <button class="btn btn-dashboard" id="save-button" type="button" onclick="create_gpt_post('From Fetch from URL' , null , false , '<?php echo $gpt_post_id ?>' ,'<?php echo $custom_post_id ?>' )" <?php if ($term_slug === 'save-article-as-sample') { ?> disabled <?php } ?>>SAVE
            </button>
        </div>

        <div class="btn-module me-3 mb-2">
            <button class="btn btn-dashboard" id="saveprePostAsDraft" type="button" onclick="create_gpt_post('save-archive-articles-as-draft', 'From Fetch from URL' , true , '<?php echo $gpt_post_id ?>' , '<?php echo $custom_post_id ?>' )" <?php if ($term_slug === 'save-article-as-sample') { ?> disabled <?php } ?>>SAVE AS DRAFT
            </button>
        </div>

        <div class="btn-module me-3 mb-2">
            <a class="btn btn-dashboard disabled" id="View-button" type="button">
                VIEW
            </a>
        </div>
        <input type="hidden" name="custom_post_id" id="custom_post_id" />
        <div class="btn-module me-3 mb-2">
            <button class="btn btn-dashboard" id="sndtoblox-button" type="button" disabled>
                SEND TO BLOX
            </button>
        </div>
        <div class="btn-module me-3 mb-2">
            <button type="button" class="btn btn-dashboard" data-bs-toggle="modal" data-bs-target="#comparison_modal" id="compare-button" disabled>COMPARE</button>

            <!-- Modal -->
            <div class="modal fade" id="comparison_modal" tabindex="-1" aria-labelledby="comparison_modalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="comparison_modalLabel">Comparison Actions</h5>
                            <button type="button" class="btn-close" id="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="d-flex justify-content-evenly py-5">
                                <!-- <button type="button" class="btn btn-primary" onclick="">View</button>
                                <button type="button" class="btn btn-primary" onclick="">Save</button> -->
                                <button style="max-height: 37px;" type="button" class="btn btn-primary" id="comparison-button">Compare</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="input-form mt-4" id="Revisions">
            <div class="add-prompt mt-0">
                <h3 class="module-heading">AI Response Revisions<span class="text-muted h6"></span></h3>
                <?php echo $revision_divs; ?>
            </div>
        </div>

        <div class="input-form mt-4" id="scrappedRevisions">
            <div class="add-prompt mt-0">
                <h3 class="module-heading">Scrapped Post Revisions<span class="text-muted h6"></span></h3>
                <?php echo $scrapped_revision_divs; ?>
            </div>
        </div>
        
    </div>
</div>

<?php

$current_user_id = get_current_user_id();
$block_test_user = get_field('block_test_user', 'option');

if ($block_test_user) {
    $blocked_user_ids = array();
    foreach ($block_test_user as $user_id) {
        $blocked_user_ids[] = $user_id;
    }
}

echo '<script>';
echo 'var blockTestUser = ' . json_encode($blocked_user_ids) . ';';
echo 'var currentUserID = ' . json_encode($current_user_id) . ';';
echo '</script>';

?>

<script>
    var inputCount = 0;
    var view_url = '';

        function collectData() {
            const mapValues = (selector) => jQuery(selector).map((_, el) => jQuery(el).val()).get();

            const fetchButton2 = jQuery('#fetch-button2');
            const scrapedPostIds = fetchButton2.attr('data-scraped-post-ids') ? fetchButton2.attr('data-scraped-post-ids').split(',') : [];

            return {
                newsFeedLinks: mapValues('input[name="newsFeedLink[]"]'),
                fetchUrlLinks: mapValues('input[name="fetchUrlLink[]"]'),
                fetchUrlTitles: mapValues('input[name="fetchUrlTitle[]"]'),
                fetchUrlContents: mapValues('textarea[name="fetchURLContent[]"]'),
                customtxt_url: mapValues('input[name="customtxt_url"]'),
                customtxt_title: mapValues('input[name="customtxt_title"]'),
                customtext_txtarea: mapValues('textarea[name="customtext_txtarea"]'),
                subject: mapValues('input[name="subject"]'),
                questions: mapValues('textarea[name="questions"]'),
                customPostIdFetch: fetchButton2.attr('data-custom-post-id'),
                scrapedPostIdsFetch: scrapedPostIds
            };
        }
        

        function onSessionData() {
            const data = collectData();

            jQuery.ajax({
                url: ajaxurl, // Ensure ajaxurl is defined and accessible
                method: 'POST',
                data: {
                    action: 'sessionManager',
                    data: data
                },
                success: function (response) {
                    //console.log('Newly stored values in session variable:', response);
                    try {
                        const storedData = JSON.parse(response);
                        //console.log('Stored Data:', storedData);
                    } catch (e) {
                        console.error('Failed to parse JSON response:', e);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error storing values in session variable:', error);
                }
            });
        }

        setInterval(onSessionData, 2000);

        // const inputs = [
        //     'input[name="newsFeedLink[]"]',
        //     'input[name="fetchUrlLink[]"]',
        //     'input[name="fetchUrlTitle[]"]',
        //     'textarea[name="fetchURLContent[]"]',
        //     'input[name="customtxt_url"]',
        //     'input[name="customtxt_title"]',
        //     'textarea[name="customtext_txtarea"]',
        //     'input[name="subject"]',
        //     'textarea[name="questions"]',
        //     '#fetch-button2[data-custom-post-id]',
        //     '#fetch-button2[data-scraped-post-ids]'
        // ].join(', ');

        //jQuery(document).on('blur', inputs, onSessionData);

        if (
            termSlug === 'save-archive-articles-as-draft' ||
            (Array.isArray(fieldNewsFeedLinks) && fieldNewsFeedLinks.every(link => link.length > 10))
        ) {
            jQuery('.add-feed-button').removeClass('disabled');
        }


    jQuery(document).ready(function () {
        
        jQuery(document).on('input', '.newsFeedLinksClass', function () {
            var url = jQuery(this).val();
            var newUrl = isUrlValid(url);

            if (!newUrl && newUrl === "") {
                jQuery('#fetch-button').addClass('disabled');
                jQuery(this).val('');
                Swal.fire({
                    title: 'ADD URL',
                    text: 'Please Add Valid URL',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            } else {
                jQuery('.add-feed-button').toggleClass('disabled', !newUrl);
                jQuery('#fetch-button').removeClass('disabled');
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        // Attach a click event listener to the element with the ID 'overlayBtn'
        var overlayBtn = document.getElementById('fetch-button');

        var contentFetched = false;

        if (overlayBtn) {
            overlayBtn.addEventListener('click', function(event) {

                var status = 0;
                event.preventDefault();
                var inputCount = 0;

                var url = jQuery('.newsFeedLinksClass').val();

                if (isUrlValid(url)) {
                    var formdata = new FormData(document.getElementById("fetch-url-form"));
                    formdata.append("action", "scrape_rssfeed");

                    jQuery.ajax({
                        type: "POST",
                        url: ajaxurl,
                        data: formdata,
                        contentType: false,
                        processData: false,
                        cache: false,
                        dataType: 'json',
                        beforeSend: function() {
                            showPreloader();
                        },
                        success: function(data) {
                            status = data.status_Code;
                            scrapped_post_ids = [];
                            word_count = 0;
                            if (status == 200) {
                                for (var i = 0; i < (data.scrapped_data.length) - 1; i++) {
                                    let newPostTitle = data.scrapped_data[i].new_post_title;
                                    let newPostDescription = data.scrapped_data[i].new_post_description;
                                    let sourceUrls = data.scrapped_data[i]._source_urls;

                                    jQuery("#fetchUrlLink" + i).val(sourceUrls);
                                    jQuery("#fetchUrlTitle" + i).val(newPostTitle);
                                    jQuery("#fetchURLContent" + i).val(newPostDescription);

                                    // CK Editor
                                    jQuery(".ck-editor").remove();
                                    var ele = `#fetchURLContent${i}`;

                                    ClassicEditor
                                        .create(document.querySelector(ele))
                                        .then(editor => {
                                            var myEditor = editor;

                                            function clearEditorContent() {
                                                myEditor.setData('');
                                            }

                                            jQuery('#reset-post').on('click', clearEditorContent);

                                            editor.model.document.on('change:data', () => {
                                                number_of_words_editor(editor.sourceElement.id, editor.getData());
                                            });

                                            editor.ui.focusTracker.on('change:isFocused', (evt, name, value) => {
                                                if (value) {
                                                    onSessionData(); 
                                                }
                                            });
                                        })
                                        .catch(error => {
                                            console.error('CKEditor error:', error);
                                        });

                                    if (jQuery("#fetchURLContent" + i).val()) {
                                        let words = jQuery("#fetchURLContent" + i).val().trim().split(/\s+/).length;
                                        word_count += words;
                                    }
                                    scrapped_post_ids.push(data.scrapped_data[i].scrapped_post_id);
                                }
                                var wrd = jQuery("#customtext_txtarea").val().trim().split(/\s+/).length;
                                word_count += wrd;

                                jQuery('#fetch-button2').attr('onclick', '');
                                jQuery('#fetch-button2').unbind('click');

                                jQuery("#word_count").html(word_count + "/1200")
                                if (word_count > 1200) {
                                    jQuery('#word_count').removeClass('text-sucess');
                                    jQuery("#word_count").addClass("text-danger");
                                } else {
                                    jQuery('#word_count').removeClass('text-danger');
                                    jQuery("#word_count").addClass("text-sucess");
                                }

                                contentFetched = true;

                                var customPostId = data.scrapped_data[(data.scrapped_data.length) - 1].custom_post_id;
                                var scrappedPostIds = scrapped_post_ids.join(',');

                                // Update the data attributes with the new values
                                jQuery('#fetch-button2').attr('data-custom-post-id', customPostId);
                                jQuery('#fetch-button2').attr('data-scraped-post-ids', scrappedPostIds);

                                jQuery('#savePostAsDraft').attr('data-custom-post-id', customPostId);
                                jQuery('#savePostAsDraft').attr('data-scraped-post-ids', scrappedPostIds);

                                jQuery('#fetch-button2').attr('onclick', 'update_scrapped_post_and_call_gpt(' + data.scrapped_data[(data.scrapped_data.length) - 1].custom_post_id + ', [' + scrapped_post_ids.join(',') + '], ' + contentFetched + ', false)');

                                jQuery('#savePostAsDraft').attr('onclick', 'update_scrapped_post_and_call_gpt(' + data.scrapped_data[(data.scrapped_data.length) - 1].custom_post_id + ', [' + scrapped_post_ids.join(',') + '],' + contentFetched + ',true)');

                                jQuery("#custom_text_div").removeClass("d-none");

                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.log('AJAX Error:', jqXHR, textStatus, errorThrown);
                            Swal.fire({
                                title: 'Failed',
                                text: 'Error occurred while fetching data. Please try again.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        },
                        complete: function() {
                            if (status == 200) {
                                jQuery("#content_div").removeClass("d-none");
                                Swal.fire({
                                    title: 'Success',
                                    text: 'Article Fetched Successfully',
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                });
                            } else if (status == 404) {
                                Swal.fire({
                                    title: 'Failed',
                                    text: 'Error with returned data. Please contact support.',
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            } else if (status == 503) {
                                Swal.fire({
                                    title: 'Warning',
                                    text: 'Url Field is empty',
                                    icon: 'warning',
                                    confirmButtonText: 'OK'
                                });
                            }
                            hidePreloader();
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Add URL',
                        text: 'Please add URL',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                }
                // inputCount++;

            });
        }
    });

    function update_scrapped_post_and_call_gpt(custom_post_id, scrapped_post_id, contentFetched, draft = false) {

        if (fieldFetchUrlLinks !== null && fieldFetchUrlLinks !== "") {
            contentFetched = true;
        }

        // Check if any of the inputs are empty
        var linkInputs = document.querySelectorAll('[name^="fetchUrlLink"]');
        var titleInputs = document.querySelectorAll('[name^="fetchUrlTitle"]');
        var contentInputs = document.querySelectorAll('.fetchURLContent');

        var isEmptyUrl = [...linkInputs].some(input => input.value.trim() === '');
        var isEmptyTitle = [...titleInputs].some(input => input.value.trim() === '');
        var isEmptyContent = [...contentInputs].some(input => input.value.trim() === '');

        if ((typeof custom_post_id !== 'undefined' && typeof scrapped_post_id !== 'undefined') ||
            (jQuery('#customtxt_url').val() != '' && jQuery('#customtxt_title').val() != '' && jQuery('#customtext_txtarea').val() != '') ||
            jQuery('#newsFeedLink').val() != '') {

                if (contentFetched && jQuery('#newsFeedLink').val() == '' && jQuery('#customtxt_url').val() != '') {
                    contentFetched = false;
                }

            if (contentFetched && jQuery('#newsFeedLink').val() != '') {

                if (isEmptyUrl) {
                    Swal.fire({
                        title: 'Error',
                        text: 'URL are empty. Please add the URL or Extract the text again.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                if (isEmptyTitle) {
                    Swal.fire({
                        title: 'Error',
                        text: 'Title is empty. Please add the Title or Extract the text again.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                if (isEmptyContent) {
                    Swal.fire({
                        title: 'Error',
                        text: 'Content is empty. Please add the Content or Extract the text again.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

            } else if (contentFetched && jQuery('#newsFeedLink').val() === '') {

                Swal.fire({
                    title: 'Error',
                    text: 'URL are empty. Please add the URL or Extract the text again.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return;

            } else if (!contentFetched && jQuery('#newsFeedLink').val() != '') {
                Swal.fire({
                    title: 'Error',
                    text: 'Please extract text before proceeding.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return;
            }

            if (typeof custom_post_id === 'undefined') {
                custom_post_id = null;
            }
            if (typeof scrapped_post_id === 'undefined') {
                scrapped_post_id = null;
            }

            // Proceed with normal flow
            var formdata = new FormData();
            var status = 0;
            var elementWrapper = document.querySelector('.element-wrapper');
            formdata.append("action", "update_scrape_post_and_gpt_call");

            const elements1 = document.querySelectorAll('[name$="fetchUrlLink[]"]');
            elements1.forEach((element, index) => {
                formdata.append(`fetchUrlLink[${index}]`, element.value);
            });

            const elements2 = document.querySelectorAll('[name$="fetchUrlTitle[]"]');
            elements2.forEach((element, index) => {
                formdata.append(`fetchUrlTitle[${index}]`, element.value);
            });

            const elements3 = document.querySelectorAll('[name$="fetchURLContent[]"]');
            elements3.forEach((element, index) => {
                formdata.append(`fetchURLContent[${index}]`, element.value);
            });

            var textareas = document.getElementsByClassName("fetchURLContent");
            var totalWords = 0;
            for (var i = 0; i < textareas.length; i++) {
                var words = textareas[i].value.trim().split(/\s+/).length;
                totalWords += words;
            }
            var customtext_words = (document.getElementById("customtext_txtarea")).value.trim().split(/\s+/).length;
            totalWords += customtext_words;

            var customtext_txtarea = (document.getElementById("customtext_txtarea")).value;
            formdata.append("customtext_txtarea", customtext_txtarea);
            formdata.append("custom_post_id", custom_post_id);
            formdata.append("scrapped_post_id", scrapped_post_id);
            formdata.append("prompt", jQuery("#prompt_textarea").val());
            formdata.append("gpt_model_id", jQuery("#gpt_model_name").val());
            formdata.append("customtxt_title", jQuery("#customtxt_title").val());
            formdata.append("customtxt_url", jQuery("#customtxt_url").val());
            formdata.append("totalWords", totalWords);
            formdata.append("draft", draft);

            editorId = 'p_response';

            jQuery.ajax({
                type: "POST",
                url: ajaxurl,
                data: formdata,
                contentType: false,
                processData: false,
                cache: false,
                dataType: 'json',
                beforeSend: function() {
                    showPreloader();
                },
                success: function(data) {
                    status = data.status_Code
                    if (status == 509) {
                        Swal.fire({
                            title: 'Note',
                            text: 'Number of words exceeded. Please limit content to 1200 words',
                            icon: 'warning',
                            confirmButtonText: 'OK'
                        });
                    } else {
                        if (draft == false) {
                            var editor = tinymce.get(editorId);
                            editor.setContent('');
                            editor.insertContent(data.content_array);
                        }
                        jQuery("#custom_post_id").val(data.custom_post_id);

                        if (termSlug !== 'save-archive-articles-as-draft') {
                            Swal.fire({
                                title: 'Success',
                                text: 'Response generated successfully',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            });
                        } else {
                            Swal.fire({
                                title: 'Success',
                                text: 'Response generated successfully',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(function() {

                                jQuery('html, body').animate({
                                    scrollTop: jQuery('#wp-p_response-editor-container').offset().top
                                }, 200);

                            });
                        }

                    }
                },

                complete: function() {
                    if (status == 200) {
                        if (draft == false) {
                            elementWrapper.classList.remove('d-none');
                            elementWrapper.classList.add('d-block');
                            saveDraftBtn = jQuery('#savePostAsDraft');
                            if (saveDraftBtn) {
                                saveDraftBtn.remove();
                            }
                        } 
                    }

                    hidePreloader();
                }
            });
        } else if (jQuery('#customtxt_url').val() === '' || jQuery('#customtxt_title').val() === '' || jQuery('#customtext_txtarea').val() === '') {
            Swal.fire({
                title: 'Error Validation',
                text: 'Please provide at least NEWS URL or fill all CUSTOM TEXT fields',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    }

    function isUrlValid(url) {
        var pattern = new RegExp('^(https?:\\/\\/)?' + // protocol
            '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|' + // domain name
            '((\\d{1,3}\\.){3}\\d{1,3}))' + // ip (v4) address
            '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*' + //port
            '(\\?[;&a-z\\d%_.~+=-]*)?' + // query string
            '(\\#[-a-z\\d_]*)?' + // fragment locator
            '(\\.com|\\.org)?$', 'i'); // TLD
        return !!pattern.test(url);
    }

    //jQuery('#fetch-button2').attr('onclick', 'swalFireOnResetButton()');

    function swalFireOnResetButtonClick() {
        Swal.fire({
            title: 'Article Reset',
            text: 'Article Reset Successfully',
            icon: 'success',
            confirmButtonText: 'OK'
        });
    }


    jQuery('#reset-post').on('click', function() {
        swalFireOnResetButtonClick();
    });

    jQuery('#reset-post').on('click', function() {
        // Hide the content_div by setting its display style to 'none'
        jQuery("#content_div").addClass("d-none");
    });

    function reset_article() {

        inputCount = 1;
        jQuery('#fetch-button2').attr('onclick', '');
        jQuery('#fetch-button2').unbind('click');
        jQuery('#fetch-button2').attr('onclick', 'update_scrapped_post_and_call_gpt()');

        jQuery('#savePostAsDraft').attr('onclick', '');
        jQuery('#savePostAsDraft').unbind('click');
        jQuery('#savePostAsDraft').attr('onclick', 'update_scrapped_post_and_call_gpt()');

        jQuery('.form-control').val('');

        jQuery(".scrapped-link-div input").val('');
        jQuery(".scrapped-link-div input:not(:first)").remove();


        jQuery("#customtxt_url, #customtxt_title, #customtext_txtarea").val('');
        editorId = 'p_response';
        var editor = tinymce.get(editorId);
        editor.setContent('');

        const addButton = document.querySelector('.add-feed-button');
        addButton.style.display = 'flex';
        addButton.style.width = 'fit-content';
        addButton.classList.add('disabled');

        jQuery(".element-wrapper").addClass("d-none");
        jQuery("#word_count").html("0/1200");

        destroy_session_ajax_newsmaster();

    }

    function destroy_session_ajax_newsmaster() {
        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'destroy_session_newsmaster'
            },
            success: function (response) {
                try {
                    var data = JSON.parse(response);
                    if (data.status === "success") {
                       window.location.href = baseUrl;
                    } else {
                        console.log("Failed to destroy session.");
                    }
                } catch (e) {
                    console.error("Error parsing JSON response: " + e);
                }
            },
            error: function (xhr, status, error) {
                console.error("An error occurred: " + error);
            }
        });
    }


    function number_of_words() {
        var textareas = document.getElementsByClassName("fetchURLContent");
        var totalWords = 0;
        for (var i = 0; i < textareas.length; i++) {
            var content = textareas[i].value.trim();
            if (content !== "") {
                var words = content.split(/\s+/).length;
                totalWords += words;
            }
        }

        var editorContent = jQuery("#customtext_txtarea").val().trim();
        if (editorContent !== "") {
            var editorWords = editorContent.split(/\s+/).length;
            totalWords += editorWords;
        }

        jQuery("#word_count").html(totalWords + "/1200");
        if (totalWords > 1200) {
            jQuery('#word_count').removeClass('text-success').addClass('text-danger');
        } else {
            jQuery('#word_count').removeClass('text-danger').addClass('text-success');
        }
    }



    function number_of_words_editor(element, data) {
        jQuery('#' + element).val(data);
        number_of_words();
    }

    function create_gpt_post(value = null, secondaryValue = null, draft = false , gptPostId = null , custom_post_id = null) {

        var editorId = 'p_response';
        var editor = tinymce.get(editorId);
        var content_array = editor.getContent();
        var status = 0;
        if(getParameter){
            var c_id = custom_post_id;
        }else{
            var c_id = jQuery("#custom_post_id").val();
        }
        
        var prompt = jQuery("#prompt_textarea").val();

        var elements1 = document.querySelectorAll('[name$="fetchUrlLink[]"]');
        var fetchUrlLink = Array.from(elements1).map(element => element.value);
       
        var elements2 = document.querySelectorAll('[name$="fetchUrlTitle[]"]');
        var fetchUrlTitle = Array.from(elements2).map(element => element.value);

        var elements3 = document.querySelectorAll('[name$="fetchURLContent[]"]');
        var fetchURLContent = Array.from(elements3).map(element => element.value);
        var customtxt_url = document.querySelector('input[name="customtxt_url"]').value;
        var customtxt_title = document.querySelector('input[name="customtxt_title"]').value;
        var customtext_txtarea = document.querySelector('textarea[name="customtext_txtarea"]').value;
        var firstTaxonomyValue = value;
        var secondTaxonomyValue = secondaryValue;
        gptPostId = gptPostId;
        
        var action = "save_gpt_post";
        if (draft == true) {
            action = "update_gpt_post";
            firstTaxonomyValue = value,
            secondTaxonomyValue = secondaryValue
        }

        if (content_array != "") {
            jQuery.ajax({
                type: "POST",
                url: ajaxurl,
                data: {
                    action: action,
                    c_id: c_id,
                    content_array: content_array,
                    prompt: prompt,
                    taxanomy: firstTaxonomyValue,
                    secondarytaxanomy: secondTaxonomyValue,
                    draft: draft,
                    fetchUrlLink: fetchUrlLink,
                    fetchUrlTitle: fetchUrlTitle,
                    fetchURLContent: fetchURLContent,
                    customtxt_url: customtxt_url,
                    customtxt_title: customtxt_title,
                    customtext_txtarea: customtext_txtarea,
                    gptPostId : gptPostId
                },
                async: false,
                success: function(data) {

                    if (data.status_Code == 200) {

                        status = data.status_Code
                        view_url = data.gpt_post_permalink
                        jQuery("#View-button").attr({
                            "target": "_blank",
                            "href": data.gpt_post_permalink
                        });
                        jQuery('#View-button').removeClass('disabled');
                        jQuery("#sndtoblox-button").on("click", function() {
                            create_wp_post_blox(data.gpt_post_id);
                        });
                        jQuery("#comparison-button").on("click", function() {
                            compare_orginal_gpt_content(data.gpt_post_id)
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: data.message,
                            icon: 'error', // success, error, warning, info, question
                            confirmButtonText: 'OK'
                        });
                    }

                }
            });
            if (status == 200) {
                Swal.fire({
                    title: 'Success',
                    text: 'Post Saved Successfully.',
                    icon: 'success', // success, error, warning, info, question
                    confirmButtonText: 'OK'
                });
                // jQuery('#View-button').css('pointer-events', 'auto');
                jQuery('#View-button').css('opacity', '1');
                var isBlocked = false;
                for (var i = 0; i < blockTestUser.length; i++) {
                    if (currentUserID == blockTestUser[i]) {
                        isBlocked = true;
                        break;
                    }
                }
                if (isBlocked) {
                    jQuery('#sndtoblox-button').prop('disabled', true);
                } else {
                    jQuery("#sndtoblox-button").prop("disabled", false);
                }
                jQuery('#compare-button').prop('disabled', false);
                jQuery('#comparison-button').show();
                jQuery('#compare-button').show();
            }
        } else {
            Swal.fire({
                title: 'Error',
                text: 'No generated Data.',
                icon: 'error', // success, error, warning, info, question
                confirmButtonText: 'OK'
            });
        }

    }


    function create_wp_post_blox(gpt_post_id) {
        var status = 0;
        jQuery.ajax({
            type: "POST",
            url: ajaxurl,
            data: {
                action: "create_wp_post_for_blox",
                gpt_post_id: gpt_post_id,
            },
            async: false,
            success: function(data) {
                status = data.status_Code;
            }
        });
        if (status == 200) {
            Swal.fire({
                title: 'Success',
                text: 'Post Created Successfully.',
                icon: 'success', // success, error, warning, info, question
                confirmButtonText: 'OK'
            });
        } else {
            Swal.fire({
                title: 'Failed',
                text: 'Failed to create post.',
                icon: 'error', // success, error, warning, info, question
                confirmButtonText: 'OK'
            });
        }
    }

    function compare_orginal_gpt_content(gpt_post_id) {
        var status = 0;
        jQuery('#comparison-button').css('background-color', 'green').html('Generating Comparison...');
        jQuery.ajax({
            type: "POST",
            url: ajaxurl,
            data: {
                action: "output_comparison",
                gpt_post_id: gpt_post_id,
            },
            beforeSend: function() {
                showPreloader();
            },
            success: function(data) {
                status = data.status_Code;
            },
            complete: function() {
                if (status == 200) {
                    jQuery('#comparison-button').html('Comparison Successful');
                    Swal.fire({
                        title: 'Success',
                        text: 'Comparison Successful.',
                        icon: 'success', // success, error, warning, info, question
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.open(view_url, '_blank');
                        }
                    });
                }
                hidePreloader();
                document.getElementById("btn-close").click();
            }
        });
    }


    function showPreloader() {
        var loader = jQuery('#preloader');
        loader.css('display', 'block');

    }

    function hidePreloader() {
        var loader = jQuery('#preloader');
        loader.css('display', 'none');
    }


    const maxInputs = 3;
    const container = document.querySelector('.url_input');
    const container2 = document.getElementById('data_content');

    function generateInputMarkup(newSourceUrl) {
        if(!newSourceUrl){
            newSourceUrl = '';
        }
        return `
                <input type="text" class="newsFeedLinksClass form-control mb-2 sessionFields" value="${newSourceUrl}" placeholder="Example: https://www.example.com/year/month/day/single-news" name="newsFeedLink[]" id="newsFeedLink" <?php if ($term_slug === 'save-article-as-sample') { ?> disabled <?php } ?> >
            `;
    }

    function generateSourceMarkup(fetchSourceUrl, fetchSourcetitle, fetchSourcecontent, inputCount) {
        if(!fetchSourceUrl || !fetchSourcetitle ||!fetchSourcecontent){
            fetchSourceUrl = '';
            fetchSourcetitle = '';
            fetchSourcecontent = '';
        } 
        return `
            <div class="input-form main-form d-flex flex-column scrapped-content-div" >
            <div class="mb-3 w-100">
                <label for="fetchUrlLink" class="form-label">
                    URL
                </label>
                <input type="text" class="form-control sessionFields" name="fetchUrlLink[]" id="fetchUrlLink${inputCount}" aria-describedby="fetchurltext" value="${fetchSourceUrl}" <?php if ($term_slug === 'save-article-as-sample') { ?> disabled <?php } ?>>
            </div> 
            <div class="mb-3">
                <label for="fetchUrlTitle" class="form-label">
                    Title
                </label>
                <input type="text" class="form-control sessionFields" name="fetchUrlTitle[]" id="fetchUrlTitle${inputCount}"
                    aria-describedby="fetchUrlTitleText" value="${fetchSourcetitle}" <?php if ($term_slug === 'save-article-as-sample') { ?> disabled <?php } ?>>
            </div>
            <div class="mb-3">
                <label for="fetchURLContent" class="form-label">
                    Content
                </label>
                <textarea type="textarea" onkeyup="number_of_words()" class="form-control fetchURLContent sessionFields" name="fetchURLContent[]"
                id="fetchURLContent${inputCount}"
                    aria-describedby="fetchURLContentText" rows="6" <?php if ($term_slug === 'save-article-as-sample') { ?> readonly <?php } ?>> ${fetchSourcecontent} </textarea>
            </div>
            </div>
            `;
    }

    function scrappedPostDataForMarkup() {
        let markupArray = {
            inputMarkups: [],
            sourceMarkups: []
        };
        scrappedPostData.forEach((post, index) => {
            let newsFeedLink = post.newsFeedLinks;
            let fetchSourceUrl = post.url;
            let fetchSourcetitle = post.title;
            let fetchSourcecontent = post.content;

            if(termSlug == 'save-archive-articles-as-draft' || termSlug == 'save-article-as-sample'){
                var newSourceUrl = fetchSourceUrl;
            }else{
                var newSourceUrl = newsFeedLink;
            }
            // Generate the markup for input and add to inputMarkups array
            let inputMarkup = generateInputMarkup(newSourceUrl);
            markupArray.inputMarkups.push(inputMarkup);

            // Generate the markup for source and add to sourceMarkups array
            let sourceMarkup = generateSourceMarkup(fetchSourceUrl, fetchSourcetitle, fetchSourcecontent, index);
            markupArray.sourceMarkups.push(sourceMarkup);

        });

        return markupArray;
    }

    function addInput(element) {
        if (inputCount < maxInputs) {
            const newInputDiv = document.createElement('div');
            newInputDiv.classList.add('scrapped-link-div');
            newInputDiv.innerHTML = generateInputMarkup();

            const newInputContent = generateSourceMarkup(null,null,null,inputCount);

            newAddedInput(newInputDiv, newInputContent, element);
        }
    }

    function addPostSourceData(element) {
        if (inputCount < maxInputs) {
            const newInputDiv = document.createElement('div');
            newInputDiv.classList.add('scrapped-link-div');
            //newInputDiv.innerHTML = scrappedPostDataForMarkup();
            returnMarkup = scrappedPostDataForMarkup();
            if (returnMarkup.inputMarkups) {
                newInputDiv.innerHTML = returnMarkup.inputMarkups.join('');
                container.appendChild(newInputDiv);
            }
            if (returnMarkup.sourceMarkups) {
                newInputContent = returnMarkup.sourceMarkups.join('');
            }

            newAddedInput(newInputDiv, newInputContent, element);
        }
    }

    function newAddedInput(newInputDiv, newInputContent, element = null) {
        container.appendChild(newInputDiv);
        container2.insertAdjacentHTML('beforeend', newInputContent);
        inputCount++;

        if (inputCount === maxInputs) {
            const addButton = document.querySelector('.add-feed-button');
            addButton.style.display = 'none';
        }

        if(fieldNewsFeedLinks !==''){
            inputCount = jQuery('.newsFeedLinksClass').length;
            if (inputCount === maxInputs) {
                const addButton = document.querySelector('.add-feed-button');
                addButton.style.display = 'none';
            }
        }

        if (element !== null) {
            if (element.classList) {
                element.classList.add('disabled');
            }
        }

        scrappedPostData.forEach((post, index) => {
            classicEditor(index+1);
        });
    }

    function classicEditor(inputCount) {
        ClassicEditor
            .create(document.querySelector(`#fetchURLContent${inputCount - 1}`))
            .then(editor => {
                var myEditor = editor;

                function clearEditorContent() {
                    myEditor.setData('');
                }

                jQuery('#reset-post').on('click', clearEditorContent);

                if (termSlug === 'save-article-as-sample') {
                    myEditor.editing.view.document.isReadOnly = true;
                } else if (termSlug === 'save-archive-articles-as-draft') {
                    myEditor.editing.view.document.isReadOnly = false;
                }

                editor.model.document.on('change:data', () => {
                    number_of_words_editor(editor.sourceElement.id, editor.getData());
                });

                editor.ui.focusTracker.on('change:isFocused', (evt, name, value) => {
                    if (value) {
                        onSessionData();
                    }
                });

                const initialData = editor.getData();
                number_of_words_editor(editor.sourceElement.id, initialData);

            });
    }

  

    function removeInput(element) {
        const inputs = container.querySelectorAll('.form-control[name="newsFeedLink[]"]');
        const lastInput = inputs[inputs.length - 1];
        const lastAdditionalContent = container2.querySelector('.input-form.main-form:last-of-type');

        if (inputs.length > 1 && lastAdditionalContent) {
            lastInput.parentNode.removeChild(lastInput);
            lastAdditionalContent.parentNode.removeChild(lastAdditionalContent);

            inputCount--;

            jQuery('.add-feed-button').removeClass('disabled');

            const addButton = document.querySelector('.add-feed-button');
            addButton.style.display = 'flex';
            addButton.style.width = 'fit-content';
        }
    }

    <?php
    if (isset($_GET['post_id']) || isset($_SESSION['field_fetchUrlLinks']) || isset($_SESSION['field_fetchUrlTitles']) || isset($_SESSION['field_fetchUrlContents'])) { ?>
        addPostSourceData();
    <?php } else { ?>
        addInput(this);
    <?php } ?>

    jQuery(document).ready(function() {
        jQuery(".btn-instruction").click(function() {
            jQuery(this).closest(".instruction-block").find(".instructions-list").toggleClass('active');
        });
    });
</script>


<div id="preloader"></div>
<style>
    *::placeholder {
        opacity: 0.5 !important;
    }

    #preloader {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 9999;
        overflow: hidden;
        background: rgba(255, 255, 255, 0.8);
        display: none;
    }

    #preloader:before {
        content: "";
        position: fixed;
        top: calc(50% - 30px);
        left: calc(50% - 30px);
        border: 6px solid #37517e;
        border-top-color: #fff;
        border-bottom-color: #fff;
        border-radius: 50%;
        width: 60px;
        height: 60px;
        animation: animate-preloader 1s linear infinite;
    }

    @keyframes animate-preloader {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>