<?php
class DB_Manager {
    public static function create_table() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $sql_users = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}disc_users` (
            `user_id` INT NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(255) NOT NULL,
            `email` VARCHAR(255) NOT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`user_id`)
        ) {$charset_collate};";

        $sql_tests = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}disc_tests` (
            `test_id` INT NOT NULL AUTO_INCREMENT,
            `test_name` VARCHAR(255) NOT NULL,
            `test_description` TEXT NOT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`test_id`)
        ) {$charset_collate};";

        $sql_questions = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}disc_questions` (
            `question_id` INT NOT NULL AUTO_INCREMENT,
            `test_id` INT NOT NULL,
            `question_text` TEXT NOT NULL,
            PRIMARY KEY (`question_id`),
            FOREIGN KEY (`test_id`) REFERENCES `{$wpdb->prefix}disc_tests`(`test_id`) ON DELETE CASCADE
        ) {$charset_collate};";

        $sql_answers = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}disc_answers` (
            `answer_id` INT NOT NULL AUTO_INCREMENT,
            `question_id` INT NOT NULL,
            `answer_text` TEXT NOT NULL,
            `score_d_adapted` INT NOT NULL,
            `score_i_adapted` INT NOT NULL,
            `score_s_adapted` INT NOT NULL,
            `score_c_adapted` INT NOT NULL,
            `score_d_natural` INT NOT NULL,
            `score_i_natural` INT NOT NULL,
            `score_s_natural` INT NOT NULL,
            `score_c_natural` INT NOT NULL,
            PRIMARY KEY (`answer_id`),
            FOREIGN KEY (`question_id`) REFERENCES `{$wpdb->prefix}disc_questions`(`question_id`) ON DELETE CASCADE
        ) {$charset_collate};";

        $sql_results = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}disc_results` (
            `result_id` INT NOT NULL AUTO_INCREMENT,
            `user_id` INT NOT NULL,
            `test_id` INT NOT NULL,
            `result_data` TEXT NOT NULL,
            `scores_adapted` TEXT NOT NULL,
            `scores_natural` TEXT NOT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`result_id`),
            FOREIGN KEY (`user_id`) REFERENCES `{$wpdb->prefix}disc_users`(`user_id`) ON DELETE CASCADE,
            FOREIGN KEY (`test_id`) REFERENCES `{$wpdb->prefix}disc_tests`(`test_id`) ON DELETE CASCADE
        ) {$charset_collate};";

        $sql_reports = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}disc_reports` (
            `report_id` INT NOT NULL AUTO_INCREMENT,
            `user_id` INT NOT NULL,
            `result_id` INT NOT NULL,
            `report_data` LONGTEXT,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`report_id`),
            FOREIGN KEY (`user_id`) REFERENCES `{$wpdb->prefix}disc_users`(`user_id`) ON DELETE CASCADE,
            FOREIGN KEY (`result_id`) REFERENCES `{$wpdb->prefix}disc_results`(`result_id`) ON DELETE CASCADE
        ) {$charset_collate};";

        $sql_report_data = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}disc_report_data` (
            `data_id` INT NOT NULL AUTO_INCREMENT,
            `report_id` INT NOT NULL,
            `data_key` VARCHAR(255) NOT NULL,
            `data_value` TEXT NOT NULL,
            PRIMARY KEY (`data_id`),
            FOREIGN KEY (`report_id`) REFERENCES `{$wpdb->prefix}disc_reports`(`report_id`) ON DELETE CASCADE
        ) {$charset_collate};";

        $sql_personality_types = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}disc_personality_types` (
            `type_id` INT NOT NULL AUTO_INCREMENT,
            `type_name` VARCHAR(255) NOT NULL,
            `title` VARCHAR(255) NOT NULL,
            `adapted_description` TEXT NOT NULL,
            `natural_description` TEXT NOT NULL,
            `coaching_tips` TEXT NOT NULL,
            PRIMARY KEY (`type_id`)
        ) {$charset_collate};";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_users);
        dbDelta($sql_tests);
        dbDelta($sql_questions);
        dbDelta($sql_answers);
        dbDelta($sql_results);
        dbDelta($sql_reports);
        dbDelta($sql_report_data);
        dbDelta($sql_personality_types);
    }

    public static function drop_table() {
        global $wpdb;
        $wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}disc_users`");
        $wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}disc_tests`");
        $wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}disc_questions`");
        $wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}disc_answers`");
        $wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}disc_results`");
        $wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}disc_reports`");
        $wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}disc_report_data`");
        $wpdb->query("DROP TABLE IF EXISTS `{$wpdb->prefix}disc_personality_types`");
    }
}
?>
<?php
class DB_Manager {
    // ... (rest of the code)
}
?>
