<?php

namespace GPT\Controllers;

use GPT\Models\GptApiSetting;
use GPT\Models\GptApiModelSetting;
use GPT\Models\GptApiPromptPlaceholder;
use GPT\Models\GptApiPromptInstruction;
use GPT\Models\GptApiPromptFormat;
use GPT\Models\GptApiPromptRule;
use GPT\Controllers\GptCptManager;
use GPT\Controllers\CustomCptManager;

class Activator {
    private $gptCptManager;
    private $customCptManager;
    private $promptRuleTable;
    private $apiSettingTable;
    private $modelSettingTable;
    private $promptFormatsTable;
    private $promptPlaceholderTable;
    private $promptInstructionTable;

    public function __construct() {
        $this->apiSettingTable = new GptApiSetting('gpt_api_settings');
        $this->modelSettingTable = new GptApiModelSetting('gpt_api_model_settings');        
        $this->promptPlaceholderTable = new GptApiPromptPlaceholder('gpt_api_prompt_placeholder');
        $this->promptInstructionTable = new GptApiPromptInstruction('gpt_api_prompt_instructions');
        $this->promptFormatsTable = new GptApiPromptFormat('gpt_api_prompt_formats');
        $this->promptRuleTable = new GptApiPromptRule('gpt_api_prompt_rules');

        $this->gptCptManager = new GptCptManager();
        $this->customCptManager = new CustomCptManager();
    }

    public function create_api_settings_table() {
        $this->apiSettingTable->create_table();
    }

    public function create_gpt_model_table() {
        $this->modelSettingTable->create_table();
        $this->modelSettingTable->seed_model_settings();
    }

    public function create_gpt_prompt_table() {
        $this->promptPlaceholderTable->create_table();
    }

    public function create_gpt_prompt_instructions_table() {
        $this->promptInstructionTable->create_table();
    }

    public function create_gpt_prompt_formats_table() {
        $this->promptFormatsTable->create_table();
    }

    public function create_gpt_prompt_rules_table() {
        $this->promptRuleTable->create_table();
    }

    public function create_gpt_post_type(){
        $this->gptCptManager->register_custom_post_type();
    }

    public function create_gpt_post_type_taxonomy(){
        $this->gptCptManager->register_custom_taxonomy();
    }

    public function create_custom_post_type(){
        $this->customCptManager->register_custom_post_type();
    }

    public function create_custom_post_type_taxonomy(){
        $this->customCptManager->register_custom_taxonomy();
    }

    public function scheduleRssPostsRephraseTask() {
        // Use WordPress cron functions to schedule periodic tasks
        if (!wp_next_scheduled('scraped_posts_to_gpt_event')) {
            wp_schedule_event(time(), 'minutely', 'scraped_posts_to_gpt_event');
        }
    }

    public function check_dependencies() {
        // Check if the dependent plugin is active
        if (!is_plugin_active('advanced-custom-fields-pro/acf.php')) {
            
            // Deactivate your plugin
            deactivate_plugins(plugin_basename('chatgptrephraser/chatgptrephraser.php'));
    
            // Set an activation notice message
            add_action('admin_notices', array($this, 'dependencies_notice'));
        }
    }
    
    public function dependencies_notice() {
        $message = __('ChatGPT Rephraser Plugin requires the ACF Pro Plugin to be installed and activated. Please activate the ACF Pro Plugin first.', '');
        echo '<div class="error"><p>' . $message . '</p> </div>';
    }

}
