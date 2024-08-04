<?php
require_once __DIR__ . '/DiSC_Admin_Base.php';

class Create_Question extends DiSC_Admin_Base {
    public function __construct($wpdb) {
        parent::__construct($wpdb);
        add_action('admin_init', array($this, 'check_user_capability'));
    }

    public function check_user_capability() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
    }

    public function render() {
        global $wpdb;

        $question_id = isset($_GET['question_id']) ? intval($_GET['question_id']) : 0;
        $test_id = isset($_GET['test_id']) ? intval($_GET['test_id']) : 0; // Get test_id from the URL
        $table_name = $wpdb->prefix . 'disc_questions';

        $question_text = '';

        if ($question_id > 0) {
            $question = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE question_id = %d", $question_id));
            if ($question) {
                $question_text = $question->question_text ?? '';
            }
        }

        ?>
        <div class="wrap">
            <h1><?php echo $question_id > 0 ? 'Edit Question' : 'Create New Question'; ?></h1>

            <a href="<?php echo admin_url('admin.php?page=disc_manage_questions&test_id=' . $test_id); ?>" class="button">Back to Questions</a>

            <form method="post">
                <input type="hidden" name="action" value="<?php echo $question_id > 0 ? 'edit' : 'add'; ?>">
                <?php if ($question_id > 0): ?>
                    <input type="hidden" name="question_id" value="<?php echo esc_attr($question_id); ?>">
                <?php endif; ?>

                <h3>Question Text</h3>
                <textarea name="question_text" style="width: 100%;" required><?php echo esc_textarea($question_text); ?></textarea>

                <button type="submit" class="button button-primary"><?php echo $question_id > 0 ? 'Save Changes' : 'Add Question'; ?></button>
            </form>
        </div>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = sanitize_text_field($_POST['action']);
            $question_text = sanitize_textarea_field($_POST['question_text']);
            $current_time = current_time('mysql');

            if ($action === 'add') {
                $wpdb->insert(
                    $table_name,
                    [
                        'test_id' => $test_id, // Use the test_id from the URL
                        'question_text' => $question_text,
                        'created_at' => $current_time,
                        'updated_at' => $current_time,
                    ],
                    ['%d', '%s', '%s', '%s']
                );
            } elseif ($action === 'edit') {
                $question_id = intval($_POST['question_id']);

                $wpdb->update(
                    $table_name,
                    [
                        'question_text' => $question_text,
                        'updated_at' => $current_time,
                    ],
                    ['question_id' => $question_id],
                    ['%s', '%s'],
                    ['%d']
                );
            }

            echo '<script type="text/javascript">window.location.href="' . admin_url('admin.php?page=disc_manage_questions&test_id=' . $test_id) . '";</script>';
            exit;
        }
    }

    public function view_question() {
        global $wpdb;

        $question_id = isset($_GET['question_id']) ? intval($_GET['question_id']) : 0;
        $table_name = $wpdb->prefix . 'disc_questions';

        if ($question_id > 0) {
            $question = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE question_id = %d", $question_id));
            if ($question) {
                ?>
                <div class="wrap">
                    <h1>View Question</h1>
                    <p><strong>Question ID:</strong> <?php echo esc_html($question->question_id); ?></p>
                    <p><strong>Question Text:</strong> <?php echo esc_html($question->question_text); ?></p>
                    <a href="<?php echo admin_url('admin.php?page=disc_manage_questions&test_id=' . $question->test_id); ?>" class="button">Back to Questions</a>
                </div>
                <?php
            } else {
                echo '<h1>Question not found.</h1>';
            }
        } else {
            echo '<h1>Invalid Question ID.</h1>';
        }
    }
}
?>
