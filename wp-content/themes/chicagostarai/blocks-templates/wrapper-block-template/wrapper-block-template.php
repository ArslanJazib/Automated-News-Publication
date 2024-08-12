<form id="custom-form">


    <?php

    $instruction_module_heading = get_field("instruction_module_heading");
    $instruction_module_button_text = get_field("instruction_module_button_text");
    $instruction_repeater_field_flow_one = get_field("instruction_repeater_field_flow_one");
    $seperator = get_field("seperator");
    $flow_two_repeater = get_field("flow_two_repeater");
    $note_for_instruction = get_field("note_for_instruction");
    $hide_instruction_block = get_field("hide_instruction_block");


    ?>
    <?php if(!$hide_instruction_block): ?>
        <div class="instruction-block">
            <div class="instructions w-100 d-flex justify-content-between mb-4">
                <h3 class="module-heading">
                    <?php echo $instruction_module_heading ?>
                </h3>
                <button type="button" class="btn btn-dashboard btn-instruction">
                    <?php echo $instruction_module_button_text ?>
                </button>
            </div>
            <div class="instructions-list">
                <?php
                if(have_rows('instruction_repeater_field_flow_one')) {
                    // Loop through the repeater rows
                    $step_number = 1; // Initialize the step number

                    while(have_rows('instruction_repeater_field_flow_one')) {
                        the_row();

                        // Get the content for the current step
                        $step_content = get_sub_field('flow_one_content');

                        // Output the step with its content
                        echo '<ul class="flow-1">';
                        echo '<li>';
                        echo '<p><span class="fw-bold">Step '.$step_number.' : </span>'.$step_content.'</p>';
                        echo '</li>';
                        echo '</ul>';

                        $step_number++; // Increment the step number
                    }
                }
                ?>
                <h3 class="fs-6 fw-bold my-4">
                    <?php echo $seperator ?>
                </h3>
                <?php
                if(have_rows('flow_two_repeater')) {
                    // Loop through the repeater rows
                    $step_number = 1; // Initialize the step number

                    while(have_rows('flow_two_repeater')) {
                        the_row();

                        // Get the content for the current step
                        $step_content = get_sub_field('flow_two_content');

                        // Output the step with its content
                        echo '<ul class="flow-2">';
                        echo '<li>';
                        echo '<p><span class="fw-bold">Step '.$step_number.' : </span>'.$step_content.'</p>';
                        echo '</li>';
                        echo '</ul>';

                        $step_number++; // Increment the step number
                    }
                }
                ?>
                <p class="Note-for-instructions">
                    <?php echo $note_for_instruction ?>
                </p>
            </div>
        </div>
    <?php endif; ?>


    <!-- Code For Adding Url  -->
    <?php
    $url_label = get_field('url_label');
    $url_placeholder = get_field('url_placeholder');
    $button_text_News = get_field('button_text_news_feed');
    $hide_acf_block = get_field('hide_acf_block');
    $max_number_url = get_field('max_number_url');
    echo '<script>';
    echo 'var maxNumberUrl = '.json_encode($max_number_url).';';
    // echo 'console.log("maxNumberUrl",maxNumberUrl)';
    echo '</script>';
    ?>

    <?php if(!$hide_acf_block): ?>
        <div class="news_feeds_link_wrapper">
            <div class="add-email source-input">
                <div class="input-form main-form">
                    <div class="remove-source text-end">
                        <button class="btn btn-dashboard remove-source-form"><i class="fa-solid fa-trash-can"></i></button>
                    </div>
                    <div class="mb-3">
                        <label for="newsFeedLink" class="form-label">
                            <?php echo $url_label; ?>
                        </label>
                        <input type="email" class="form-control" name="newsFeedLink[]" aria-describedby="emailHelp"
                            placeholder="Enter URL">
                        <div id="emailHelp" class="form-text">
                            <?php echo $url_placeholder; ?>
                        </div>
                    </div>
                </div>
            </div>
            <!-- <div class="btn-module mb-5">
                <a class="btn btn-dashboard add-feed-button" type="button">
                    <?php echo $button_text_News; ?>
                </a>
            </div> -->
        </div>
    <?php endif; ?>





    <!-- Code For Adding Source Block -->
    <?php
    $source_heading = get_field('source_heading');
    $source_title = get_field('source_title');
    $source_text = get_field('source_text');
    $source_url = get_field('source_url');
    $button_text = get_field('button_text');
    $hide_source_url_block = get_field('hide_source_url_block');
    $source_maximum_url = get_field('source_maximum_url');
    echo '<script>';
    echo 'var maxSourceNumberUrl = '.json_encode($source_maximum_url).';';
    echo '</script>';
    ?>
    <?php if(!$hide_source_url_block): ?>
        <div class="source_link_wrapper">
            <div class="add-url-source add-source-input">
                <div class="input-form add-source-main-form">
                    <div class="my-2">
                        <div class="block-header d-flex align-items-center justify-content-between">
                            <h4 class="block-head mb-0">
                                <?php echo $source_heading; ?>
                            </h4>
                            <div class="remove-source">
                                <button class="btn  btn-dashboard remove-source-form"><i
                                        class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                        </div>
                        <div class="row">
                        <p>
                        Optional: Copy and paste the source text in parts or the full article for more accurate results.
                        </p>
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
                            aria-describedby="emailHelp" placeholder="Enter Source URL">
                    </div>
                </div>
            </div>
            <div class="btn-module mb-5">
                <a href="" class="btn btn-dashboard add-source-button" type="button">
                    <?php echo $button_text; ?>
                </a>
            </div>
        </div>
    <?php endif; ?>


<div class="add-prompt mt-5">
                    <h4 class="block-head">
                        Subject
                    </h4>
                    <div class="input-form mt-3">
                        <div class="mb-3">
                           <input type="text" class="form-control" placeholder='eg. Write a story about "Alzheimer"s Disease"' name="subject" />
                        </div>
                    </div>
                </div>


<div class="add-prompt mt-5">
                    <h4 class="block-head">
                        Questions
                    </h4>
                    <div class="input-form mt-3">
                        <div class="mb-3">
                           <textarea class="form-control border-0" name="questions" id="questions" cols="30"
                                rows="7" placeholder='eg.
1. "What is Alzheimer"s Disease"
2. "What are the symptoms of Alzheimer"s Disease"
3. "What are the treatments for Alzheimer"s Disease"
4. "Why are blood tests useful for diagnosing Alzheimer"s"
5. "What are the drawbacks of using blood tests to diagnose Alzheimer"s"'></textarea>
                        </div>
                    </div>
                </div>

    <!-- Adding Prompt Block -->
    <?php if(current_user_can('administrator')) { ?>
        <?php
        global $chat_gpt_rephraser;

        $prompt_heading = get_field("prompt_heading");
        $prompt_textarea = $chat_gpt_rephraser->get_prompt();

        $hide_prompt_block = get_field("hide_prompt_block");
        ?>
        <?php if(!$hide_prompt_block): ?>
            <div class="hide_prompt_block">
                <div class="add-prompt mt-5">
                    <h4 class="block-head">
                        <?php echo $prompt_heading ?>
                    </h4>
                    <div class="input-form mt-3">
                        <div class="mb-3">
                            <textarea class="form-control border-0" name="prompt_textarea" id="prompt_textarea" cols="30"
                                rows="7"><?php echo $prompt_textarea ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    <?php } ?>

    <?php

        global $chat_gpt_rephraser;

        $prompt_heading = get_field("prompt_heading");
        $prompt_textarea = $chat_gpt_rephraser->get_prompt();

        $hide_prompt_block = get_field("hide_prompt_block");
        ?>

    <div class="ai-selector">
        <h4 class="block-head mb-0">
            <?php echo "GPT Model" ?>
        </h4>
        <div class="selector-dashboard mt-3">
            <div class="mb-3">
                <?php
                $gpt_dropdown = $chat_gpt_rephraser->get_gpt_models(); ?>
            </div>
        </div>
    </div>



    <!-- Adding Submit Button -->
    <?php
    $form_submission_button_text = get_field("form_submission_button_text");
    ?>
    <div class="btn-module mb-5">
        <a class="btn btn-dashboard" id="submit-button" type="button">
            <?php echo $form_submission_button_text ?>
        </a>
    </div>

</form>


<div class="element-wrapper d-none">
    <?php
    $content = ''; // You can provide initial content here

    // Editor settings
    $editor_id = 'p_response';
    $settings = array(
        'media_buttons' => false,
        'textarea_name' => 'my_custom_editor',
        'editor_height' => 500,
    );

    // Display the editor
    wp_editor($content, $editor_id, $settings);
    ?>
    <div class="d-flex flex-wrap mt-3">
        <div class="btn-module me-3 mb-2">
            <button class="btn btn-dashboard" id="save-button" type="button" onclick="create_gpt_post()">SAVE
            </button>
        </div>



        <div class="btn-module me-3 mb-2">
            <a class="btn btn-dashboard" id="View-button" type="button">
                VIEW
            </a>
        </div>
        <div class="btn-module me-3 mb-2">
            <button class="btn btn-dashboard" id="sndtoblox-button" type="button" disabled>
                SEND TO BLOX
            </button>
        </div>
        <input type="hidden" name="custom_post_id" id="custom_post_id" />
        <div class="btn-module me-3 mb-2">
            <button type="button" class="btn btn-dashboard" data-bs-toggle="modal" data-bs-target="#comparison_modal"
                id="compare-button" disabled>COMPARE</button>

            <!-- Modal -->
            <div class="modal fade" id="comparison_modal" tabindex="-1" aria-labelledby="comparison_modalLabel"
                aria-hidden="true">
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
    </div>
</div>
</div>
</div>
</div>
</div>

<script src="<?php echo get_template_directory_uri()."/blocks-templates/wrapper-block-template/wrapper-block-template.js" ?>"></script>

<script>

    var view_url = '';
    document.addEventListener('DOMContentLoaded', function () {
        // Attach a click event listener to the element with the ID 'overlayBtn'
        var overlayBtn = document.getElementById('submit-button');
        var elementWrapper = document.querySelector('.element-wrapper');
        var loader = document.getElementById('preloader');

        if (overlayBtn) {
            overlayBtn.addEventListener('click', function (event) {

                event.preventDefault();

                loader.classList.add('d-block');

                var formdata = new FormData(document.getElementById("custom-form"));
                formdata.append("action", "url_link_parse");
                var editorId = 'p_response';

                jQuery.ajax({
                    type: "POST",
                    url: ajaxurl,
                    data: formdata,
                    contentType: false,
                    processData: false,
                    cache: false,
                    dataType: 'json',
                    success: function (data) {
                        console.log(data)
                        if (data.status_Code == 200) {
                            elementWrapper.classList.remove('d-none');
                            elementWrapper.classList.add('d-block');
                            var editor = tinymce.get(editorId);
                            editor.setContent('');
                            editor.insertContent(data.content_array);
                            jQuery("#custom_post_id").val(data.custom_post_id);
                        }
                        else if (data.status_Code == 503) {
                            Swal.fire({
                                title: 'Empty Fields',
                                text: 'Required Fields are empty.',
                                icon: 'warning', // success, error, warning, info, question
                                confirmButtonText: 'OK'
                            });
                        }
                        else if (data.status_Code == 404) {
                            Swal.fire({
                                title: 'Error',
                                text: 'Error with GPT response',
                                icon: 'error', // success, error, warning, info, question
                                confirmButtonText: 'OK'
                            });
                        }
                        else if (data.status_Code == 505) {
                            Swal.fire({
                                title: 'Error',
                                text: 'The Feed links contain a duplicate link',
                                icon: 'info', // success, error, warning, info, question
                                confirmButtonText: 'OK'
                            });
                        }
                    },
                    error: function (error) {
                        // Handle errors
                        console.log(error);
                    },
                    complete: function () {
                        // Hide the loader when the process is complete (whether success or error)
                        loader.classList.remove('d-block');
                    }
                });
            });
            jQuery('#submit-button').prop('disabled', true);
            // document.getElementById('save-button').removeAttribute('disabled');
        }
    });



    jQuery(document).ready(function () {
        // This code runs when the document is fully loaded and ready.

        jQuery(".btn-instruction").click(function () {
            // When an element with class "btn-instruction" is clicked.

            jQuery(".instructions-list").toggleClass('active');
            // Toggle the class 'active' on elements with class "instructions-list".
        });
    });

    function initializeAddSourceFormFunction() {
        let formCount = 1;
        // Apply the click event handler to the "Add source" button
        jQuery('.add-source-button').click(function (event) {
            event.preventDefault();
            if (formCount < maxSourceNumberUrl) {
                // Clone the source form fields
                const wrapperElement = jQuery('.add-source-input');
                const originalForm = wrapperElement.find('.add-source-main-form:last');
                const clonedFields = originalForm.clone();
                // Add a unique identifier to the cloned form
                clonedFields.addClass('cloned-form-' + formCount);

                // Show the close button for the cloned form
                clonedFields.find('.remove-source-form').show();

                // Clear the input fields in the cloned form
                clonedFields.find('input[type="text"]').val('');
                clonedFields.find('textarea').val('');
                clonedFields.find('input[type="email"]').val('');

                // Append the cloned form fields below the original form
                wrapperElement.append(clonedFields);
                formCount++;

                if (formCount >= maxSourceNumberUrl) {
                    jQuery('.add-source-button').hide();
                }
            }
        });

        // Apply the click event handler to the "Remove" button directly
        jQuery('.add-source-input').on('click', '.remove-source-form', function () {
            if (formCount > 1) {
                jQuery(this).closest('.add-source-main-form').remove();
                formCount--; // Decrement the form count
            }

            jQuery('.add-source-button').show();
        });

        // Initially hide the close button
        jQuery('.remove-source-form').hide();
    }

    // Call the function to initialize the form functionality
    initializeAddSourceFormFunction();


    function initializeUseDifferentSourceFunction() {
        let formCount = 1;
        const maxFormCount = maxNumberUrl; // Set the maximum form count

        // Apply the click event handler to the "Add News Feed" button
        jQuery('.add-feed-button').click(function (event) {
            event.preventDefault();

            if (formCount < maxFormCount) {
                // Clone the form up to the maxFormCount times
                const wrapperElement = document.querySelector('.source-input');
                const originalForm = wrapperElement.querySelector('.main-form');
                const clonedForm = originalForm.cloneNode(true);

                // Add a unique identifier to the cloned form
                clonedForm.classList.add('cloned-form-' + formCount);

                // Show the close button for the cloned form
                jQuery(clonedForm).find('.remove-source-form').show();

                // Clear the input field in the cloned form
                const emailInput = clonedForm.querySelector('input[type="email"]');
                emailInput.value = '';

                // Append the cloned form below the original form
                wrapperElement.append(clonedForm);

                formCount++; // Increment the form count

                // Check if the maximum form count has been reached
                if (formCount >= maxFormCount) {
                    // Hide the "Add News Feed" button
                    jQuery('.add-feed-button').hide();
                }
            }
        });

        // Use event delegation for dynamically added "close" buttons
        jQuery('.source-input').on('click', '.remove-source-form', function () {
            if (formCount > 1) {
                jQuery(this).closest('.main-form').remove();
                formCount--; // Decrement the form count
            }

            // Show the "Add News Feed" button after removing a form
            jQuery('.add-feed-button').show();
        });

        // Initially hide the close button
        jQuery('.remove-source-form').hide();
    }
    // Call the function to initialize the form functionality
    initializeUseDifferentSourceFunction();




    function create_gpt_post() {

        var editorId = 'p_response';
        var editor = tinymce.get(editorId);
        var content_array = editor.getContent();
        var status = 0;
        var c_id = jQuery("#custom_post_id").val();
        var prompt = jQuery("#prompt_textarea").val();

        jQuery.ajax({
            type: "POST",
            url: ajaxurl,
            data: {
                action: "save_gpt_post",
                c_id: c_id,
                content_array: content_array,
                prompt: prompt,
                taxanomy: "From Custom Post",
            },
            async: false,
            success: function (data) {
                status = data.status_Code
                view_url = data.gpt_post_permalink
                jQuery("#View-button").attr({
                    "target": "_blank",
                    "href": data.gpt_post_permalink
                });
                jQuery("#sndtoblox-button").on("click", function () {
                    create_wp_post_blox(data.gpt_post_id);
                });
                jQuery("#comparison-button").on("click", function () {
                    compare_orginal_gpt_content(data.gpt_post_id)
                });

            }
        });
        if (status == 200) {
            Swal.fire({
                title: 'Success',
                text: 'Post Saved Successfully.',
                icon: 'success', // success, error, warning, info, question
                confirmButtonText: 'OK'
            });

            // jQuery('#save-button').prop('disabled', true);
            // jQuery('#View-button').css('pointer-events', 'auto');
            jQuery('#View-button').css('opacity', '1');
            jQuery('#sndtoblox-button').prop('disabled', false);
            jQuery('#compare-button').prop('disabled', false);
            jQuery('#comparison-button').show();
            jQuery('#compare-button').show();
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
            success: function (data) {
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
            jQuery('#sndtoblox-button').prop('disabled', true);
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
        // jQuery('#comparison-button').css('background-color', 'green').html('Generating Comparison...');
        jQuery.ajax({
            type: "POST",
            url: ajaxurl,
            data: {
                action: "output_comparison",
                gpt_post_id: gpt_post_id,
            },
            beforeSend: function () {
                showPreloader();
            },
            success: function (data) {
                status = data.status_Code;
            },
            complete: function () {
                if (status == 200) {
                    // jQuery('#comparison-button').html('Comparison Successful');
                    jQuery('#comparison-button').hide();
                    jQuery('#compare-button').hide();
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



</script>
<div id="preloader"></div>
<style>
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