<?php 

namespace GMAIL\Controllers;

use GMAIL\Controllers\GmailCptManager;

class Deactivator {
    private $gmailCptManager;

    public function __construct() {
        // Register the custom post type on activation
        $this->gmailCptManager = new GmailCptManager();
    }

    public function disable_custom_post_type(){
        $this->gmailCptManager->deactivate_gmail_post_type();
    }

    public function unscheduleGmailInboxTask() {
        wp_clear_scheduled_hook('gmail_fetch_inbox_event');
    }
}

?>