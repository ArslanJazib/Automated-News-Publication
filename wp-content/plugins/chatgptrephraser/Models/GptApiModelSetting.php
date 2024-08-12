<?php
namespace GPT\Models;

class GptApiModelSetting {
    private $table_name;

    public function __construct($table_name) {
        global $wpdb;
        $this->table_name = $wpdb->prefix . $table_name;
    }

    public function create_table() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        
        $table_name = $this->table_name;
        
        // Check if the table already exists
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            $sql = "CREATE TABLE $table_name (
                id INT NOT NULL AUTO_INCREMENT,
                model_name VARCHAR(255),
                token_max_length VARCHAR(255),
                selected TINYINT(1) DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            ) $charset_collate;";
            
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
    }

    public function get_selected_model() {
        global $wpdb;
        
        // Prepare the SQL query to select the 'model_name' for the first selected model
        // $query = "SELECT model_name FROM $this->table_name WHERE selected = 1 LIMIT 1";
        $query = "SELECT $this->table_name.model_name FROM wp_gpt_api_settings
        INNER JOIN $this->table_name ON
        $this->table_name.id = wp_gpt_api_settings.model_id
        LIMIT 1";
        
        // Get the model name from the database
        $selected_model = $wpdb->get_var($query);

        if ($wpdb->last_error) {
            $error_message = $wpdb->last_error;
            // Handle or log the error as needed
            echo "Database Error: " . $error_message;
            exit( var_dump( $wpdb->last_query ) );
        }
    
        return $selected_model;
    }     
    
    public function seed_model_settings() {
        // Check if there are any existing records in the table
        if (!$this->has_records()) {
            // Define the initial data you want to insert
            $initial_data = [
                'model_name' => 'gpt-3.5-turbo-0301',
                'token_max_length' => '10000',
                'selected' => 0
            ];
    
            // Insert the initial data into the table
            $this->create_record($initial_data['model_name'], $initial_data['token_max_length'], $initial_data['selected']);

            // Define the initial data you want to insert
            $initial_data = [
                'model_name' => 'gpt-4-0314',
                'token_max_length' => '10000',
                'selected' => 1
            ];
    
            // Insert the initial data into the table
            $this->create_record($initial_data['model_name'], $initial_data['token_max_length'], $initial_data['selected']);

            // Define the initial data you want to insert
            $initial_data = [
                'model_name' => 'gpt-3.5-turbo-16k',
                'token_max_length' => '10000',
                'selected' => 0
            ];
    
            // Insert the initial data into the table
            $this->create_record($initial_data['model_name'], $initial_data['token_max_length'], $initial_data['selected']);

        }
    }
    
    public function has_records() {
        global $wpdb;
        $query = "SELECT COUNT(*) FROM $this->table_name";
        $count = $wpdb->get_var($query);
        return $count > 0;
    }    
    
    public function create_record($model_name = null, $token_max_length = null, $selected = null) {
        global $wpdb;
    
        // Check if a record already exists
        $existing_record = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM {$this->table_name} WHERE model_name = %s AND token_max_length = %s", $model_name, $token_max_length)
        );
    
        $data = [
            'model_name' => $model_name,
            'token_max_length' => $token_max_length,
            'selected' => $selected
        ];
    
        if ($existing_record) {
            // Update the existing record
            $wpdb->update($this->table_name, $data, ['id' => $existing_record->id]);
        } else {
            // Insert a new record
            $wpdb->insert($this->table_name, $data);
        }
    }
    
    public function get_model_settings() {
        global $wpdb;
        $query = "SELECT model_name, token_max_length FROM $this->table_name LIMIT 1";
        $result = $wpdb->get_row($query, ARRAY_A);
        
        return $result;
    } 
    
    public function get_model_name() {
        global $wpdb;
        $query = "SELECT model_name FROM $this->table_name LIMIT 1";
        $result = $wpdb->get_row($query, ARRAY_A);
        
        return $result;
    }  
    
    public function get_model_max_tokens() {
        global $wpdb;
        $query = "SELECT token_max_length FROM $this->table_name LIMIT 1";
        $result = $wpdb->get_row($query, ARRAY_A);
        
        return $result;
    }

    public function update_record($record_id, $data) {
        global $wpdb;
        
        return $wpdb->update($this->table_name, $data, array('id' => $record_id));
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

    public function drop_table() {
        global $wpdb;
        $wpdb->query("DROP TABLE IF EXISTS $this->table_name");
    }
}