<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<form id="custom-form">


<?php

$instruction_module_heading = get_field("instruction_module_heading");
$instruction_module_button_text = get_field("instruction_module_button_text");
$instruction_repeater_field_flow_one = get_field("instruction_repeater_field_flow_one");
$seperator = get_field("seperator");
$flow_two_repeater = get_field("flow_two_repeater");
$note_for_instruction = get_field("note_for_instruction");


?>
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
        if (have_rows('instruction_repeater_field_flow_one')) {
            // Loop through the repeater rows
            $step_number = 1; // Initialize the step number
        
            while (have_rows('instruction_repeater_field_flow_one')) {
                the_row();

                // Get the content for the current step
                $step_content = get_sub_field('flow_one_content');

                // Output the step with its content
                echo '<ul class="flow-1">';
                echo '<li>';
                echo '<p><span class="fw-bold">Step ' . $step_number . ' : </span>' . $step_content . '</p>';
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
        if (have_rows('flow_two_repeater')) {
            // Loop through the repeater rows
            $step_number = 1; // Initialize the step number
        
            while (have_rows('flow_two_repeater')) {
                the_row();

                // Get the content for the current step
                $step_content = get_sub_field('flow_two_content');

                // Output the step with its content
                echo '<ul class="flow-2">';
                echo '<li>';
                echo '<p><span class="fw-bold">Step ' . $step_number . ' : </span>' . $step_content . '</p>';
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



<!-- Code For Adding Url  -->
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




<!-- Code For Adding Source Block -->
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


<!-- Adding Prompt Block -->

<?php

// my_custom_function(); // Call the function to update the content
// chat_gpt_model_fetch();

$chat_gpt_rephraser = new ChatGptRephraser();

$prompt_heading = get_field("prompt_heading");
// $prompt_textarea = $chat_gpt_rephraser->get_prompt(); // Fetch the dynamically generated content
// $gpt_dropdown = get_field("gpt_dropdown");
$prompt_textarea = $chat_gpt_rephraser->get_prompt();


// print_r($gpt_dropdown);exit;
?>

<div class="add-prompt mt-5">
    <h4 class="block-head">
        <?php echo $prompt_heading ?>
    </h4>
    <div class="input-form mt-3">
        <div class="mb-3">
            <textarea class="form-control border-0" name="" id="" cols="30" rows="7"><?php echo $prompt_textarea ?></textarea>
        </div>
    </div>
</div>

<div class="ai-selector">
    <div class="selector-dashboard mt-3">
        <div class="mb-3">
            <?php
        $gpt_dropdown = $chat_gpt_rephraser->get_gpt_models();?>
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
                        // The content you want to edit
                        $content = ''; // You can provide initial content here

                        // Editor settings
                        $editor_id = 'my_wysiwyg_editor'; // A unique ID for the editor
                        $settings = array(
                            'media_buttons' => true, // Show media upload buttons
                            'textarea_name' => 'my_custom_editor', // Name attribute for the textarea
                            'textarea_rows' => 10, // Number of rows in the textarea
                        );

                        // Display the editor
                        wp_editor($content, $editor_id, $settings);
                        ?>
                        <div class="d-flex mt-3" style="gap: 10px;">
                            <div class="btn-module mb-5">
                                <a class="btn btn-dashboard" id="save-button" type="button">
                                    SAVE
                                </a>
                            </div>

                            <div class="btn-module mb-5">
                                <a class="btn btn-dashboard" id="View-button" type="button">
                                    VIEW
                                </a>
                            </div>

                            <div class="btn-module mb-5">
                                <a class="btn btn-dashboard" id="sndtoblox-button" type="button">
                                    SEND TO BLOX
                                </a>
                            </div>

                            <div class="btn-module mb-5">
                                <a class="btn btn-dashboard" id="compare-button" type="button">
                                    COMPARE
                                </a>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Attach a click event listener to the element with the ID 'overlayBtn'
        var overlayBtn = document.getElementById('submit-button');
        var elementWrapper = document.querySelector('.element-wrapper');
        if (overlayBtn) {
            overlayBtn.addEventListener('click', function(event) {
                event.preventDefault(); // Prevent default form submission
                // Create a FormData object from the 'custom-form' form
                elementWrapper.classList.remove('d-none');
                elementWrapper.classList.add('d-block');
                var formdata = new FormData(document.getElementById("custom-form"));

                // Add an 'action' parameter to the form data
                formdata.append("action", "url_link_parse");

                // Make an AJAX POST request using jQuery
                jQuery.ajax({
                    type: "POST",
                    url: ajaxurl,
                    data: formdata,
                    contentType: false,
                    processData: false,
                    cache: false,
                    success: function(data) {
                        console.log(data);
                    }
                });
            });
        }
    });
</script>

</body>
</html>


<script
    src="<?php echo get_template_directory_uri() . "/blocks-templates/wrapper-block-template/wrapper-block-template.js" ?>"></script>

