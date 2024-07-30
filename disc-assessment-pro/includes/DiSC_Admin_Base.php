<?php
abstract class DiSC_Admin_Base extends DiSC_Plugin_Base {
    public function __construct() {
    }

    public function add_menu_item() {
        add_action('admin_menu', array($this, 'render_menu'));
    }

    public function render_menu() {
        // Add a menu item for the plugin
    }

}

require_once __DIR__ . '/DiSC_Plugin_Base.php';
?>