<div id="output-tab">
    <div class="card">
        <h4>Guide</h4>
        <ul>
            <li><b>Step 1:</b> As per your input in the previous tab this is the compiled prompt</li>
        </ul>
    </div>

    <div class="form-container">
        <?php settings_fields('gpt_api_prompt_settings_output'); ?>
        <?php do_settings_sections('gpt_api_prompt_settings_output'); ?>
    </div>
</div>
