<?php
require_once __DIR__ . '/DiSC_Frontend_Base.php';

class DiSC_Frontend_Display_Tests extends DiSC_Frontend_Base {
    public function __construct($wpdb) {
        parent::__construct($wpdb);
        add_shortcode('display_tests', array($this, 'render_tests'));
    }

    public function render_tests($atts) {
        global $wpdb;
        $atts = shortcode_atts(array(
            'id' => '',
        ), $atts);

        if (!empty($atts['id'])) {
            $test = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}disc_tests WHERE test_id = %d", intval($atts['id'])));
            if (!$test) {
                error_log('Test not found for ID: ' . intval($atts['id']));
                return '<p>Test not found.</p>';
            }

            $questions = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}disc_questions WHERE test_id = %d", intval($test->test_id)));
            if (empty($questions)) {
                error_log('No questions available for test ID: ' . intval($test->test_id));
                return '<p>No questions available for this test.</p>';
            }

            $output = '<div class="disc-test">';
            $output .= '<h2>' . esc_html($test->test_name) . ' (ID: ' . esc_html($test->test_id) . ')</h2>';
            $output .= '<p>' . esc_html($test->test_description) . '</p>';

            $output .= '<form method="post" action="' . esc_url(admin_url('admin-post.php')) . '">';
            foreach ($questions as $question) {
                $output .= '<div class="question">';
                $output .= '<p>' . esc_html($question->question_text) . '</p>';
                $answers = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}disc_answers WHERE question_id = %d", intval($question->question_id)));
                foreach ($answers as $answer) {
                    $output .= '<label><input type="radio" name="question_' . esc_html($question->question_id) . '" value="' . esc_html($answer->answer_id) . '"> ' . esc_html($answer->answer_text) . '</label><br>';
                }
                $output .= '</div>';
            }
            $output .= '<input type="hidden" name="action" value="submit_test">';
            $output .= '<input type="hidden" name="test_id" value="' . esc_html($test->test_id) . '">';
            $output .= '<button type="submit" class="button">Submit</button>';
            $output .= '</form>';
            $output .= '</div>';
        } else {
            $tests = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}disc_tests");
            if (empty($tests)) {
                return '<p>No tests available.</p>';
            }

            $output = '<div class="disc-tests">';
            foreach ($tests as $test) {
                $output .= '<div class="disc-test">';
                $output .= '<h2>' . esc_html($test->test_name) . ' (ID: ' . esc_html($test->test_id) . ')</h2>';
                $output .= '<p>' . esc_html($test->test_description) . '</p>';
                $output .= '<a href="' . esc_url(admin_url('admin.php?page=disc_view_results&test_id=' . $test->test_id)) . '" class="button">View Results</a>';
                $output .= '</div>';
            }
            $output .= '</div>';
        }

        return $output;
    }
}

global $wpdb;
$tests_display = new DiSC_Frontend_Display_Tests($wpdb);
?>
