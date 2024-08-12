<?php

namespace RSS\Controllers;

use RSS\Models\RssSetting;

class RssSettings {
    private $rssSettingsTable;

    public function __construct() {

        $this->rssSettingsTable = new RssSetting('rss_feeds');

        // Initialize your RSS feeds settings controller
        add_action('admin_init', array($this, 'register_settings'));
    }

    public function add_menu() {
        // Create admin menu item for RSS feeds settings
        add_menu_page('RSS Feeds Settings', 'RSS Feeds Settings', 'manage_options', 'rss-feeds-settings', array($this, 'render_settings_page'));
    }

    public function render_settings_page() {
        // Data to be passed to the view
        $rssLinks = $this->rssSettingsTable->get_rss_urls_records();

        // Load the view template
        include(dirname(plugin_dir_path(__FILE__)) . '/Views/settings/rss-feeds-page.php');
    }

    public function register_settings() {
        register_setting('rss_feeds_settings', 'rss_feed_urls', array($this, 'store_rss_feeds'));
        register_setting('rss_feeds_settings', 'max_allowed_items');
        add_settings_section('rss_feed_section', '', array($this, 'rss_feed_section_callback'), 'rss_feeds_settings');
        add_settings_field('max_allowed_items', 'Max Allowed Items', array($this, 'max_allowed_items_callback'), 'rss_feeds_settings', 'rss_feed_section');
        add_settings_field('rss_feed_urls', 'RSS Feed URL', array($this, 'rss_feed_urls_callback'), 'rss_feeds_settings', 'rss_feed_section');
    }

    public function store_rss_feeds() {
        $posted_data = $_POST;

        if (isset($posted_data['rss_feed_urls']) && is_array($posted_data['rss_feed_urls'])) {
            // Clear all records first
            $this->rssSettingsTable->clear_all_records();
    
            foreach ($posted_data['rss_feed_urls'] as $index => $rss_feed_url) {
                // Only update if the URL is not empty
                if (!empty($rss_feed_url)) {
                    // Get the corresponding max items value from the submitted data
                    $max_items = !empty($posted_data['rss_feed_max'][$index]) ? intval($posted_data['rss_feed_max'][$index]) : 1;
    
                    // Insert a new record with the URL and max items value
                    $this->rssSettingsTable->create_record($rss_feed_url, $max_items);
                }
            }
        }
    }
    
    public function rss_feed_section_callback() {
        echo '';
    }
    
    public function rss_feed_urls_callback() {
        include(dirname(plugin_dir_path(__FILE__)) . '/Views/settings/rss-feeds-urls.php');
    }  
    
    public function max_allowed_items_callback() {
        include(dirname(plugin_dir_path(__FILE__)) . '/Views/settings/rss-feeds-max.php');
    }  
}