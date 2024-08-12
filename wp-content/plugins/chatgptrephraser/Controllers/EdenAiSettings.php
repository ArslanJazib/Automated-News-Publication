<?php

namespace GPT\Controllers;

use GPT\Models\EdenAiModel;

class EdenAiSettings {
    private $apiSettingsTable;
    private $modelSettingsTable;

    public function __construct() {
        $this->apiSettingsTable = new EdenAiModel('eden_api_settings');

        // Initialize your Eden API settings controller
        add_action('admin_init', array($this, 'register_settings'));
    }

    public function add_menu() {
        // Create an admin menu item for API settings
        add_menu_page('Eden API Settings', 'Eden API Settings', 'manage_options', 'eden-api-settings', array($this, 'render_settings_page'));
    }

    public function render_settings_page() {
        // Data to be passed to the view
        $apiSettings = $this->apiSettingsTable->get_api_settings();

        // Load the view template for Eden API settings
        include(dirname(plugin_dir_path(__FILE__)) . '/Views/settings/eden-api-page.php');
    }

    public function register_settings() {
        // Register Eden API settings
        register_setting('eden_api_settings', 'eden_api_settings', array($this, 'store_eden_api_settings'));

        add_settings_section('eden_api_section', 'Eden API Settings', array($this, 'eden_api_section_callback'), 'eden_api_settings');

        add_settings_field('eden_api_url', 'API URL', array($this, 'eden_api_url_callback'), 'eden_api_settings', 'eden_api_section');
        add_settings_field('eden_api_key', 'API Key', array($this, 'eden_api_key_callback'), 'eden_api_settings', 'eden_api_section');
    }

    public function store_eden_api_settings() {
        // Get API URL and API Key from the input array
        $api_url = isset($_POST['eden_api_url']) ? sanitize_text_field($_POST['eden_api_url']) : null;
        $api_key = isset($_POST['eden_api_key']) ? sanitize_text_field($_POST['eden_api_key']) : null;
        // Update the database record
        $this->apiSettingsTable->create_record($api_url, $api_key);
    }

    public function eden_api_section_callback() {
        echo '';
    }

    // Callback to render the API URL input field
    public function eden_api_url_callback() {
        $data = $this->apiSettingsTable->get_api_url();
        if (is_array($data) && isset($data['api_url'])) {
            $apiUrl = $data['api_url'];
        } else {
            $apiUrl = "";
        }
        include(dirname(plugin_dir_path(__FILE__)) . '/Views/settings/eden-api-url.php');
    }

    // Callback to render the API Key input field
    public function eden_api_key_callback() {
        $data = $this->apiSettingsTable->get_api_key();
        if (is_array($data) && isset($data['api_key'])) {
            $apiKey = $data['api_key'];
        } else {
            $apiKey = "";
        }
        include(dirname(plugin_dir_path(__FILE__)) . '/Views/settings/eden-api-key.php');
    }


}