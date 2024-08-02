<?php
require_once __DIR__ . '/DiSC_Frontend_Base.php';

class DiSC_Frontend_Display_Tests extends DiSC_Frontend_Base {
    public function __construct($wpdb) {
        parent::__construct($wpdb);
        add_shortcode('display_tests', array($this, 'render_tests'));
    }

    public function render_tests() {
        global $wpdb;
        $tests = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}disc_tests");

        if (empty($tests)) {
            return '<p>No tests available.</p>';
        }

        $output = '<div class="disc-tests">';
        foreach ($tests as $test) {
            $output .= '<div class="disc-test">';
            $output .= '<h2>' . esc_html($test->test_name) . '</h2>';
            $output .= '<p>' . esc_html($test->test_description) . '</p>';
            $output .= '<a href="' . esc_url(admin_url('admin.php?page=disc_view_results&test_id=' . $test->test_id)) . '" class="button">View Results</a>';
            $output .= '</div>';
        }
        $output .= '</div>';

        return $output;
    }
}

global $wpdb;
$tests_display = new DiSC_Frontend_Display_Tests($wpdb);
?>
