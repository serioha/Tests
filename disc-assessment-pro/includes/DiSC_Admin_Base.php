<?php
abstract class DiSC_Admin_Base extends DiSC_Plugin_Base {
    public function __construct() {
        add_action('admin_menu', array($this, 'add_menu_item'));
    }

    public function add_menu_item() {
        // Add a menu item for the plugin
    }
}
?>
