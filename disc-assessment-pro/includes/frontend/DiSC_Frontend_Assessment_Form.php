<?php
require_once __DIR__ . '/DiSC_Frontend_Base.php';

class DiSC_Frontend_Assessment_Form extends DiSC_Frontend_Base {
    public function __construct($wpdb) {
        parent::__construct($wpdb);
        add_action('admin_post_submit_test', array($this, 'handle_submission'));
    }

    public function handle_submission() {
        global $wpdb;

        if (!isset($_POST['test_id']) || !isset($_POST['action']) || $_POST['action'] !== 'submit_test') {
            error_log('Invalid submission: test_id or action is missing in handle_submission method.');
            wp_die(__('Invalid submission.'));
        }

        $test_id = intval($_POST['test_id']);
        $user_answers = [];

        foreach ($_POST as $key => $value) {
            if (strpos($key, 'question_') === 0) {
                $question_id = intval(str_replace('question_', '', $key));
                $user_answers[$question_id] = intval($value);
            }
        }

        // Here you can process the answers, save them to the database, or generate results

        wp_redirect(admin_url('admin.php?page=disc_view_results&test_id=' . $test_id));
        exit;
    }
}

global $wpdb;
$assessment_form = new DiSC_Frontend_Assessment_Form($wpdb);
?>
