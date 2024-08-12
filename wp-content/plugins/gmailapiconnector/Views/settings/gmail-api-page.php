<div class="container">
    <h1>GMAIL API Credentials</h1>

    <!-- Add a container for displaying status messages -->
    <div id="status-container"></div>

    <div class="card guide-container">
        <h4>Guide: Setting Up Gmail API Connector</h4>
        <ol>
            <li><b>Label Setup:</b> In your Gmail Inbox, add the appropriate label for automated emails to prevent errors.</li>
            <li><b>Gmail API Console:</b> Open the Gmail API developer console.</li>
            <li><b>Copy Credentials:</b> Manually copy the credentials from the console.</li>
            <li><b>Add Credentials:</b> Paste the copied credentials on the settings page.</li>
            <li><b>Enable Authorization:</b> Fill out all required credentials to enable the authorize button.</li>
            <li><b>Authorize Connection:</b> Click the authorize button to generate a token for authentication.</li>
        </ol>
    </div>

    <div class="form-container">
        <form method="POST" action="options.php">
            <?php settings_fields('gmail_api_settings'); ?>
            <?php do_settings_sections('gmail_api_settings'); ?>

            <?php
            // Check if all required fields are filled to enable/disable the authorize button
            if (
                !empty($apiSettings['client_id']) &&
                !empty($apiSettings['project_id']) &&
                !empty($apiSettings['auth_uri']) &&
                !empty($apiSettings['token_uri']) &&
                !empty($apiSettings['auth_provider_x509_cert_url']) &&
                !empty($apiSettings['client_secret']) &&
                !empty($apiSettings['redirect_uris']) &&
                !empty($apiSettings['label_id']) &&
                !empty($apiSettings['search_query']) &&
                !empty($apiSettings['assign_labels_to_fetched_emails'])
            ) {
                $authorizeButtonDisabled = true;
            } else {
                $authorizeButtonDisabled = false;
            }
            ?>

            <div class="button-container">
                <button class="button button-primary" id="save-api-creds">Save</button>

                <!-- Disable the authorize button if required fields are not filled -->
                <a href="<?php echo $authUrl; ?>" class="button button-secondary" id="authorize-gmail-btn" <?php echo $authorizeButtonDisabled ? '' : 'disabled'; ?>>Authorize Gmail Connection</a>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var authCode = '<?php echo isset($_GET['code']) ? $_GET['code'] : null; ?>';

        <?php
        if ($authorizeButtonDisabled) {
            // Call a JavaScript function to handle the authorization logic
            echo "authorizeGmail('$authCode');";
        }
        ?>
        
        // Call a JavaScript function to handle the authorization logic
        // authorizeGmail(authCode);

        // JavaScript function to handle the Gmail authorization logic
        function authorizeGmail(authCode) {
            // Add nonce to the AJAX data
            var data = {
                action: 'authorize_gmail',
                security_nonce: myplugin_vars.security_nonce,
                gmail_auth_code: authCode
            };

            // Call the create_access_token function using AJAX
            jQuery.ajax({
                url: myplugin_vars.ajax_url,
                type: 'POST',
                data: data,
                success: function(response) {

                    // Display the status message on the settings page
                    var statusContainer = document.getElementById('status-container');
                    statusContainer.innerHTML = '<p><strong>Status:</strong> ' + response.data.message + '</p>';

                    // Enable the authorize button after successful authorization
                    var authorizeBtn = document.getElementById('authorize-gmail-btn');
                    authorizeBtn.removeAttribute('disabled');
                },
                error: function(error) {
                    console.error(error);
                },
            });
        }
        
    });
</script>