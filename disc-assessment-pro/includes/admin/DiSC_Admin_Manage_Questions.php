<?php
require_once __DIR__ . '/DiSC_Admin_Base.php';

class DiSC_Admin_Manage_Questions extends DiSC_Admin_Base {
    public function __construct($wpdb) {
        parent::__construct($wpdb);
        add_action('admin_menu', array($this, 'add_menu_item'));
        add_action('admin_post_delete_question', array($this, 'delete_question'));
    }

    public function delete_question() {
        global $wpdb;

        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        error_log('Attempting to delete question ID: ' . intval($_GET['question_id'])); // Log delete action
        if (!isset($_GET['test_id'])) {
            error_log('Test ID is missing in delete_question method.');
            wp_die(__('Test ID is missing.'));
        }

        $test_id = intval($_GET['test_id']);
        if ($test_id === 0) {
            error_log('Test ID is zero in delete_question method.');
            wp_die(__('Test ID is invalid.'));
        }

        if (isset($_GET['question_id'])) {
            $question_id = intval($_GET['question_id']);
            $wpdb->delete("{$wpdb->prefix}disc_questions", array('question_id' => $question_id));
        } else {
            error_log('Question ID is missing in delete_question method.');
            wp_die(__('Question ID is missing.'));
        }

        wp_redirect(admin_url('admin.php?page=disc_manage_questions&test_id=' . $test_id));
        exit;
    }

    public function add_menu_item($menu_title = 'Manage Questions', $menu_slug = 'disc_manage_questions', $capability = 'manage_options', $callback = null) {
        add_submenu_page(
            'disc_manage_tests',
            $menu_title,
            $menu_title,
            $capability,
            $menu_slug,
            array($this, 'display_questions_page')
        );
    }

    public function display_questions_page() {
        global $wpdb;

        // Handle the custom action for creating or editing questions
        if (isset($_GET['action'])) {
            $create_question = new Create_Question($wpdb);
            if ($_GET['action'] === 'create_new_question') {
                $create_question->render(); // Call the render method to display the form
                return;
            } elseif ($_GET['action'] === 'edit' && isset($_GET['question_id'])) {
                $create_question->render(); // Call the render method for editing
                return;
            }
        }

        if (!isset($_GET['test_id'])) {
            wp_die(__('Test ID is missing.'));
        }
        $test_id = intval($_GET['test_id']);
        if ($test_id === 0) {
            wp_die(__('Test ID is missing.'));
        }
        $test = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}disc_tests WHERE test_id = %d", $test_id));

        if (!$test) {
            echo '<h1>Test not found.</h1>';
            return;
        }

        $questions_query = $wpdb->prepare("SELECT * FROM {$wpdb->prefix}disc_questions WHERE test_id = %d", $test_id);
        $questions = $wpdb->get_results($questions_query);

        ?>
        <div class="wrap">
            <h1>Manage Questions for Test: <?php echo esc_html($test->test_name); ?></h1>
            <a href="<?php echo admin_url('admin.php?page=disc_manage_questions&test_id=' . $test_id . '&action=create_new_question'); ?>" class="button">Create New Question</a>
            <a href="<?php echo admin_url('admin.php?page=disc_manage_tests'); ?>" class="button">Back to Tests</a>

            <h2>Existing Questions</h2>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><input type="checkbox" /></th>
                        <th>ID</th>
                        <th>Question Text</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($questions)): ?>
                        <?php foreach ($questions as $question): ?>
                            <tr>
                                <td><input type="checkbox" /></td>
                                <td><?php echo esc_html($question->question_id); ?></td>
                                <td><?php echo esc_html($question->question_text); ?></td>
                                <td>
                                    <a href="<?php echo admin_url('admin.php?page=disc_manage_questions&test_id=' . $test_id . '&action=edit&question_id=' . $question->question_id); ?>" class="button">Edit</a>
                                    <a href="<?php echo admin_url('admin-post.php?action=delete_question&question_id=' . $question->question_id . '&test_id=' . $test_id); ?>" class="button" onclick="return confirm('Are you sure you want to delete this question?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">No questions found for this test.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
    }
}

global $wpdb;
$questions_manager = new DiSC_Admin_Manage_Questions($wpdb);
?>
