<?php
class DiSC_Admin_Base {
    protected $wpdb; // Define the property

    public function __construct($wpdb) {
        $this->wpdb = $wpdb;
    }

    public function add_menu_item($menu_title, $menu_slug, $capability, $callback = null) {
        // Check if the add_menu_page function exists
        if (function_exists('add_menu_page')) {
            // Check if callback is provided for the main menu
            if ($callback) {
                add_menu_page($menu_title, 'DiSC Assessment', $capability, $menu_slug, $callback);
            } else {
                add_menu_page($menu_title, 'DiSC Assessment', $capability, $menu_slug, array($this, 'render_diSC_assessment'));
            }
        } else {
            error_log('add_menu_page function does not exist.');
        }
    }

    public function add_submenu_item($parent_slug, $menu_title, $menu_slug, $capability, $callback) {
        add_submenu_page($parent_slug, $menu_title, $menu_title, $capability, $menu_slug, $callback);
    }

    public function render_diSC_assessment() {
        // Render the DiSC assessment interface
        echo '<h1>DiSC Assessment</h1>';
        echo '<p>Welcome to the DiSC Assessment plugin!</p>';
    }
}
?>
