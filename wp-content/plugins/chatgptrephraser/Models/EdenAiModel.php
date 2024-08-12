<?php

namespace GPT\Models;

class EdenAiModel {
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
                api_key BLOB,
                api_url VARCHAR(255),
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            ) $charset_collate;";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
    }

    public function create_record($api_url = null, $api_key = null) {
        global $wpdb;

        // Check if a record already exists
        $existing_record = $wpdb->get_row("SELECT * FROM $this->table_name LIMIT 1");

        $data = [
            'api_url' => $api_url,
            'api_key' => $api_key,
        ];
        if ($existing_record) {
            // Update the existing record
            $wpdb->update($this->table_name, $data, ['id' => $existing_record->id]);
            if ($wpdb->last_error) {
                $error_message = $wpdb->last_error;
                // Handle or log the error as needed
                echo "Database Error: " . $error_message;
            }
        // exit( var_dump( $wpdb->last_query ) );

        } else {
            // Insert a new record
            $wpdb->insert($this->table_name, $data);
        }
    }

    public function get_api_settings() {
        global $wpdb;
        $query = "SELECT api_url, api_key FROM $this->table_name LIMIT 1";
        $result = $wpdb->get_row($query, ARRAY_A);

        return $result;
    }

    public function get_api_url() {
        global $wpdb;
        $query = "SELECT api_url FROM $this->table_name LIMIT 1";
        $result = $wpdb->get_row($query, ARRAY_A);

        return $result;
    }

    public function get_api_key() {
        global $wpdb;
        $query = "SELECT api_key FROM $this->table_name LIMIT 1";
        $result = $wpdb->get_row($query, ARRAY_A);

        return $result;
    }

    public function get_settings_id() {
        global $wpdb;
        $query = "SELECT id FROM $this->table_name LIMIT 1";
        $result = $wpdb->get_var($query);

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
?>