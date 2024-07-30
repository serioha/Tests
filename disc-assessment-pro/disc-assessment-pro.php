<?php
/*
Plugin Name: DiSC Assessment Pro
Description: A plugin to manage DiSC assessments and generate detailed reports
Version: 1.0
*/

class DiSC_Plugin {
    public function __construct() {
        // Initialize the plugin
        global $wpdb;
         $this->db_manager = new DB_Manager($wpdb);
         $this->admin_base = new DiSC_Admin_Base($wpdb);
         $this->settings = new DiSC_Settings($wpdb);
         $this->report_generator = new DiSC_Report_Generator($wpdb);
         $this->user = new DiSC_User($wpdb);
         $this->tests = new DiSC_Tests($wpdb);
         $this->reporting = new DiSC_Reporting($wpdb);
         $this->assessment_form = new DiSC_Frontend_Assessment_Form($wpdb);
         $this->result_display = new DiSC_Frontend_Result_Display($wpdb);

         // Register activation and deactivation hooks
         register_activation_hook(__FILE__, array($this, 'activate'));
         register_deactivation_hook(__FILE__, array($this, 'deactivate'));

         // Add admin menu items
         $this->admin_base->add_menu_item();
                                                                                                                                              
         // Add settings
         $this->settings->add_settings();

         // Register shortcode for assessment form
         add_shortcode('disc_assessment_form', array($this->assessment_form, 'render_form'));

         // Handle form submission
         add_action('wp_ajax_disc_assessment_submit', array($this->assessment_form, 'handle_submission'));        
    }

    public function activate() {
        $this->db_manager->create_table();
    }
                                                                                                                                             
    public function deactivate() {
        $this->db_manager->drop_table();
    }
}

require_once __DIR__ . '/includes/DB_Manager.php';
require_once __DIR__ . '/includes/DiSC_Admin_Base.php';
require_once __DIR__ . '/includes/DiSC_Admin_Manage_Tests.php';
require_once __DIR__ . '/includes/DiSC_Admin_Manage_Personality_Types.php';
require_once __DIR__ . '/includes/DiSC_Admin_View_Results.php';
require_once __DIR__ . '/includes/core/DiSC_Report_Generator.php';
require_once __DIR__ . '/includes/core/DiSC_Settings.php';
require_once __DIR__ . '/includes/frontend/DiSC_Frontend_Assessment_Form.php';
require_once __DIR__ . '/includes/frontend/DiSC_Frontend_Base.php';
require_once __DIR__ . '/includes/frontend/DiSC_Frontend_Result_Display.php';
require_once __DIR__ . '/includes/models/DiSC_Reporting.php';
require_once __DIR__ . '/includes/models/DiSC_Tests.php';
require_once __DIR__ . '/includes/models/DiSC_User.php';

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

define('DISC_ASSESSMENT_PRO_VERSION', '1.0.0');
