<?php
namespace GPT\Models;

class GptApiPromptRule {
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
                rules LONGTEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            ) $charset_collate;";
            
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
    }

    public function create_record($rules) {
        global $wpdb;
        $data = ['rules' => $rules];
        $wpdb->insert($this->table_name, $data);
        return $wpdb->insert_id;
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

    public function get_record_by_rules($rules){
        global $wpdb;
        $query = $wpdb->prepare("SELECT * FROM $this->table_name WHERE rules = %s", $rules);
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