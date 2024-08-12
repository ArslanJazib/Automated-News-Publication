<div id="placeholders-tab">
    <div class="card">
        <h4>Guide</h4>
        <ul>
            <li><b>Step 1:</b> Add unique placeholders that would be given in prompt to segerate contnet.</li>
            <li><b>Step 2:</b> Placeholder should start with '[' and end with ']'.</li>
            <li><b>Step 3:</b> All letters in the placeholder should be capital and without special characters.</li>
            <li><b>Step 4:</b> Add instruction for the prompt associated with the placeholder in the adjacent text area e.g. The title will be indicated with [TITLE].</li>
            <li><b>Step 5:</b> Click the Add Placeholder button to add more placeholders with their repective instructions.</li>
            <li><b>Step 6:</b> Click the remove button to remove placeholder.</li>
            <li><b>Step 7:</b> Press the save button.</li>
        </ul>
    </div>

    <div class="form-container">
        <form method="POST" action="options.php">

            <div class="button-container">
                <input type="button" class="button-secondary" id="add-placeholder" value="Add Placeholder">
                <button class="button button-primary" id="save-placeholders">Save Placeholders</button>
            </div>

            <?php settings_fields('gpt_api_prompt_settings_placeholder'); ?>
            <?php do_settings_sections('gpt_api_prompt_settings_placeholder'); ?>

            <?php if (!empty($placeholders) && is_array($placeholders)) : ?>
                <table class="input-table form-table" role="presentation">
                    <tbody>
                        <?php foreach ($placeholders as $index => $placeholder) : ?>
                            <?php $index = $index+1; ?>
                            <tr>
                                <th scope="row">Placeholder Instructions</th>
                                <td> 
                                    <textarea class="gpt-prompt-instructions" name="gpt_prompt_instructions[<?php echo esc_attr($index); ?>]" rows="4" cols="50"> <?php echo esc_attr($placeholder['all_instructions']); ?> </textarea>
                                </td>
                                <th scope="row">Prompt Placeholder</th>
                                <td> 
                                    <input type="text" class="gpt-prompt-placeholder" name="gpt_prompt_placeholders[<?php echo esc_attr($index); ?>]" value="<?php echo esc_attr($placeholder['placeholder']); ?>" />
                                </td>
                                <td> 
                                    <button class="button button-danger delete-field">Remove</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

        </form>

    </div>
</div>

<script>
    var counter = document.querySelectorAll('.gpt-prompt-placeholder').length;

    document.querySelector('#add-placeholder').addEventListener('click', function (e) {
        // Create a new table row
        const newRow = document.createElement('tr');

        // Create the table header (th) element for Prompt Placeholder
        const placeholderHeader = document.createElement('th');
        placeholderHeader.setAttribute('scope', 'row');
        placeholderHeader.textContent = 'Prompt Placeholder';

        // Create the table data (td) element for the input field of Prompt Placeholder
        const placeholderData = document.createElement('td');

        // Create the input element for Prompt Placeholder
        const placeholderInput = document.createElement('input');
        placeholderInput.type = 'text';
        placeholderInput.name = `gpt_prompt_placeholders[${counter}]`;

        // Append the input to the respective table data
        placeholderData.appendChild(placeholderInput);

        // Create the table header (th) element for Placeholder Instructions
        const instructionsHeader = document.createElement('th');
        instructionsHeader.setAttribute('scope', 'row');
        instructionsHeader.textContent = 'Placeholder Instructions';

        // Create the table data (td) element for the textarea of Placeholder Instructions
        const instructionsData = document.createElement('td');

        // Create the textarea element for Placeholder Instructions
        const instructionsTextarea = document.createElement('textarea');
        instructionsTextarea.className = 'gpt-prompt-instructions';
        instructionsTextarea.name = `gpt_prompt_instructions[${counter}]`;
        instructionsTextarea.rows = 4;
        instructionsTextarea.cols = 50;

        // Append the textarea to the respective table data
        instructionsData.appendChild(instructionsTextarea);

        // Create the table data (td) element for the "Remove" button
        const removeButtonData = document.createElement('td');

        // Create the "Remove" button
        const removeButton = document.createElement('button');
        removeButton.className = 'button button-danger delete-field';
        removeButton.textContent = 'Remove';

        // Append the "Remove" button to the respective table data
        removeButtonData.appendChild(removeButton);

        // Append the headers and table data to the table row
        newRow.appendChild(instructionsHeader);
        newRow.appendChild(instructionsData);
        newRow.appendChild(placeholderHeader);
        newRow.appendChild(placeholderData);
        newRow.appendChild(removeButtonData);

        // Get the table body
        const tableBody = document.querySelector('.input-table tbody');

        // Insert the new row after the last row in the table body
        tableBody.appendChild(newRow);

        // Increment the counter for the next input
        counter++;
    });

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('delete-field')) {
            // Find the closest row (table row) to the clicked "Remove" button
            const rowToRemove = e.target.closest('tr');

            // Check if a row was found
            if (rowToRemove) {
                // Remove the found row from the table
                rowToRemove.remove();
            }
        }
    });
</script>
