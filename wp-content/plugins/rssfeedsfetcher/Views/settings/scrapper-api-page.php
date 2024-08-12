<div class="container">
    <h1>Scrapper API Credentials</h1>

    <div class="card">
        <h4>Guide</h4>
        <ul>
            <li><b>Step 1:</b> Add the API url</li>
            <li><b>Step 2:</b> Add the API key</li>
        </ul>
    </div>

    <div class="form-container">
        <form method="POST" action="options.php">

            <?php settings_fields('scrapper_api_settings'); ?>
            <?php do_settings_sections('scrapper_api_settings'); ?>

            <div class="button-container">
                <button class="button button-primary" id="save-api-creds">Save</button>
            </div>

        </form>

    </div>
</div>