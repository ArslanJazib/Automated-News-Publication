<?php

/*
Plugin Name: ChatGPT Rephraser
Description: The ChatGPT Rephraser plugin is a powerful tool designed to enhance your ChatGPT experience by providing a user-friendly settings page that interfaces directly with the ChatGPT API. With this plugin, you can effortlessly manage your interaction with ChatGPT and tailor it to your specific needs.
Version: 2.1
Author: Bilal Khalid
*/

defined('ABSPATH') or die('I can see you');

if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
    require_once dirname(__FILE__) . '/vendor/autoload.php';
}

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

use GPT\Controllers\Activator;
use GPT\Controllers\Deactivator;
use GPT\Controllers\Uninstaller;
use GPT\Controllers\GptApiSettings;
//use GPT\Controllers\EdenAiSettings;
use GPT\Controllers\ContentRephrasers;
use GPT\Controllers\GptApiPromptSettings;

if (!class_exists('ChatGptRephraser')) {
    class ChatGptRephraser {
        private $activator;
        private $deactivator;
        private $api_setting;
        private $eden_api_setting;
        private $model_setting;
        private $api_prompt_setting;
        private $is_rss_plugin_active;
        private $is_gmail_plugin_active;
        private $content_rephrasers;

        public function __construct() {
            add_action('admin_init', array($this, 'dependency_checker'));

            $this->activator = new Activator();
            register_activation_hook(__FILE__, array($this, 'plugin_activation'));

            $this->deactivator = new Deactivator();
            register_deactivation_hook(__FILE__, array($this, 'plugin_deactivation'));

            add_action('init', array($this, 'render_custom_post_type'));
            add_action('init', array($this, 'render_custom_post_type_taxonomy'));

            $this->api_setting = new GptApiSettings();
            add_action('admin_menu', array($this, 'render_api_settings_page'), 10);

            //  $this->eden_api_setting = new EdenAiSettings();
            // add_action('admin_menu', array($this, 'render_eden_api_settings_page'), 9);

            $this->api_prompt_setting = new GptApiPromptSettings();
            add_action('admin_menu', array($this, 'render_api_prompt_settings_page'), 10);

            $this->content_rephrasers = new ContentRephrasers();
            if (is_plugin_active('rssfeedsfetcher/rssfeedsfetcher.php')) {
                $this->is_rss_plugin_active = true;

                // Crone Job To Rephrase automated articles
                // add_action('scraped_posts_to_gpt_event', array($this, 'executeRssToGptFeeds'));

                // For testing purposes only
                // add_action('init', array($this, 'executeRssToGptFeeds'));
            }else{
                $this->is_rss_plugin_active = false;
            }

            // Register the uninstall hook outside the class constructor
            register_uninstall_hook(__FILE__, array('ChatGptRephraser', 'plugin_uninstall'));

        }

        public function dependency_checker(){
            $this->activator->check_dependencies();
        }

        public function plugin_activation() {
            $this->activator->create_api_settings_table();
            $this->activator->create_gpt_model_table();
            $this->activator->create_gpt_prompt_table();
            $this->activator->create_gpt_prompt_instructions_table();
            $this->activator->create_gpt_prompt_rules_table();
            $this->activator->create_gpt_prompt_formats_table();
            if($this->is_rss_plugin_active){
                $this->activator->scheduleRssPostsRephraseTask();
            }
        }

        public function plugin_deactivation() {
            $this->deactivator->disable_custom_post_type();
            $this->deactivator->unscheduleRssPostsRephraseTask();

        }

        public static function plugin_uninstall() {
            $uninstaller = new Uninstaller();
            $uninstaller->drop_api_settings_table();
            $uninstaller->drop_gpt_api_model_settings_table();
            $uninstaller->drop_gpt_api_prompt_placeholder_table();
            $uninstaller->drop_gpt_api_prompt_instructions_table();
            $uninstaller->drop_gpt_api_prompt_formats_table();
            $uninstaller->drop_gpt_api_prompt_rules_table();
            $uninstaller->unscheduleRssPostsRephraseTask();
        }

        public function render_custom_post_type() {
            $this->activator->create_gpt_post_type();
            $this->activator->create_custom_post_type();
        }

        public function render_custom_post_type_taxonomy(){
            $this->activator->create_gpt_post_type_taxonomy();
            $this->activator->create_custom_post_type_taxonomy();
        }

        public function render_model_settings_page() {
            $this->model_setting->add_menu();
        }

        public function render_api_settings_page() {
            $this->api_setting->add_menu();
        }

         public function render_eden_api_settings_page() {
            $this->eden_api_setting->add_menu();
        }

        public function render_api_prompt_settings_page() {
            $this->api_prompt_setting->add_menu();
        }

        public function executeRssToGptFeeds() {
            $this->content_rephrasers->scraped_posts_to_gpt();
        }

       public function process_chatgpt_request($c_id=NULL,$prompt=NULL,$gpt_model_name=NULL,$c_text=NULL){
            return $this->content_rephrasers->call_gpt_api($c_id,$prompt,$gpt_model_name,$c_text);
        }

        public function get_prompt(){
            return $this->api_prompt_setting->generate_dynamic_settings_prompt();
        }

        public function get_gpt_models(){
            return $this->api_setting->gpt_model_name_callback();
        }

        public function create_custom_gpt_post($content,$prompt,$c_id,$taxanomy,$title=NULL,$flag=NULL,$draft_flag=NULL,$input_text_value=null,$secondary_taxonomy,$created_gpt_post_id=NULL){

            return $this->content_rephrasers->create_custom_gpt_post($content,$prompt,$c_id,$taxanomy,$title,$flag,$draft_flag,$input_text_value,$secondary_taxonomy,$created_gpt_post_id);
        }

        public function update_custom_gpt_post($content,$prompt,$c_id,$taxanomy,$title=NULL,$flag=NULL,$input_text_value=null,$secondary_taxonomy,$created_gpt_post_id=NULL,$draftGPTPostID=null){

            return $this->content_rephrasers->update_custom_gpt_post($content,$prompt,$c_id,$taxanomy,$title,$flag,$input_text_value,$secondary_taxonomy,$created_gpt_post_id,$draftGPTPostID);
        }

        public function get_gpt_post_from_custom_post ($c_id) {
            return $this->content_rephrasers->get_gpt_post_from_custom_post($c_id);
        }

        public function get_scraped_post_from_custom_post ($c_id) {
            return $this->content_rephrasers->get_scraped_post_from_custom_post($c_id);
        }

        public function compare_ai_original_content($original_content,$gpt_postcontent,$comp_flag){
            return $this->content_rephrasers->compare_ai_original_content_chatgpt($original_content,$gpt_postcontent,$comp_flag);
        }

        public function chat_gpt_request_newsmaster($input_data = array()){
            return $this->content_rephrasers->call_gpt_api_newsmaster($input_data);
        }

        public function call_gpt_api_for_gmail_controller($gmail_post_id){
            return $this->content_rephrasers->call_gpt_api_for_gmail($gmail_post_id);
        }
        public function chatgpt_rephrasing_call($post_data){
            return $this->content_rephrasers->call_gpt_api_for_rephrasing($post_data);
        }

    }

    $chat_gpt_rephraser = new ChatGptRephraser();
}