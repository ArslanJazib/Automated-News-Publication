<?php

namespace GPT\Controllers;

use GPT\Models\GptApiModelSetting;
use GPT\Models\GptApiPromptFormat;
use GPT\Models\GptApiPromptInstruction;
use GPT\Models\GptApiPromptPlaceholder;
use GPT\Models\GptApiPromptRule;
use GPT\Models\GptApiSetting;
use GPT\Controllers\GptCptManager;
use GPT\Controllers\CustomCptManager;
class Uninstaller {
    private $apiSettingTable;
    private $modelSettingTable;
    private $promptPlaceholderTable;
    private $promptInstructionTable;
    private $promptFormatsTable;
    private $promptRuleTable;
    private $gptCptManager;
    private $customCptManager;

    public function __construct() {
        global $wpdb;

        $this->apiSettingTable = new GptApiSetting('gpt_api_settings');
        $this->modelSettingTable = new GptApiModelSetting('gpt_api_model_settings');        
        $this->promptPlaceholderTable = new GptApiPromptPlaceholder('gpt_api_prompt_placeholder');
        $this->promptInstructionTable = new GptApiPromptInstruction('gpt_api_prompt_instructions');
        $this->promptFormatsTable = new GptApiPromptFormat('gpt_api_prompt_formats');
        $this->promptRuleTable = new GptApiPromptRule('gpt_api_prompt_rules');

        $this->gptCptManager = new GptCptManager();
        $this->customCptManager = new CustomCptManager();

    }

    public function drop_api_settings_table() {
        $this->apiSettingTable->drop_table();
    }

    public function drop_gpt_api_model_settings_table() {
        $this->modelSettingTable->drop_table();
    }

    public function drop_gpt_api_prompt_placeholder_table(){
        $this->promptPlaceholderTable->drop_table();
    }

    public function drop_gpt_api_prompt_instructions_table() {
        $this->promptInstructionTable->drop_table();
    }

    public function drop_gpt_api_prompt_formats_table() {
        $this->promptFormatsTable->drop_table();
    }

    public function drop_gpt_api_prompt_rules_table() {
        $this->promptRuleTable->drop_table();
    }

    public function drop_gpt_post_type(){
        $this->gptCptManager->uninstall_custom_post_type();
    }

    public function drop_custom_post_type(){
        $this->customCptManager->uninstall_custom_post_type();
    }

    public function unscheduleRssPostsRephraseTask() {
        wp_clear_scheduled_hook('scraped_posts_to_gpt_event');
    }
}
