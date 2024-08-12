<?php

namespace GMAIL\Models;

class GmailApiSetting {
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
                client_id BLOB,
                project_id VARCHAR(255),
                auth_uri VARCHAR(255),
                token_uri VARCHAR(255),
                auth_provider_x509_cert_url VARCHAR(255),
                client_secret BLOB,
                redirect_uris TEXT,
                label_id VARCHAR(255),
                search_query TEXT,
                assign_labels_to_fetched_emails VARCHAR(255), 
                max_limit VARCHAR(255),
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            ) $charset_collate;";
            
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
    }

    public function create_record($data) {
        global $wpdb;
        
        // Check if a record already exists
        $existing_record = $wpdb->get_row("SELECT * FROM $this->table_name LIMIT 1");
        
        if ($existing_record) {
            // Update the existing record
            $wpdb->update($this->table_name, $data, ['id' => $existing_record->id]);
        } else {
            // Insert a new record
            $wpdb->insert($this->table_name, $data);
        }
    }

    public function get_api_settings() {
        global $wpdb;
        $query = "SELECT client_id, project_id, auth_uri, token_uri, auth_provider_x509_cert_url, 
        client_secret, redirect_uris, label_id, search_query, assign_labels_to_fetched_emails,max_limit FROM $this->table_name LIMIT 1";
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