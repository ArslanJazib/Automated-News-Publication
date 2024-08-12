<?php
    namespace RSS\Models;

    class RssSetting {
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
                    rss_feed_link VARCHAR(255) NOT NULL,
                    max_allowed_items INT DEFAULT 1,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (id)
                ) $charset_collate;";
        
                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                dbDelta($sql);
            }
        }    
        
        public function create_record($rss_feed_link, $max_items) {
            global $wpdb;
            
            return $wpdb->insert(
                $this->table_name,
                array(
                    'rss_feed_link' => $rss_feed_link,
                    'max_allowed_items' => $max_items
                )
            );
        }
        
        public function get_record_by_url($url) {
            global $wpdb;
            $query = $wpdb->prepare("SELECT id, rss_feed_link, max_allowed_items FROM $this->table_name WHERE rss_feed_link = %s", $url);
            $result = $wpdb->get_row($query, ARRAY_A);
        
            return $result;
        }        

        public function update_record($record_id, $rss_feed_link = null, $max_items = null) {
            global $wpdb;
            
            $data = array();
        
            // Only update the fields that are provided
            if ($rss_feed_link !== null) {
                $data['rss_feed_link'] = $rss_feed_link;
            }
        
            if ($max_items !== null) {
                $data['max_allowed_items'] = $max_items;
            }
        
            return $wpdb->update(
                $this->table_name,
                $data,
                array('id' => $record_id)
            );
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

        public function get_rss_urls_records() {
            global $wpdb;
            $query = "SELECT rss_feed_link, max_allowed_items  FROM $this->table_name";
            $results = $wpdb->get_results($query, ARRAY_A);
            
            return $results;
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
?>