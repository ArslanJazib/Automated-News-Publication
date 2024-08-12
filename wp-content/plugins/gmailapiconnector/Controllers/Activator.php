<?php

namespace GMAIL\Controllers;

use GMAIL\Models\GmailApiSetting;
use GMAIL\Models\TokenManager;
use GMAIL\Controllers\GmailCptManager;

class Activator {
    private $gmailCptManager;
    private $apiSettingTable;
    private $tokenSettingTable;

    public function __construct() {

        $this->apiSettingTable = new GmailApiSetting('gmail_api_settings');

        $this->tokenSettingTable = new TokenManager('gmail_tokens');
        
        $this->gmailCptManager = new GmailCptManager();

    }

    public function create_api_settings_table() {
        $this->apiSettingTable->create_table();
    }

    public function create_gmail_tokens_table() {
        $this->tokenSettingTable->create_table();
    }

    public function create_gmail_post_type(){
        $this->gmailCptManager->register_custom_post_type();
    }

    public function create_gmail_post_type_taxonomy(){
        $this->gmailCptManager->register_custom_taxonomy();
    }
    
    public function scheduleGmailInboxTask() {
        // Use WordPress cron functions to schedule periodic tasks
        if (!wp_next_scheduled('gmail_fetch_inbox_event')) {
            wp_schedule_event(time(), 'minutely', 'gmail_fetch_inbox_event');
        }
    }

    public function check_dependencies() {
        // Check if the dependent plugin is active
        if (!is_plugin_active('advanced-custom-fields-pro/acf.php')) {
            
            // Deactivate your plugin
            deactivate_plugins(plugin_basename('gmailapiconnector/gmailapiconnector.php'));
    
            // Set an activation notice message
            add_action('admin_notices', array($this, 'dependencies_notice'));
        }
    }
    
    public function dependencies_notice() {
        $message = __('Gmail API Connector Plugin requires the ACF Pro Plugin to be installed and activated. Please activate the ACF Pro Plugin first.', '');
        echo '<div class="error"><p>' . $message . '</p> </div>';
    }

}
