<?php


global $chat_gpt_rephraser;

$prompt_heading = get_field("prompt_heading");
$prompt_textarea = $chat_gpt_rephraser->get_prompt();


?>

<div class="add-prompt mt-5">
    <h4 class="block-head">
        <?php echo $prompt_heading ?>
    </h4>
    <div class="input-form mt-3">
        <div class="mb-3">
            <textarea class="form-control border-0" name="prompt_textarea" id="prompt_textarea" cols="30" rows="7"><?php echo $prompt_textarea ?></textarea>
        </div>
    </div>
</div>

<div class="ai-selector">
    <div class="selector-dashboard mt-3">
        <div class="mb-3">
            <?php
            $gpt_dropdown = $chat_gpt_rephraser->get_gpt_models(); ?>
        </div>
    </div>
</div>