<?php
class DiSC_Settings {
    private $wpdb;

    public function __construct($wpdb) {
        $this->wpdb = $wpdb;
    }

    public function add_settings() {
        // Add settings for the plugin
    }
}
?>
