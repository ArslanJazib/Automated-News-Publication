<?php

/*
Plugin Name: RSS Feed Fetcher
Description: The RSS Feed Fetcher plugin for WordPress allows you to easily fetch and manage multiple XML RSS Feeds. It provides an intuitive admin interface for adding and configuring RSS Feeds, which are then processed and stored as custom post types. The plugin also handles the segregation of posts based on paywalls, and posts without paywalls are scraped using an abstract API.
Version: 2.0
Author: Bilal Khalid
*/

defined('ABSPATH') or die('I can see you');

if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
    require_once dirname(__FILE__) . '/vendor/autoload.php';
}

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

use RSS\Controllers\Activator;
use RSS\Controllers\Deactivator;
use RSS\Controllers\Uninstaller;
use RSS\Controllers\ScrapperApiSettings;
use RSS\Controllers\RssSettings;
use RSS\Controllers\RssFeedsParser;

if (!class_exists('RssFeedFetcher')) {
    class RssFeedFetcher {
        private $activator;
        private $deactivator;
        private $api_setting;
        private $rss_setting;
        private $rss_feed_parser;

        public function __construct() {
            add_action('admin_init', array($this, 'dependency_checker'));

            $this->activator = new Activator();
            register_activation_hook(__FILE__, array($this, 'plugin_activation'));

            $this->deactivator = new Deactivator();
            register_deactivation_hook(__FILE__, array($this, 'plugin_deactivation'));

            add_action('init', array($this, 'render_custom_post_type'));
            add_action('init', array($this, 'render_custom_post_type_taxonomy'));

            $this->api_setting = new ScrapperApiSettings();
            add_action('admin_menu', array($this, 'render_api_settings_page'), 10);

            $this->rss_setting = new RssSettings();
            add_action('admin_menu', array($this, 'render_rss_settings_page'), 10);

            // Crone Job To Fetch Articles From RSS Feeds
            // add_action('fetch_rss_feeds_event', array($this, 'executeFetchRssFeeds'));

            // For testing purposes only
            // add_action('init', array($this, 'executeFetchRssFeeds'));

            // Register the uninstall hook outside the class constructor
            register_uninstall_hook(__FILE__, array('RssFeedFetcher', 'plugin_uninstall'));

            $this->rss_feed_parser = new RssFeedsParser();

        }

        public function dependency_checker(){
            $this->activator->check_dependencies();
        }

        public function plugin_activation() {
            $this->activator->create_api_settings_table();
            $this->activator->create_rss_feeds_table();
            $this->activator->scheduleRssFeedParsingTask();
        }

        public function plugin_deactivation() {
            $this->deactivator->disable_custom_post_type();
            $this->deactivator->unscheduleRssFeedsTask();
        }

        public static function plugin_uninstall() {
            $uninstaller = new Uninstaller();
            $uninstaller->drop_api_settings_table();
            $uninstaller->drop_rss_feeds_table();
            $uninstaller->drop_scraped_post_type();
            $uninstaller->unscheduleRssFeedsTask();
        }

        public function render_custom_post_type() {
            $this->activator->create_scraped_post_type();
        }

        public function render_custom_post_type_taxonomy(){
            $this->activator->create_scraped_post_type_taxonomy();
        }

        public function render_rss_settings_page() {
            $this->rss_setting->add_menu();
        }

        public function render_api_settings_page() {
            $this->api_setting->add_menu();
        }

        public function executeFetchRssFeeds() {
            $this->rss_feed_parser->fetch_rss_feeds();
        }

        public function do_rss_parse($url = array(),  $title = array(), $text = array(), $link = array(), $extra_inputs= array()){

            $this->$url = $url;
            $this->$title = $title;
            $this->$text = $text;
            $this->$link = $link;
            $this->$extra_inputs = $extra_inputs;
            $empty_flag = 1;

            foreach ($url as $value) {
                if(!empty($value)){
                    $empty_flag = 0 ;
                    break;
                }
            }

            if($empty_flag == 0){
                $res_data = $this->rss_feed_parser->insert_scraped_post($url, $title, $text, $link, $extra_inputs);
            }
            elseif($empty_flag == 1){
                $res_data = $this->rss_feed_parser->insert_custom_post($title, $text, $link, $extra_inputs);
            }
            return $res_data;
            exit;
        }

        public function formated_scrapped_content($url){
            $scrapped_data = $this->rss_feed_parser->return_formated_scrapped_content($url);
            return $scrapped_data;
        }

    }

    $rss_feed_fetcher = new RssFeedFetcher();
}