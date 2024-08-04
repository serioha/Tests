<?php
class DiSC_Admin_View_Results extends DiSC_Admin_Base {
    public function __construct($wpdb) {
        parent::__construct($wpdb);
    }

    public function add_menu_item($menu_title, $menu_slug, $capability, $callback = null) {
        add_submenu_page('DiSC Assessment', 'View Results', 'View Results', $capability, $menu_slug, array($this, 'render_results_view'));
    }

    public function render_results_view() {
        // Render the results interface
		error_log('Rendering results view interface.');
		echo '<h1>View Results</h1>';
        echo '<p>This is a test.</p>';
    }
}
?>
