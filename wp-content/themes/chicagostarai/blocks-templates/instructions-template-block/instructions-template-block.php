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
<script
    src="<?php echo get_template_directory_uri() . "/blocks-templates/instructions-template-block/instructions-template-block.js" ?>"></script>