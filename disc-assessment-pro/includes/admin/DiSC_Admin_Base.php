<?php
abstract class DiSC_Admin_Base {
    protected $wpdb;

    public function __construct($wpdb) {
        $this->wpdb = $wpdb;
    }

    // Base class for admin functionality
    public function add_menu_item() {
        add_action('admin_menu', array($this, 'render_menu'));
    }

    public function render_menu() {
        // Add a menu item for the plugin
    }
}
?>
