<?php
require_once __DIR__ . '/DiSC_Admin_Base.php';

class DiSC_Admin_Manage_Questions extends DiSC_Admin_Base {
    public function __construct($wpdb) {
        parent::__construct($wpdb);
        add_action('admin_menu', array($this, 'add_menu_item'));
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

        $test_id = isset($_GET['test_id']) ? intval($_GET['test_id']) : 0;
        $test = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}disc_tests WHERE test_id = %d", $test_id));

        if (!$test) {
            echo '<h1>Test not found.</h1>';
            return;
        }

        $questions = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}disc_questions WHERE test_id = %d", $test_id));

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
                    <?php foreach ($questions as $question): ?>
                        <tr>
                            <td><input type="checkbox" /></td>
                            <td><?php echo esc_html($question->question_id); ?></td>
                            <td><?php echo esc_html($question->question_text); ?></td>
                            <td>
                                <a href="<?php echo admin_url('admin.php?page=disc_manage_questions&test_id=' . $test_id . '&action=edit&question_id=' . $question->question_id); ?>" class="button">Edit</a>
                                <a href="<?php echo admin_url('admin-post.php?action=delete_question&question_id=' . $question->question_id); ?>" class="button" onclick="return confirm('Are you sure you want to delete this question?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    }
}

global $wpdb;
$questions_manager = new DiSC_Admin_Manage_Questions($wpdb);
?>
