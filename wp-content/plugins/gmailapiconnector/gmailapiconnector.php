<?php

/*
Plugin Name: Gmail API Connector
Description: The Gmail API Connector plugin for WordPress allows you to easily fetch and manage multiple emails from your inbox in an automated manner. It provides an intuitive admin interface for adding and configuring API Settings, which are then processed and stored as custom post types.
Version: 2.0
Author: Bilal Khalid
*/

defined('ABSPATH') or die('I can see you');

if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
    require_once dirname(__FILE__) . '/vendor/autoload.php';
}

include_once(ABSPATH . 'wp-admin/includes/plugin.php');
include_once(ABSPATH . 'wp-admin/includes/post.php' );

use GMAIL\Controllers\Activator;
use GMAIL\Controllers\Deactivator;
use GMAIL\Controllers\Uninstaller;
use GMAIL\Controllers\GmailApiSettings;
use GMAIL\Controllers\GmailInboxParser;

if (!class_exists('GmailApiConnector')) {
    class GmailApiConnector {
        private $activator;
        private $deactivator;
        private $api_setting;
        private $gmail_inbox_extractor;

        public function __construct() {
            add_action('admin_init', array($this, 'dependency_checker'));

            $this->activator = new Activator();
            register_activation_hook(__FILE__, array($this, 'plugin_activation'));

            $this->deactivator = new Deactivator();
            register_deactivation_hook(__FILE__, array($this, 'plugin_deactivation'));

            add_action('init', array($this, 'render_custom_post_type'));
            add_action('init', array($this, 'render_custom_post_type_taxonomy'));


            $this->api_setting = new GmailApiSettings();
            add_action('admin_menu', array($this, 'render_api_settings_page'), 10);

            add_action('wp_ajax_authorize_gmail', array($this, 'ajax_authorize_gmail'));
            
            // Crone Job To Fetch Press Releases From Gmail Inbox
            // add_action('gmail_fetch_inbox_event', array($this, 'execute_fetch_gmail_inbox'));

            // For testing purposes only
            // add_action('init', array($this, 'execute_fetch_gmail_inbox'));

            // Enqueue script and localize variables
            add_action('admin_enqueue_scripts', array($this, 'enqueue_my_script'));

            // Register the uninstall hook outside the class constructor
            register_uninstall_hook(__FILE__, array('GmailApiConnector', 'plugin_uninstall'));

            $this->gmail_inbox_extractor = new GmailInboxParser();

        }

        public function dependency_checker(){
            $this->activator->check_dependencies();
        }

        public function plugin_activation() {
            $this->activator->create_api_settings_table();
            $this->activator->create_gmail_tokens_table();
            $this->activator->scheduleGmailInboxTask();
        }

        public function plugin_deactivation() {
            $this->deactivator->disable_custom_post_type();
            $this->deactivator->unscheduleGmailInboxTask();
        }

        public static function plugin_uninstall() {
            $uninstaller = new Uninstaller();
            $uninstaller->drop_api_settings_table();
            $uninstaller->drop_gmail_tokens_table();
            $uninstaller->drop_gmail_post_type();
            $uninstaller->unscheduleGmailInboxTask();
        }

        public function render_custom_post_type() {
            $this->activator->create_gmail_post_type();
        }

        public function render_custom_post_type_taxonomy(){
            $this->activator->create_gmail_post_type_taxonomy();
        }

        public function render_api_settings_page() {
            $this->api_setting->add_menu();
        }

        public function execute_fetch_gmail_inbox() {
            $this->gmail_inbox_extractor->processEmails();
        }

        public function ajax_authorize_gmail() {            
            if (!check_ajax_referer('authorize-gmail-nonce', 'security_nonce', false)) {
                wp_send_json_error('Nonce verification failed.');
            }

            // Get the authentication code from the AJAX data, or set it to null if not present
            $authCode = isset($_POST['gmail_auth_code']) ? sanitize_text_field($_POST['gmail_auth_code']) : null;

            // Call the create_access_token method with the authentication code
            wp_send_json_success($this->gmail_inbox_extractor->create_access_token($authCode));
        }

        // Enqueue script and localize variables (remove wp_enqueue_script)
        public function enqueue_my_script() {
            // Localize nonce and AJAX URL
            wp_localize_script('jquery', 'myplugin_vars', array(
                'security_nonce' => wp_create_nonce('authorize-gmail-nonce'),
                'ajax_url' => admin_url('admin-ajax.php'),
            ));
        }
    }

    $gmail_api_connector = new GmailApiConnector();
}