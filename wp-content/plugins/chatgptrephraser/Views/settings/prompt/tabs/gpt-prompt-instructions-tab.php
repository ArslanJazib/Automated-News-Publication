<div id="instructions-tab">
    <div class="card">
        <h4>Guide</h4>
        <ul>
            <li><b>Step 1:</b> Write instructions on what you want in the rephrased content</li>
            <li><b>Step 2:</b> Use the pre defined placeholders in your input below</li>
            <li><b>Available Placeholders:</b>
            <?php if (!empty($placeholders) && is_array($placeholders)) : ?>
                        <?php foreach ($placeholders as $index => $placeholder) : ?>
                                <?php echo esc_attr($placeholder['placeholder']); ?>
                        <?php endforeach; ?>
            <?php endif; ?>
            </li>
        </ul>
    </div>

    <div class="form-container">
        <form method="POST" action="options.php">

            <div class="button-container">
                <button class="button button-primary" id="save-instructions">Save Instructions</button>
            </div>

            <?php settings_fields('gpt_api_prompt_settings_instructions'); ?>
            <?php do_settings_sections('gpt_api_prompt_settings_instructions'); ?>

        </form>

    </div>
</div>