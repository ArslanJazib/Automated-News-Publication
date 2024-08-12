<?php

namespace GMAIL\Controllers;

use GMAIL\Models\GmailApiSetting;
use GMAIL\Models\TokenManager;
use GMAIL\Controllers\GmailCptManager;

class Uninstaller {
    private $apiSettingTable;
    private $gmailCptManager;
    private $tokenSettingTable;

    public function __construct() {
        global $wpdb;

        $this->apiSettingTable = new GmailApiSetting('gmail_api_settings');

        $this->tokenSettingTable = new TokenManager('gmail_tokens');

        $this->gmailCptManager = new GmailCptManager();
    }

    public function drop_api_settings_table() {
        $this->apiSettingTable->drop_table();
    }

    public function drop_gmail_tokens_table() {
        $this->tokenSettingTable->drop_table();
    }

    public function drop_gmail_post_type(){
        $this->gmailCptManager->uninstall_gmail_post_type();
    }

    public function unscheduleGmailInboxTask() {
        wp_clear_scheduled_hook('gmail_fetch_inbox_event');
    }
}
