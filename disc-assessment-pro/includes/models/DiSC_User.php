<?php
class DiSC_User {
    private $wpdb;

    public function __construct($wpdb) {
        $this->wpdb = $wpdb;
    }

    public function save_user_data() {
        // Save user data to the database
    }
}
?>
