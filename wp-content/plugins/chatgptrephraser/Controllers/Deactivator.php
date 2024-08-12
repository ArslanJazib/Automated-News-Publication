<?php 

namespace GPT\Controllers;

use GPT\Controllers\GptCptManager;
use GPT\Controllers\CustomCptManager;

class Deactivator {
    private $gptCptManager;
    private $customCptManager;

    public function __construct() {
        // Register the custom post type on activation
        $this->gptCptManager = new GptCptManager();
        $this->customCptManager = new CustomCptManager();
    }

    public function disable_gpt_post_type(){
        $this->gptCptManager->deactivate_custom_post_type();
    }

    public function disable_custom_post_type(){
        $this->customCptManager->deactivate_custom_post_type();
    }

    public function unscheduleRssPostsRephraseTask() {
        wp_clear_scheduled_hook('fetch_rss_feeds_event');
    }
}

?>