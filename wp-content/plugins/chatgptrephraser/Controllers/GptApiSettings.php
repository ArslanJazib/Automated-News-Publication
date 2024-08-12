<?php

namespace GPT\Controllers;

use GPT\Models\GptApiSetting;
use GPT\Models\GptApiModelSetting;

class GptApiSettings {
    private $apiSettingsTable;
    private $modelSettingsTable;

    public function __construct() {
        $this->apiSettingsTable = new GptApiSetting('gpt_api_settings');
        $this->modelSettingsTable = new GptApiModelSetting('gpt_api_model_settings');

        // Initialize your Chat GPT API settings controller
        add_action('admin_init', array($this, 'register_settings'));
    }

    public function add_menu() {
        // Create an admin menu item for API settings
        add_menu_page('Chat GPT API Settings', 'Chat GPT API Settings', 'manage_options', 'gpt-api-settings', array($this, 'render_settings_page'));
    }

    public function render_settings_page() {
        // Data to be passed to the view
        $apiSettings = $this->apiSettingsTable->get_api_settings();

        // Load the view template for Chat GPT API settings
        include(dirname(plugin_dir_path(__FILE__)) . '/Views/settings/gpt-api-page.php');
    }

    public function register_settings() {
        // Register Chat GPT API settings
        register_setting('gpt_api_settings', 'gpt_api_settings', array($this, 'store_gpt_api_settings'));
    
        add_settings_section('gpt_api_section', 'Chat GPT API Settings', array($this, 'gpt_api_section_callback'), 'gpt_api_settings');
    
        add_settings_field('gpt_api_url', 'API URL', array($this, 'gpt_api_url_callback'), 'gpt_api_settings', 'gpt_api_section');
        add_settings_field('gpt_api_key', 'API Key', array($this, 'gpt_api_key_callback'), 'gpt_api_settings', 'gpt_api_section');
        add_settings_field('gpt_model_name', 'Model Name', array($this, 'gpt_model_name_callback'), 'gpt_api_settings', 'gpt_api_section');
    }
    
    public function store_gpt_api_settings() {
        // Get API URL and API Key from the input array
        $api_url = isset($_POST['gpt_api_url']) ? sanitize_text_field($_POST['gpt_api_url']) : null;
        $api_key = isset($_POST['gpt_api_key']) ? sanitize_text_field($_POST['gpt_api_key']) : null;
        $model_id = isset($_POST['gpt_model_name']) ? intval($_POST['gpt_model_name']) : null;
    
        // Update the database record
        $this->apiSettingsTable->create_record($api_url, $api_key, $model_id);
    }

    public function gpt_api_section_callback() {
        echo '';
    }

    // Callback to render the API URL input field
    public function gpt_api_url_callback() {
        $data = $this->apiSettingsTable->get_api_url();
        if (is_array($data) && isset($data['api_url'])) {
            $apiUrl = $data['api_url'];
        } else {
            $apiUrl = "";
        }
        include(dirname(plugin_dir_path(__FILE__)) . '/Views/settings/gpt-api-url.php');
    }

    // Callback to render the API Key input field
    public function gpt_api_key_callback() {
        $data = $this->apiSettingsTable->get_api_key();
        if (is_array($data) && isset($data['api_key'])) {
            $apiKey = $data['api_key'];
        } else {
            $apiKey = "";
        }
        include(dirname(plugin_dir_path(__FILE__)) . '/Views/settings/gpt-api-key.php');
    }

    // Callback to render the Model Name Dropdown
    public function gpt_model_name_callback() {
        $all_models = $this->modelSettingsTable->get_all_records();
        $selected_model = $this->modelSettingsTable->get_selected_model();
        include(dirname(plugin_dir_path(__FILE__)) . '/Views/settings/gpt-model-name.php');
    }
}