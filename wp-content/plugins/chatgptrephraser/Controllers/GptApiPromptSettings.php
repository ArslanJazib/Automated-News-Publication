<?php

namespace GPT\Controllers;

use GPT\Models\GptApiPromptPlaceholder;
use GPT\Models\GptApiPromptInstruction;
use GPT\Models\GptApiPromptFormat;
use GPT\Models\GptApiPromptRule;

class GptApiPromptSettings {
    private $promptsTable;
    private $instructionsTable;
    private $promptFormatsTable;
    private $promptRuleTable;

    public function __construct() {
        $this->promptsTable = new GptApiPromptPlaceholder('gpt_api_prompt_placeholder');
        $this->instructionsTable = new GptApiPromptInstruction('gpt_api_prompt_instructions');
        $this->promptFormatsTable = new GptApiPromptFormat('gpt_api_prompt_formats');
        $this->promptRuleTable = new GptApiPromptRule('gpt_api_prompt_rules');

        add_action('admin_init', array($this, 'register_settings'));
    }

    public function add_menu() {
        add_menu_page('Chat GPT Prompt Settings', 'Chat GPT Prompt Settings', 'manage_options', 'prompts-settings', array($this, 'render_settings_page'));
    }

    public function render_settings_page() {
        $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'placeholders-tab';
        $placeholders = $this->promptsTable->get_all_records_eager_loaded();
        $formats = $this->promptFormatsTable->get_all_records();

        include(dirname(plugin_dir_path(__FILE__)) . '/Views/settings/prompt/gpt-prompt-page.php');
    }

    public function register_settings() {
        // Register Chat GPT API settings for each tab
        register_setting('gpt_api_prompt_settings_placeholder', 'gpt_api_prompt_settings_placeholder', array($this, 'store_gpt_api_prompt_settings'));
        register_setting('gpt_api_prompt_settings_instructions', 'gpt_api_prompt_settings_instructions', array($this, 'store_gpt_api_prompt_settings'));
        register_setting('gpt_api_prompt_settings_format', 'gpt_api_prompt_settings_format', array($this, 'store_gpt_api_prompt_settings'));
        register_setting('gpt_api_prompt_settings_output', 'gpt_api_prompt_settings_output', array($this, 'store_gpt_api_prompt_settings'));

        add_settings_section('gpt_api_prompt_section', '', array($this, 'gpt_api_prompt_section_callback'), 'gpt_api_prompt_settings_placeholder');
        add_settings_section('gpt_api_prompt_section', '', array($this, 'gpt_api_prompt_section_callback'), 'gpt_api_prompt_settings_instructions');
        add_settings_section('gpt_api_prompt_section', '', array($this, 'gpt_api_prompt_section_callback'), 'gpt_api_prompt_settings_format');
        add_settings_section('gpt_api_prompt_section', '', array($this, 'gpt_api_prompt_section_callback'), 'gpt_api_prompt_settings_output');

        // Placeholder Tab
        add_settings_field('gpt_placeholder_instructions', 'Placeholder Instructions', array($this, 'gpt_prompt_instructions_callback'), 'gpt_api_prompt_settings_placeholder', 'gpt_api_prompt_section');
        add_settings_field('gpt_prompt_placeholder', 'Prompt Placeholder', array($this, 'gpt_prompt_placeholder_callback'), 'gpt_api_prompt_settings_placeholder', 'gpt_api_prompt_section');

        // Instructions Tab
        add_settings_field('gpt_prompt_instructions', 'Prompt Instructions', array($this, 'gpt_prompt_rules_callback'), 'gpt_api_prompt_settings_instructions', 'gpt_api_prompt_section');

        // Format Tab
        add_settings_field('gpt_prompt_format', 'Output Format Rule', array($this, 'gpt_output_format_callback'), 'gpt_api_prompt_settings_format', 'gpt_api_prompt_section');

        // Output Tab
        add_settings_field('gpt_prompt_ouput', 'Prompt', array($this, 'gpt_prompt_output_callback'), 'gpt_api_prompt_settings_output', 'gpt_api_prompt_section');
    }

    public function store_gpt_api_prompt_settings() {
        $posted_data = $_POST;
        // Handle form data submission to add or update placeholders and instructions
        if (
            isset($posted_data['gpt_prompt_placeholders']) &&
            is_array($posted_data['gpt_prompt_placeholders']) &&
            isset($posted_data['gpt_prompt_instructions']) &&
            is_array($posted_data['gpt_prompt_instructions'])
        ) {
            // Clear out old placeholders and instructions
            $this->promptsTable->clear_all_records();
            $this->instructionsTable->clear_all_records();
    
            // Get the posted placeholders and instructions arrays
            $placeholders = $posted_data['gpt_prompt_placeholders'];
            $instructions = $posted_data['gpt_prompt_instructions'];
    
            foreach ($placeholders as $index => $placeholder) {
                $placeholder = sanitize_text_field($placeholder); // Sanitize user input
    
                if (!empty($placeholder)) {
                    // Insert a new placeholder
                    $placeholder_id = $this->promptsTable->create_record($placeholder);
    
                    // Get the corresponding instructions for the current placeholder
                    $currentInstructions = $instructions[$index];
    
                    // Update or create instructions associated with the placeholder
                    $this->update_or_create_instructions($placeholder_id, $currentInstructions);
                }
            }
        }
    
        // Handle form data submission to add or update rules
        if (
            isset($posted_data['gpt_prompt_rules'])
        ) {
            // Clear out old rules
            $this->promptRuleTable->clear_all_records();
    
            $rules = sanitize_text_field($posted_data['gpt_prompt_rules']);
            $rule_id = $this->promptRuleTable->create_record($rules);
        }
    
        // Handle form data submission to add or update formats
        if (isset($posted_data['gpt_prompt_formats']) &&
            is_array($posted_data['gpt_prompt_formats'])) {
            // Clear out old formats
            $this->promptFormatsTable->clear_all_records();
    
            $formats = $posted_data['gpt_prompt_formats'];
    
            foreach ($formats as $index => $format) {
                $format = ($format);
    
                if (!empty($format)) {
                    // Insert a new format
                    $format_id = $this->promptFormatsTable->create_record($format);
                }
            }
        }
    }
    
    
    private function update_or_create_instructions($placeholder_id, $currentInstruction) {
        if (!empty($placeholder_id) && !empty($currentInstruction)) {
            $instruction = sanitize_text_field($currentInstruction); // Sanitize user input

            // Check if the instruction exists
            $existing_instruction = $this->instructionsTable->get_record_by_placeholder_id_and_instruction($placeholder_id, $instruction);

            if (!$existing_instruction) {                
                // Insert a new instruction
                $this->instructionsTable->create_record($placeholder_id, $instruction);
            }
        }
    }

    public function generate_dynamic_settings_prompt() {
        // Retrieve eager loaded data from the placeholders table, including instructions
        $placeholdersWithInstructions = $this->promptsTable->get_all_records_eager_loaded();
    
        // Initialize the output
        $output = '';
    
        // Append placeholders and instructions
        foreach ($placeholdersWithInstructions as $placeholder) {
            $output .= "\n".$placeholder['all_instructions'] . " ";
            $output .= $placeholder['placeholder'] . " ";
        }
    
        // Retrieve data from the rules table
        $rules = $this->promptRuleTable->get_all_records();
    
        // Append rules
        foreach ($rules as $rule) {
            $updated_rule = str_replace('\\', "",$rule['rules']);
            $output .= "\n". wp_unslash($updated_rule) . " ";
        }
    
        // Retrieve data from the format table
        $formats = $this->promptFormatsTable->get_all_records();
    
        // Append formats
        foreach ($formats as $format) {
            $updated_format = str_replace('\\', "",$format['format']);
            $output .= "\n". wp_unslash($updated_format). " ";
        }
    
        // Output the dynamically compiled result
        return $output;
    }

    public function gpt_api_prompt_section_callback() {
        echo '';
    }

    public function gpt_prompt_instructions_callback() {
        include(dirname(plugin_dir_path(__FILE__)) . '/Views/settings/prompt/fields/gpt-prompt-instructions.php');
    }

    public function gpt_prompt_placeholder_callback() {
        include(dirname(plugin_dir_path(__FILE__)) . '/Views/settings/prompt/fields/gpt-prompt-placeholders.php');
    }

    public function gpt_prompt_rules_callback() {
        $rules = $this->promptRuleTable->get_all_records();
        if(!empty($rules) && is_array($rules) && isset($rules[0]['rules'])){
            $rules = $rules[0]['rules'];
        }
        include(dirname(plugin_dir_path(__FILE__)) . '/Views/settings/prompt/fields/gpt-prompt-rules.php');
    }

    public function gpt_output_format_callback() {
        include(dirname(plugin_dir_path(__FILE__)) . '/Views/settings/prompt/fields/gpt-prompt-format.php');
    }

    public function gpt_prompt_output_callback() {
        $finalPrompt = $this->generate_dynamic_settings_prompt();
        include(dirname(plugin_dir_path(__FILE__)) . '/Views/settings/prompt/fields/gpt-prompt-output.php');
    }
}

?>