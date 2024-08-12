<style>
    .delete-field {
        margin-left: 20px !important;
    }

    .button-container {
        margin-top: 20px !important;
    }
</style>

<div class="container">
    <h1>RSS Feed Settings</h1>

    <div class="card">
        <h4>Guide</h4>
        <ul>
            <li><b>Step 1:</b> Locate the RSS Feed Links.</li>
            <li><b>Step 2:</b> Paste RSS Feed Links in the designated input field.</li>
            <li><b>Step 3:</b> Add Additional Fields for Multiple RSS Feeds.</li>
            <li><b>Step 4:</b> Save RSS Feed Links.</li>
            <li><b>Step 5:</b> Access Saved RSS Feed Links.</li>
        </ul>
    </div>

    <div class="form-container">
        <form method="POST" action="options.php">

            <div class="button-container">
                <input type="button" class="button-secondary" id="add-field" value="Add Field">
                <button class="button button-primary" id="save-feeds">Save Feeds</button>
            </div>

            <?php settings_fields('rss_feeds_settings'); ?>
            <?php do_settings_sections('rss_feeds_settings'); ?>

            <?php if (!empty($rssLinks) && is_array($rssLinks)) : ?>
                <table class="input-table form-table" role="presentation">
                    <tbody>
                        <?php foreach ($rssLinks as $index => $url) : ?>
                            <?php $index = $index+1; ?>
                            <tr>
                                <th scope="row">RSS Feed URL</th>
                                <td> 
                                    <input type="text" class="rss-feed-url" name="rss_feed_urls[<?php echo esc_attr($index); ?>]" value="<?php echo esc_attr($url['rss_feed_link']); ?>" />
                                </td>
                                <th scope="row">Max Allowed Items</th>
                                <td> 
                                    <input type="number" class="rss-feed-max" name="rss_feed_max[<?php echo esc_attr($index); ?>]" value="<?php echo esc_attr($url['max_allowed_items']); ?>" />
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
    var counter = document.querySelectorAll('.rss-feed-url').length;

    document.querySelector('#add-field').addEventListener('click', function (e) {
        // Create a new table row
        const newRow = document.createElement('tr');

        // Create the table header (th) element for RSS Feed URL
        const urlHeader = document.createElement('th');
        urlHeader.setAttribute('scope', 'row');
        urlHeader.textContent = 'RSS Feed URL';

        // Create the table data (td) element for the input field of RSS Feed URL
        const urlData = document.createElement('td');

        // Create the input element for RSS Feed URL
        const urlInput = document.createElement('input');
        urlInput.type = 'text';
        urlInput.name = `rss_feed_urls[${counter}]`;

        // Create the table header (th) element for Max Allowed Items
        const maxItemsHeader = document.createElement('th');
        maxItemsHeader.setAttribute('scope', 'row');
        maxItemsHeader.textContent = 'Max Allowed Items';

        // Create the table data (td) element for the input field of Max Allowed Items
        const maxItemsData = document.createElement('td');

        // Create the input element for Max Allowed Items
        const maxItemsInput = document.createElement('input');
        maxItemsInput.type = 'number';
        maxItemsInput.name = `rss_feed_max[${counter}]`;

        // Create the "Remove" button
        const removeButton = document.createElement('input');
        removeButton.type = 'button';
        removeButton.classList.add('button', 'button-danger', 'delete-field');
        removeButton.value = 'Remove';

        // Append the input and button to the respective table data
        urlData.appendChild(urlInput);
        maxItemsData.appendChild(maxItemsInput);
        maxItemsData.appendChild(removeButton);

        // Append the headers and table data to the table row
        newRow.appendChild(urlHeader);
        newRow.appendChild(urlData);
        newRow.appendChild(maxItemsHeader);
        newRow.appendChild(maxItemsData);

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
