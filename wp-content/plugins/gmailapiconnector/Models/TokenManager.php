<?php

namespace GMAIL\Models;

class TokenManager {

    private $table_name;

    public function __construct($table_name) {
        global $wpdb;
        $this->table_name = $wpdb->prefix . $table_name;
    }

    /**
     * Create the token table during plugin activation.
     */
    public function create_table() {
        global $wpdb;

        $table_name = $this->table_name;

        $charset_collate = $wpdb->get_charset_collate();

        $query = $wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($table_name));

        if (!$wpdb->get_var($query) == $table_name) {
            $sql = "CREATE TABLE $table_name (
                id INT(11) NOT NULL AUTO_INCREMENT,
                access_token BLOB NOT NULL,
                refresh_token BLOB NOT NULL,
                expiration_time INT(11) NOT NULL,
                created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            ) $charset_collate;";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
    }
    
    /**
     * Store tokens in the database.
     *
     * @param string $access_token
     * @param string $refresh_token
     * @param int $expiration_time
     */
    public function store_access_tokens($access_token, $refresh_token, $expiration_time) {
        global $wpdb;
        $table_name = $this->table_name;

        // Prepare the data to be inserted.
        $data = array(
            'access_token' => $access_token,
            'refresh_token' => $refresh_token,
            'expiration_time' => $expiration_time,
        );

        // Clear existing tokens.
        $this->delete_tokens();

        // Insert the new tokens.
        $wpdb->insert($table_name, $data, array('%s', '%s', '%d'));
    }

    /**
     * Check if tokens exist in the database.
     *
     * @return object|null Database tokens or null if not found.
     */
    public function get_tokens_from_database() {
        global $wpdb;
        $table_name = $this->table_name;

        // Get tokens from the database.
        $tokens = $wpdb->get_row("SELECT * FROM $table_name");

        return $tokens;
    }

    /**
     * Update the access token in the database.
     *
     * @param string $access_token
     * @param int $id
     */
    public function update_access_token($access_token, $id) {
        global $wpdb;
        $table_name = $this->table_name;

        // Update the access token.
        $wpdb->update(
            $table_name,
            array('access_token' => $access_token),
            array('ID' => $id)
        );
    }

    /**
     * Delete all tokens from the database.
     */
    public function delete_tokens() {
        global $wpdb;
        $table_name = $this->table_name;

        // Delete all tokens.
        $wpdb->query("DELETE FROM $table_name");
    }

    /**
     * Drop the token table during plugin deactivation.
     */
    public function drop_table() {
        global $wpdb;

        $table_name = $this->table_name;

        $sql = "DROP TABLE IF EXISTS $table_name;";

        $wpdb->query($sql);
    }
}
