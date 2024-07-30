<?php
/*
Plugin Name: DiSC Assessment Pro
Description: A plugin to manage DiSC assessments and generate detailed reports
Version: 1.0
*/

class DiSC_Plugin {
    public function __construct() {
        // Initialize the plugin
    }
}

require_once __DIR__ . '/includes/DB_Manager.php';
require_once __DIR__ . '/includes/DiSC_Admin_Base.php';

// Remove this line, as DiSC_Admin_Base is an abstract class
$disc_plugin = new DiSC_Plugin();

class DiSC_Activator {
    public static function activate() {
        DB_Manager::create_table();
    }
}

class DiSC_Deactivator {
    public static function deactivate() {
        DB_Manager::drop_table();
    }
}
register_activation_hook(__FILE__, array('DiSC_Activator', 'activate'));
register_deactivation_hook(__FILE__, array('DiSC_Deactivator', 'deactivate'));

define('DISC_ASSESSMENT_PRO_VERSION', '1.0');
