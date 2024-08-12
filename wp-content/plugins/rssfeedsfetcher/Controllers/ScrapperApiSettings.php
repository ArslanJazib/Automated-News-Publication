<?php

namespace RSS\Controllers;

use RSS\Models\ScrapperApiSetting;

class ScrapperApiSettings {
    private $apiSettingsTable;

    public function __construct() {
        $this->apiSettingsTable = new ScrapperApiSetting('scrapper_api_settings');

        // Initialize your Scrapper API settings controller
        add_action('admin_init', array($this, 'register_settings'));
    }

    public function add_menu() {
        // Create admin menu item for API settings
        add_menu_page('Scrapper API Settings', 'Scrapper API Settings', 'manage_options', 'scrapper-api-settings', array($this, 'render_settings_page'));
    }

    public function render_settings_page() {
        // Data to be passed to the view
        $apiSettings = $this->apiSettingsTable->get_api_settings();

        // Load the view template for Scrapper API settings
        include(dirname(plugin_dir_path(__FILE__)) . '/Views/settings/scrapper-api-page.php');
    }

    public function register_settings() {
        // Register Scrapper API settings
        register_setting('scrapper_api_settings', 'scrapper_api_settings', array($this, 'store_scrapper_api_settings'));
    
        add_settings_section('scrapper_api_section', 'Scrapper API Settings', array($this, 'scrapper_api_section_callback'), 'scrapper_api_settings');
    
        add_settings_field('scrapper_api_url', 'API URL', array($this, 'scrapper_api_url_callback'), 'scrapper_api_settings', 'scrapper_api_section');
        add_settings_field('scrapper_api_key', 'API Key', array($this, 'scrapper_api_key_callback'), 'scrapper_api_settings', 'scrapper_api_section');
    }
    
    public function store_scrapper_api_settings() {

        // Get API URL and API Key from the input array
        $api_url = isset($_POST['scrapper_api_url']) ? sanitize_text_field($_POST['scrapper_api_url']) : null;
        $api_key = isset($_POST['scrapper_api_key']) ? sanitize_text_field($_POST['scrapper_api_key']) : null;
    
        // Update the database record
        $this->apiSettingsTable->create_record($api_url, $api_key);
    }

    public function scrapper_api_section_callback() {
        echo '';
    }

    // Callback to render the API URL input field
    public function scrapper_api_url_callback() {
        $data = $this->apiSettingsTable->get_api_url();
        if(is_array($data) && isset($data['api_url'])){
            $apiUrl = $data['api_url'];
        }else{
            $apiUrl = "";
        }
        include(dirname(plugin_dir_path(__FILE__)) . '/Views/settings/scrapper-api-url.php');
    }

    // Callback to render the API Key input field
    public function scrapper_api_key_callback() {
        $data = $this->apiSettingsTable->get_api_key();
        if(is_array($data) && isset($data['api_key'])){
            $apiKey = $data['api_key'];
        }else{
            $apiKey = "";
        }
        include(dirname(plugin_dir_path(__FILE__)) . '/Views/settings/scrapper-api-key.php');
    }
}

?>