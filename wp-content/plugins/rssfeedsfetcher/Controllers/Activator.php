<?php

namespace RSS\Controllers;

use RSS\Models\ScrapperApiSetting;
use RSS\Models\RssSetting;
use RSS\Controllers\ScrapedCptManager;

class Activator {
    private $scrapedCptManager;
    private $apiSettingTable;
    private $rssSettingTable;

    public function __construct() {
        $api_table_name = 'scrapper_api_settings';
        $this->apiSettingTable = new ScrapperApiSetting($api_table_name);
        $rss_table_name = 'rss_feeds';
        $this->rssSettingTable = new RssSetting($rss_table_name);
        $this->scrapedCptManager = new ScrapedCptManager();
    }

    public function create_api_settings_table() {
        $this->apiSettingTable->create_table();
    }

    public function create_rss_feeds_table() {
        $this->rssSettingTable->create_table();
    }

    public function create_scraped_post_type(){
        $this->scrapedCptManager->register_custom_post_type();
    }

    public function create_scraped_post_type_taxonomy(){
        $this->scrapedCptManager->register_custom_taxonomy();
    }
    public function scheduleRssFeedParsingTask() {
        // Use WordPress cron functions to schedule periodic tasks
        if (!wp_next_scheduled('fetch_rss_feeds_event')) {
            wp_schedule_event(time(), 'minutely', 'fetch_rss_feeds_event');
        }
    }
    
    public function check_dependencies() {
        // Check if the dependent plugin is active
        if (!is_plugin_active('advanced-custom-fields-pro/acf.php')) {
            
            // Deactivate your plugin
            deactivate_plugins(plugin_basename('rssfeedsfetcher/rssfeedsfetcher.php'));
    
            // Set an activation notice message
            add_action('admin_notices', array($this, 'dependencies_notice'));
        }
    }
    
    public function dependencies_notice() {
        $message = __('RSS Feed Fetcher Plugin requires the ACF Pro Plugin to be installed and activated. Please activate the ACF Pro Plugin first.', '');
        echo '<div class="error"><p>' . $message . '</p> </div>';
    }

}
