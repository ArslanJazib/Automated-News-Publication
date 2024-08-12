<div id="format-tab">
    <div class="card">
        <h4>Guide</h4>
        <ul>
            <li><b>Step 1:</b> Decribe how the final ouput should be presented</li>
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
                <input type="button" class="button-secondary" id="add-format" value="Add Format">
                <button class="button button-primary" id="save-formats">Save Fromats</button>
            </div>

            <?php settings_fields('gpt_api_prompt_settings_format'); ?>
            <?php do_settings_sections('gpt_api_prompt_settings_format'); ?>

            <?php if (!empty($formats) && is_array($formats)) : ?>
                <table class="input-table form-table" role="presentation">
                    <tbody>
                        <?php foreach ($formats as $index => $format) : ?>
                            <?php $index = $index+1; ?>
                            <tr>
                                <th scope="row">Output Format Rule</th>
                                <td> 
                                    <textarea class="gpt-prompt-formats" name="gpt_prompt_formats[<?php echo esc_attr($index); ?>]" rows="4" cols="100"> <?php echo esc_attr($format['format']); ?> </textarea>
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
    var counter = document.querySelectorAll('.gpt-prompt-formats').length;

    document.querySelector('#add-format').addEventListener('click', function (e) {
        // Create a new table row
        const newRow = document.createElement('tr');

        // Create the table header (th) element for Output Formats
        const formatsHeader = document.createElement('th');
        formatsHeader.setAttribute('scope', 'row');
        formatsHeader.textContent = 'Output Format Rule';

        // Create the table data (td) element for the textarea of Output Formats
        const formatsData = document.createElement('td');

        // Create the textarea element for Output Formats
        const formatsTextarea = document.createElement('textarea');
        formatsTextarea.className = 'gpt-prompt-formats';
        formatsTextarea.name = `gpt_prompt_formats[${counter}]`;
        formatsTextarea.rows = 4;
        formatsTextarea.cols = 50;

        // Append the textarea to the respective table data
        formatsData.appendChild(formatsTextarea);

        // Create the table data (td) element for the "Remove" button
        const removeButtonData = document.createElement('td');

        // Create the "Remove" button
        const removeButton = document.createElement('button');
        removeButton.className = 'button button-danger delete-field';
        removeButton.textContent = 'Remove';

        // Append the "Remove" button to the respective table data
        removeButtonData.appendChild(removeButton);

        // Append the headers and table data to the table row
        newRow.appendChild(formatsHeader);
        newRow.appendChild(formatsData);
        newRow.appendChild(removeButtonData);

        // Get the table body
        const tableBody = document.querySelector('#format-tab .input-table tbody');

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
