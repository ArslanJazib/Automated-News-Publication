<?php 

namespace GMAIL\Controllers;

use GMAIL\Models\GmailApiSetting;
use GMAIL\Controllers\GmailInboxParser;

class GmailApiSettings {
    private $apiSettings;
    private $apiSettingsTable;
    private $gmialInboxController;

    public function __construct() {
        $this->apiSettingsTable = new GmailApiSetting('gmail_api_settings');
        $this->apiSettings = $this->apiSettingsTable->get_api_settings();

        // Initialize your GMAIL API settings controller
        add_action('admin_init', array($this, 'register_settings'));

        $this->gmialInboxController = new GmailInboxParser();
    }

    public function add_menu() {
        // Create an admin menu item for API settings
        add_menu_page('GMAIL API Settings', 'GMAIL API Settings', 'manage_options', 'gmail-api-settings', array($this, 'render_settings_page'));
    }

    public function render_settings_page() {
        // Data to be passed to the view
        $apiSettings = $this->apiSettingsTable->get_api_settings();

        if($this->apiSettings){
            $authUrl = $this->gmialInboxController->get_auth_url();
        }
        
        // Load the view template for GMAIL API settings
        include(dirname(plugin_dir_path(__FILE__)) . '/Views/settings/gmail-api-page.php');
    }

    public function register_settings() {
        // Register GMAIL API settings
        register_setting('gmail_api_settings', 'gmail_api_settings', array($this, 'store_gmail_api_settings'));
    
        add_settings_section('gmail_api_section', 'GMAIL API Settings', array($this, 'gmail_api_section_callback'), 'gmail_api_settings');
        
        // Callback functions for each field
        $fields = array(
            'client_id' => 'Client Id',
            'project_id' => 'Project Id',
            'auth_uri' => 'Auth Uri',
            'token_uri' => 'Token Uri',
            'auth_provider_x509_cert_url' => 'Auth Provider X509 Cert Url',
            'client_secret' => 'Client Secret',
            'redirect_uris' => 'Redirect Uris',
            'label_id' => 'Label Id',
            'search_query' => 'Seach Query',
            'assign_labels_to_fetched_emails' => 'Assign Labels To fetched Emails',
            'max_limit' => 'Maximum Number Of Emails to fetch'
        );

        foreach ($fields as $field => $label) {
            add_settings_field($field, $label, array($this, $field . '_callback'), 'gmail_api_settings', 'gmail_api_section');
        }
    }
    
    public function store_gmail_api_settings() {
        // Get each API setting field from the input array
        $data = array(
            'client_id' => sanitize_text_field($_POST['client_id']),
            'project_id' => sanitize_text_field($_POST['project_id']),
            'auth_uri' => sanitize_text_field($_POST['auth_uri']),
            'token_uri' => sanitize_text_field($_POST['token_uri']),
            'auth_provider_x509_cert_url' => sanitize_text_field($_POST['auth_provider_x509_cert_url']),
            'client_secret' => sanitize_text_field($_POST['client_secret']),
            'redirect_uris' => sanitize_text_field($_POST['redirect_uris']),
            'label_id' => sanitize_text_field($_POST['label_id']),
            'search_query' => sanitize_text_field($_POST['search_query']),
            'assign_labels_to_fetched_emails' => sanitize_text_field($_POST['assign_labels_to_fetched_emails']),
            'max_limit' => sanitize_text_field($_POST['max_limit'])
        );

        // Update the database record
        $this->apiSettingsTable->create_record($data);
    }

    public function gmail_api_section_callback() {
        echo '';
    }

    // Callback for each field to render the input
    public function client_id_callback() {
        $this->render_field('client_id');
    }
    
    public function project_id_callback() {
        $this->render_field('project_id');
    }
    
    public function auth_uri_callback() {
        $this->render_field('auth_uri');
    }

    public function token_uri_callback() {
        $this->render_field('token_uri');
    }

    public function auth_provider_x509_cert_url_callback() {
        $this->render_field('auth_provider_x509_cert_url');
    }

    public function client_secret_callback() {
        $this->render_field('client_secret');
    }

    public function redirect_uris_callback() {
        $this->render_field('redirect_uris');
    }

    public function label_id_callback() {
        $this->render_field('label_id');
    }

    public function search_query_callback() {
        $this->render_field('search_query');
    }

    public function assign_labels_to_fetched_emails_callback() {
        $this->render_field('assign_labels_to_fetched_emails');
    }

    public function max_limit_callback() {
        $this->render_field('max_limit');
    }

    private function render_field($field) {
        $data = $this->apiSettingsTable->get_api_settings();
        $value = (is_array($data) && isset($data[$field])) ? $data[$field] : '';
        include(dirname(plugin_dir_path(__FILE__)) . '/Views/settings/' . $field . '.php');
    }
}
