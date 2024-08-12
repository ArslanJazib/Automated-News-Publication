<?php

namespace RSS\Controllers;

use RSS\Models\ScrapperApiSetting;
use RSS\Models\RssSetting;
use RSS\Controllers\ScrapedCptManager;

class Uninstaller {
    private $apiSettingTable;
    private $rssSettingTable;
    private $scrapedCptManager;

    public function __construct() {
        global $wpdb;
        $api_table_name = $wpdb->prefix . 'api_settings';
        $this->apiSettingTable = new ScrapperApiSetting($api_table_name);
        $rss_table_name = $wpdb->prefix . 'rss_feeds';
        $this->rssSettingTable = new RssSetting($rss_table_name);
        $this->scrapedCptManager = new ScrapedCptManager();
    }

    public function drop_api_settings_table() {
        $this->apiSettingTable->drop_table();
    }

    public function drop_rss_feeds_table() {
        $this->rssSettingTable->drop_table();
    }

    public function drop_scraped_post_type(){
        $this->scrapedCptManager->uninstall_custom_post_type();
    }

    public function unscheduleRssFeedsTask() {
        wp_clear_scheduled_hook('fetch_rss_feeds_event');
    }
}
