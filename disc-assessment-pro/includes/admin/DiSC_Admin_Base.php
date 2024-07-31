<?php
abstract class DiSC_Admin_Base {
    protected $wpdb;

    public function __construct($wpdb) {
        $this->wpdb = $wpdb;
    }

    // Base class for admin functionality
    public function add_menu_item($menu_title, $menu_slug, $capability, $callback) {
        add_submenu_page($menu_title, 'Manage Personality Types', 'Manage Personality Types', $capability, $menu_slug, $callback);
    }

    public function render_menu() {
        // Add submenu items
        add_submenu_page('disc-assessment', 'Manage Tests', 'Manage Tests', 'manage_options', 'disc-manage-tests', array($this, 'render_test_manager'));
        add_submenu_page('disc-assessment', 'Manage Personalities', 'Manage Personalities', 'manage_options', 'disc-manage-personality-types', array($this, 'render_personality_types_manager'));
    }

}
?>
