<?php
class DB_Manager {
    public static function create_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'disc_assessments';
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id INT(6) UNSIGNED,
            assessment_data TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public static function drop_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'disc_assessments';
        $wpdb->query("DROP TABLE IF EXISTS $table_name");
    }
}
?>
