<?php 

namespace RSS\Controllers;

use RSS\Controllers\ScrapedCptManager;

class Deactivator {
    private $scrapedCptManager;

    public function __construct() {
        // Register the custom post type on activation
        $this->scrapedCptManager = new ScrapedCptManager();
    }

    public function disable_custom_post_type(){
        $this->scrapedCptManager->deactivate_custom_post_type();
    }

    public function unscheduleRssFeedsTask() {
        wp_clear_scheduled_hook('fetch_rss_feeds_event');
    }
}

?>