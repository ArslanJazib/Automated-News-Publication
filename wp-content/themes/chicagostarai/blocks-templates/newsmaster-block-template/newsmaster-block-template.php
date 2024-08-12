<?php

// Newsmaster Text Box ACF Field
$newsmaster_text = get_field('newsmaster_text');
// Newsmaster Prompt Box ACF Field
$newsmaster_prompt = get_field('newsmaster_prompt');
// Newsmaster Generate Story ACF Field
$newsmaster_button = get_field('newsmaster_button');

$term_slug = '';

$text_area_session_value = '';

$prompt_text_area_session_value = '';

$base_url = get_home_url();

$get_parameter = isset($_GET['post_id']) ? $_GET['post_id'] : '';

$gpt_post_id = '';

if(isset($_SESSION['text_textarea']) || isset($_SESSION['prompt_textarea'])){
    $text_area_session_value = $_SESSION['text_textarea'];
    $prompt_text_area_session_value = $_SESSION['prompt_textarea'];
}

if (isset($_GET['post_id'])) {

    $gpt_post_id = $_GET['post_id'];

    $used_prompt = get_field('used_prompt', $gpt_post_id);

    $used_input_values = get_field('newsmaster_text' , $gpt_post_id);

    $categories = get_the_terms($gpt_post_id, 'gpt_categories');

    if ($categories) {
        foreach ($categories as $category) {
            $term_slug = $category->slug;
        }
    }

    $post_content = get_post_field('post_content', $gpt_post_id);

}

?>
<script>
    var baseUrl = <?php echo json_encode($base_url) ?>;
    var getParameter = "<?php echo $get_parameter; ?>";
</script>

<div class="btn-module mb-3 d-flex justify-content-end">
    <a class="btn btn-sm <?php if ($term_slug === 'save-article-as-sample') { ?> disabled <?php } ?>" id="reset-post" type="button" onclick="reset_article()" style="background-color: #dc3545!important;">RESET ARTICLE</a>
</div>
<form id="newsmasterForm">
    <div class="add-prompt mt-5">
        <h4 class="block-head">
            <?php echo $newsmaster_text ?> <span class="text-muted h6">(optional)</span>
        </h4>
        <div class="input-form mt-3">
            <div class="mb-3">
            <textarea class="form-control border-0 sessionFields" name="text_textarea" id="text_textarea" cols="30" rows="7" placeholder="Insert text that you want to manipulate...." required <?php if($term_slug === 'save-article-as-sample') { ?> disabled <?php }  ?>><?php if($term_slug === 'save-article-as-sample' || $term_slug === 'save-archive-articles-as-draft') { echo htmlspecialchars(strip_tags($used_input_values));} else  if (is_array($text_area_session_value)) { echo implode(', ', $text_area_session_value);}?></textarea>
            </div>
        </div>
    </div>
    <div class="add-prompt mt-5">
        <h4 class="block-head">
            <?php echo $newsmaster_prompt ?> 
        </h4>
        <p class="mt-2">Give the AI instructions.</p>
        <div class="input-form mt-3">
            <div class="mb-3">
            <?php
                $content2 = '';
                $editor_id2 = 'prompt_textarea';
                $settings2 = array(
                    'media_buttons' => false,
                    'textarea_name' => 'prompt_textarea',
                    'editor_height' => 300,
                );
                // Display the editor
                if ($term_slug === 'save-article-as-sample') {
                        $settings2['tinymce'] = array(
                            'readonly' => true,
                        );
                        wp_editor($used_prompt, $editor_id2, $settings2);
                } else if ($term_slug === 'save-archive-articles-as-draft') {
                    wp_editor($used_prompt, $editor_id2, $settings2);
                } else if (is_array($prompt_text_area_session_value)) { 
                        wp_editor(implode(', ', $prompt_text_area_session_value), $editor_id2, $settings2);
                    } 
                else   {
                    wp_editor($content2, $editor_id2, $settings2);
                }
            ?>
            <!-- <textarea class="form-control border-0" name="prompt_textarea" id="prompt_textarea" cols="30" rows="7" placeholder="Type any prompt here..." required></textarea> -->
            </div>
        </div>
    </div>
    <div class="btn-module mb-5">
        <a class="btn btn-dashboard submit-button <?php if ($term_slug === 'save-article-as-sample') { ?> disabled <?php } ?>"type="button" data-type="false">
            <?php echo $newsmaster_button ?>
        </a>
        <a class="btn btn-dashboard submit-button <?php if ($term_slug === 'save-article-as-sample') { ?> disabled <?php } ?>" type="button" data-type="true">
            Save Post As Draft
        </a>
    </div>
</form>
<div class="element-wrapper <?php if ($term_slug === 'save-article-as-sample') { ?> d-block <?php } else if ($term_slug === 'save-archive-articles-as-draft' && !empty($post_content)) { ?> d-block <?php } else { ?> d-none <?php } ?>">

    <?php
    $content = '';

    $editor_id = 'newsmaster_response';
    $settings = array(
        'media_buttons' => true,
        'textarea_name' => 'my_custom_editor',
        'editor_height' => 500,
    );

    if ($term_slug === 'save-article-as-sample') {
        $settings['tinymce'] = array(
            'readonly' => true,
        );
        wp_editor($post_content, $editor_id, $settings);
    } else if ($term_slug === 'save-archive-articles-as-draft') {
        wp_editor($post_content, $editor_id, $settings);
    } else  {
        wp_editor($content, $editor_id, $settings);
    }
    ?>
    <div class="btn-module mb-5 mt-2">
        <a class="btn btn-dashboard <?php if ($term_slug === 'save-article-as-sample') { ?> disabled <?php } ?>" id="save-button" type="button" onclick="create_gpt_post_newsmaster(null , 'From Newsmaster' , null , '<?php echo $gpt_post_id ?>' )">
            SAVE
        </a>
        <a class="btn btn-dashboard <?php if ($term_slug === 'save-article-as-sample') { ?> disabled <?php } ?>" id="saveASSample" type="button" onclick="create_gpt_post_newsmaster(null , 'save-article-as-sample' , 'from-newsmaster' , '<?php echo $gpt_post_id ?>')">
            SAVE SAMPLE ARTICLE
        </a>
        <a class="btn btn-dashboard <?php if ($term_slug === 'save-article-as-sample') { ?> disabled <?php } ?>" id="savePostAsDraft" type="button" onclick="create_gpt_post_newsmaster(true , 'save-archive-articles-as-draft' , 'from-newsmaster' , '<?php echo $gpt_post_id ?>')">
            SAVE POST AS DRAFT
        </a>
    </div>

</div>
<div id="preloadernewsmaster"></div>


<script>
    var globalGptPostId = null;

    var gptPostID = "<?php echo $gpt_post_id; ?>";

    jQuery(document).ready(function () {

        function collectData() {

            var dataArray = [];

            jQuery('.sessionFields').each(function () {
                var value = jQuery(this).val();
                var fieldID = jQuery(this).attr('id');
                dataArray.push({
                    'fieldID': fieldID,
                    'value': value
                });
            });

            var tinymceContent = tinymce.get('prompt_textarea').getContent();
            var editorId = document.querySelector('.wp-editor-area').getAttribute('id');
            dataArray.push({
                'fieldID': editorId,
                'value': tinymceContent
            });

            return dataArray;
        }

        jQuery('.sessionFields').blur(function () {
            onSessionData();
        });


        tinymce.get('prompt_textarea').on('blur', e => {
            onSessionData();
        });

        function onSessionData() {
            var dataArray = collectData();

            jQuery.ajax({
                url: ajaxurl,
                method: 'POST',
                data: {
                    action: 'sessionManagerNewsMaster',
                    data: dataArray
                },
                success: function (response) {
                    console.log('Newly stored values in session variable:', response);
                    try {
                        var storedData = JSON.parse(response);
                        console.log('Stored Data:', storedData);
                    } catch (e) {
                        console.error('Failed to parse JSON response:', e);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error storing values in session variable:', error);
                }
            });
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        var overlayBtn = document.getElementsByClassName('submit-button');
        var elementWrapper = document.querySelector('.element-wrapper');
        var loader = document.getElementById('preloadernewsmaster');
        Array.prototype.forEach.call(overlayBtn, function(element) {
            element.addEventListener('click', function(event) {
                event.preventDefault();
                
                var editorId2 = 'prompt_textarea';
                var editor2 = tinymce.get(editorId2);
                var prompt_textarea_content = editor2.getContent();
                var draft = element.dataset.type;
                var gpt_post_id = globalGptPostId;
                var secondarytaxonomy = 'from-newsmaster';
                var draftGPTPostID = gptPostID;

                var textarea = document.getElementById('text_textarea');
                var inputTextValue = textarea.value;
                if( prompt_textarea_content != ''){

                    loader.classList.add('d-block');
                    if(draft !== 'true') {
                        elementWrapper.classList.remove('d-none');
                        elementWrapper.classList.add('d-block');
                    }
                    var formdata = new FormData(document.getElementById("newsmasterForm"));

                    
                    // Add an 'action' parameter to the form data
                    formdata.append("action", "newsmaster_content_action");
                    formdata.append("prompt_textarea", prompt_textarea_content);
                    formdata.append("draft", draft);
                    formdata.append("secondarytaxonomy", secondarytaxonomy);
                    formdata.append("inputTextValue", inputTextValue);
                    formdata.append("draftGPTPostID", draftGPTPostID);

                    if(gpt_post_id == null){
                        console.log("No GPTPostFound");
                    }else{
                        formdata.append("gpt_post_id", gpt_post_id);
                    }

                    var editorId = 'newsmaster_response';
                    // Make an AJAX POST request using jQuery
                    jQuery.ajax({
                        type: "POST",
                        url: ajaxurl,
                        data: formdata,
                        contentType: false,
                        processData: false,
                        cache: false,
                        dataType: 'json',
                        success: function(data) {
                            var statusCode = data.status_Code;
                            var gptResponse = data.gpt_response;
                            if(draft !== 'true') {
                                var editor = tinymce.get(editorId);
                                editor.setContent('');
                                editor.insertContent(data.gpt_response);
                            } else {
                                globalGptPostId = gptResponse;
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.log('AJAX Error:', jqXHR, textStatus, errorThrown);
                        },
                        complete: function () {
                            // Hide the loader when the process is complete (whether success or error)
                            loader.classList.remove('d-block');
                            if (getParameter) {
                                saveDraftBtn = document.querySelector('a[data-type="true"]');
                                if (saveDraftBtn) {
                                    saveDraftBtn.remove();
                                }
                            }
                        }
                    });
                }
                else{
                    Swal.fire({
                        title: 'Empty Fields',
                        text: 'Please fill all fields',
                        icon: 'warning', // success, error, warning, info, question
                        confirmButtonText: 'OK'
                    });
                }
                
            });
        });
    });

    function create_gpt_post_newsmaster(draft = false, value = null , secondaryValue = null  , gptPostID = null) {

        var editorId = 'newsmaster_response';
        var editor = tinymce.get(editorId);
        var newsmaster_content = editor.getContent();
        var status = 0;
        // Prompt Text Editor
        var editorId2 = 'prompt_textarea';
        var editor2 = tinymce.get(editorId2);
        var prompt = editor2.getContent();
        var created_gpt_post_id = globalGptPostId;
        var draftGPTPostID = gptPostID;

        var textarea = document.getElementById('text_textarea');
        var inputTextValue = textarea.value;

        var action = "save_gpt_post_newsmaster";
        var taxanomy = value;
        var secondaryTaxonomy = secondaryValue;

        if(draft){ 
            action = 'update_gpt_post_newsmaster';
            created_gpt_post_id = gptPostID;
        } 

        jQuery.ajax({
            type: "POST",
            url: ajaxurl,
            data: {
                action: action,
                newsmaster_content: newsmaster_content,
                prompt: prompt,
                taxanomy: taxanomy,
                secondaryTaxonomy: secondaryTaxonomy,
                inputTextValue: inputTextValue,
                draft: draft,
                created_gpt_post_id: created_gpt_post_id,
                draftGPTPostID : draftGPTPostID
            },
            async: false,
            success: function (data) {
                status = data.status_Code;
                globalGptPostId = data.gpt_post_id;
                if (status === 200) {
                    Swal.fire({
                        title: 'Success',
                        text: 'Post Saved Successfully.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });
                } else {
                    Swal.fire({
                        title: 'Failed',
                        text: 'Failed to Save.',
                        icon: 'error', // success, error, warning, info, question
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log('AJAX Error:', jqXHR, textStatus, errorThrown);
            }
        });
    }

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

    function reset_article() {

        jQuery('.sessionFields').val('');
        tinymce.get('prompt_textarea').setContent('');

        destroy_session_ajax();

    }

    function destroy_session_ajax() {
        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'destroy_session'
            },
            success: function (response) {
                try {
                    var data = JSON.parse(response);
                    if (data.status === "success") {
                        window.location.href = baseUrl + '/newsmaster';
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

</script>
<style>
    #preloadernewsmaster {
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

    #preloadernewsmaster:before {
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