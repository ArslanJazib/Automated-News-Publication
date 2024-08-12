<?php
namespace GPT\Models;

class GptApiPromptPlaceholder {
    private $table_name;

    public function __construct($table_name) {
        global $wpdb;
        $this->table_name = $wpdb->prefix . $table_name;
    }

    public function create_table() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
    
        $table_name = $this->table_name;
    
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            $sql = "CREATE TABLE $table_name (
                id INT NOT NULL AUTO_INCREMENT,
                placeholder VARCHAR(255),
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            ) $charset_collate;";
            
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
    }

    public function create_record($placeholder) {
        global $wpdb;
        $data = ['placeholder' => $placeholder];
        $wpdb->insert($this->table_name, $data);
        return $wpdb->insert_id; // Return the newly created placeholder ID
    }

    public function update_record($record_id, $data) {
        global $wpdb;
        
        return $wpdb->update($this->table_name, $data, ['id' => $record_id]);
    }

    public function delete_record($record_id) {
        global $wpdb;
        return $wpdb->delete($this->table_name, array('id' => $record_id));
    }

    public function get_all_records() {
        global $wpdb;
        $query = "SELECT * FROM $this->table_name";
        $results = $wpdb->get_results($query, ARRAY_A);
        
        return $results;
    }

    public function get_all_records_eager_loaded() {
        global $wpdb;

        $prompt_instructions_table = $wpdb->prefix . 'gpt_api_prompt_instructions';
        
        // Perform a SQL JOIN to retrieve instructions for all placeholders
        $query = "SELECT p.placeholder, GROUP_CONCAT(i.instructions SEPARATOR '\n') AS all_instructions
                  FROM $this->table_name AS p
                  LEFT JOIN $prompt_instructions_table AS i ON p.id = i.placeholder_id
                  GROUP BY p.placeholder";
        
        // Fetch all instructions for all placeholders
        $results = $wpdb->get_results($query, ARRAY_A);
        
        return $results;
    }    

    public function get_record_by_placeholder($placeholder){
        global $wpdb;
        $query = $wpdb->prepare("SELECT * FROM $this->table_name WHERE placeholder = %s", $placeholder);
        $result = $wpdb->get_row($query, ARRAY_A);
    
        return $result;
    }

    public function clear_all_records() {
        global $wpdb;
        $wpdb->query("DELETE FROM $this->table_name");
    }
    
    public function drop_table() {
        global $wpdb;
        $wpdb->query("DROP TABLE IF EXISTS $this->table_name");
    }
}