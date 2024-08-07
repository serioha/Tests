<?php
/*
Plugin Name: DiSC Assessment Pro
Description: A plugin to manage DiSC assessments and generate detailed reports
Version: 1.0.0
*/

require_once __DIR__ . '/includes/admin/DiSC_Admin_Base.php';
require_once __DIR__ . '/includes/admin/DiSC_Admin_Manage_Tests.php';
require_once __DIR__ . '/includes/admin/DiSC_Admin_Manage_Personality_Types.php';
require_once __DIR__ . '/includes/admin/DiSC_Admin_View_Results.php';
require_once __DIR__ . '/includes/admin/DiSC_Admin_Manage_Reports.php';
require_once __DIR__ . '/includes/admin/DiSC_Admin_Manage_Questions.php';
require_once __DIR__ . '/includes/core/DB_Manager.php';
require_once __DIR__ . '/includes/core/DiSC_Report_Generator.php';
require_once __DIR__ . '/includes/core/DiSC_Settings.php';
require_once __DIR__ . '/includes/frontend/DiSC_Frontend_Assessment_Form.php';
require_once __DIR__ . '/includes/frontend/DiSC_Frontend_Base.php';
require_once __DIR__ . '/includes/frontend/DiSC_Frontend_Result_Display.php';
require_once __DIR__ . '/includes/frontend/DiSC_Frontend_Display_Tests.php';
require_once __DIR__ . '/includes/models/DiSC_Reporting.php';
require_once __DIR__ . '/includes/models/DiSC_Tests.php';
require_once __DIR__ . '/includes/models/DiSC_User.php';
require_once __DIR__ . '/includes/admin/create-personality-type.php';
require_once __DIR__ . '/includes/admin/create-test.php';
require_once __DIR__ . '/includes/admin/create-question.php';

class DiSC_Plugin {
    private $db_manager;
    private $admin_base;
    private $settings;
    private $report_generator;
    private $user;
    private $tests;
    private $reporting;
    private $assessment_form;
    private $result_display;

    public function __construct() {
        // Initialize the plugin
        global $wpdb;
        $this->db_manager = new DB_Manager($wpdb);
        $this->admin_base = new DiSC_Admin_Base($wpdb);
        $this->settings = new DiSC_Settings($wpdb);
        $this->report_generator = new DiSC_Report_Generator($wpdb);
        $this->user = new DiSC_User($wpdb);
        //$this->tests = new DiSC_Admin_Manage_Tests($wpdb);
        $this->reporting = new DiSC_Reporting($wpdb);
        $this->assessment_form = new DiSC_Frontend_Assessment_Form($wpdb);
        $this->result_display = new DiSC_Frontend_Result_Display($wpdb);

        // Register activation and deactivation hooks
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));

        // Add admin menu items
        add_action('admin_menu', array($this, 'register_admin_menu'));

        // Add settings
        $this->settings->add_settings();

        // Register shortcode for assessment form
        add_shortcode('disc_assessment_form', array($this->assessment_form, 'render_form'));

        // Handle form submission
        add_action('wp_ajax_disc_assessment_submit', array($this->assessment_form, 'handle_submission'));

        // Enqueue admin scripts and styles
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }

    public function register_admin_menu() {
        global $wpdb; // Ensure $wpdb is defined here
        $this->admin_base->add_menu_item('DiSC Assessment', 'disc-assessment', 'manage_options', array($this, 'render_menu'));

        // Add submenus
        $this->admin_base->add_submenu_item('disc-assessment', 'Manage Personality Types', 'manage-personality-types', 'manage_options', array(new DiSC_Admin_Manage_Personality_Types($wpdb), 'display_personality_types_page'));
        $this->admin_base->add_submenu_item('disc-assessment', 'Manage Tests', 'manage-tests', 'manage_options', array(new DiSC_Admin_Manage_Tests($wpdb), 'display_tests_page'));
        $this->admin_base->add_submenu_item('disc-assessment', 'View Results', 'view-results', 'manage_options', array(new DiSC_Admin_View_Results($wpdb), 'render_results_view'));
        $this->admin_base->add_submenu_item('disc-assessment', 'Manage Reports', 'manage-reports', 'manage_options', array(new DiSC_Admin_Manage_Reports($wpdb), 'render_report_manager'));
    }

    public function render_menu() {
        echo '<h1>DiSC Assessment</h1>';
        echo '<p>Welcome to the DiSC Assessment plugin!</p>';
    }

    public function activate() {
        $this->db_manager->create_table();
    }

    public function deactivate() {
        $this->db_manager->drop_table();
    }

/*    public function enqueue_admin_scripts($hook) {
        error_log('Hook from `disc-assessment-pro.php`: ' . $hook);
        // Enqueue the script only on the specific admin page
        //if ($hook == 'toplevel_page_disc-assessment' || $hook == 'disc-assessment_page_manage-personality-types' || $hook == 'disc-assessment_page_manage-tests' || $hook == 'disc-assessment_page_view-results' || $hook == 'disc-assessment_page_manage-reports' || $hook == 'disc-assessment_page_create-question' || $hook == 'admin_page_disc_manage_questions') {
        if ($hook == 'admin_page_disc_manage_questions') {
            wp_enqueue_style('disc-assessment-admin-style', plugin_dir_url(__FILE__) . 'assets/css/style.css');
            wp_enqueue_script('disc-assessment-script', plugin_dir_url(__FILE__) . 'assets/js/script.js', array('jquery'), null, true);
        }
    }*/

    function enqueue_admin_scripts($hook) {
        error_log('Hook from `disc-assessment-pro.php`: ' . $hook);
        if ($hook === 'admin_page_disc_manage_questions') {
            $action = isset($_GET['action']) ? $_GET['action'] : '';
            if ($action === 'create_new_question' || $action === 'edit') {
                // Enqueue scripts for Create/Edit Question page
                wp_enqueue_script('disc-assessment-create-edit', plugin_dir_url(__FILE__) . 'assets/js/script.js', array('jquery'), null, true);
            } else {
                // Enqueue scripts for Manage Questions page
                //wp_enqueue_script('disc-assessment-manage', plugin_dir_url(__FILE__) . 'assets/css/style.css');
                wp_enqueue_style('disc-assessment-admin-style', plugin_dir_url(__FILE__) . 'assets/css/style.css');
            }
        }
    }
    
}

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

add_action('plugins_loaded', function() {
    new DiSC_Plugin();
    global $wpdb;
    new DiSC_Frontend_Display_Tests($wpdb);
});
