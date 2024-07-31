<?php
class DiSC_Admin_Base {
    protected $wpdb; // Define the property

    public function __construct($wpdb) {
        $this->wpdb = $wpdb;
    }

    public function add_menu_item($menu_title, $menu_slug, $capability, $callback = null) {
        // Check if the add_menu_page function exists
        if (function_exists('add_menu_page')) {
            // Check if user has the required capability
            if (current_user_can($capability)) {
                // Check if callback is provided for the main menu
                if ($callback) {
                    add_menu_page($menu_title, 'DiSC Assessment', $capability, $menu_slug, $callback);
                } else {
                    add_menu_page($menu_title, 'DiSC Assessment', $capability, $menu_slug, array($this, 'render_diSC_assessment'));
                }
            } else {
                add_menu_page($menu_title, 'DiSC Assessment', 'read', $menu_slug, array($this, 'render_access_denied'));
            }
        } else {
            error_log('add_menu_page function does not exist.');
        }
    }

    public function add_submenu_item($parent_slug, $menu_title, $menu_slug, $capability, $callback) {
        if (current_user_can($capability)) {
            add_submenu_page($parent_slug, $menu_title, $menu_title, $capability, $menu_slug, $callback);
        } else {
            add_submenu_page($parent_slug, $menu_title, $menu_title, 'read', $menu_slug, array($this, 'render_access_denied'));
        }
    }

    public function render_diSC_assessment() {
        // Render the DiSC assessment interface
        echo '<h1>DiSC Assessment</h1>';
        echo '<p>Welcome to the DiSC Assessment plugin!</p>';
    }

    public function render_access_denied() {
        echo '<h1>Access Denied</h1>';
        echo '<p>Sorry, you are not allowed to access this page.</p>';
    }
}
?>
