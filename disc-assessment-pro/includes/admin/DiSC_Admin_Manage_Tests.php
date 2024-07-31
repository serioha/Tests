<?php
class DiSC_Admin_Manage_Tests extends DiSC_Admin_Base {
    public function __construct($wpdb) {
        parent::__construct($wpdb);
        add_action('admin_init', array($this, 'check_user_capability'));
    }

    public function check_user_capability() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
    }

    public function add_menu_item($menu_title, $menu_slug, $capability, $callback = null) {
        add_submenu_page('DiSC Assessment', 'Manage Tests', 'Manage Tests', $capability, $menu_slug, array($this, 'render_test_manager'));
        // Register hidden submenu for creating a new test
        add_submenu_page(null, 'Create New Test', 'Create New Test', $capability, 'disc_create_test', array(new Create_Test($this->wpdb), 'render'));
        // Register hidden submenu for editing a test
        add_submenu_page(null, 'Edit Test', 'Edit Test', $capability, 'disc_edit_test', array($this, 'render_edit_test'));
    }

    public function render_test_manager() {
        // Handle actions
        if (isset($_GET['action'])) {
            if ($_GET['action'] == 'create_test') {
                return $this->render_create_test();
            } elseif ($_GET['action'] == 'edit_test' && isset($_GET['test_id'])) {
                return $this->render_edit_test($_GET['test_id']);
            } elseif ($_GET['action'] == 'manage_questions' && isset($_GET['test_id'])) {
                return $this->render_manage_questions($_GET['test_id']);
            }
        }

        // Display the tests table
        $this->display_tests_table();
    }

    private function display_tests_table() {
        global $wpdb;
        $tests = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}disc_tests");

        echo '<h1>Manage Tests</h1>';
        echo '<a href="' . admin_url('admin.php?page=disc_manage_tests&action=create_test') . '" class="button">Create New Test</a>';
        echo '<a href="#" class="button">Import JSON</a>';
        echo '<a href="#" class="button">Export JSON</a>';
        echo '<table class="widefat fixed">';
        echo '<thead><tr><th><input type="checkbox" /></th><th>ID</th><th>Title</th><th>Description</th><th>Shortcode</th><th>No of Questions</th><th>Created on</th><th>Updated on</th><th>View Results</th><th>Manage Questions</th><th>Actions</th></tr></thead>';
        echo '<tbody>';

        foreach ($tests as $test) {
            echo '<tr>';
            echo '<td><input type="checkbox" value="' . $test->test_id . '" /></td>';
            echo '<td>' . $test->test_id . '</td>';
            echo '<td>' . $test->test_name . '</td>';
            echo '<td>' . $test->test_description . '</td>';
            echo '<td>[test_shortcode_' . $test->test_id . ']</td>';
            echo '<td>' . $this->get_question_count($test->test_id) . '</td>';
            echo '<td>' . $test->created_at . '</td>';
            echo '<td>' . $test->updated_at . '</td>';
            echo '<td><a href="' . admin_url('admin.php?page=disc_view_results&test_id=' . $test->test_id) . '">View Results</a></td>';
            echo '<td><a href="' . admin_url('admin.php?page=disc_manage_tests&action=manage_questions&test_id=' . $test->test_id) . '">Manage Questions</a></td>';
            echo '<td><a href="' . admin_url('admin.php?page=disc_manage_tests&action=edit_test&test_id=' . $test->test_id) . '">Edit</a> | <a href="' . admin_url('admin.php?page=disc_manage_tests&action=delete_test&test_id=' . $test->test_id) . '">Delete</a></td>';
            echo '</tr>';
        }

        echo '</tbody></table>';
    }

    private function get_question_count($test_id) {
        global $wpdb;
        return $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}disc_questions WHERE test_id = %d", $test_id));
    }

    private function render_edit_test($test_id) {
        global $wpdb;
        $test = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}disc_tests WHERE test_id = %d", $test_id));

        if ($test) {
            echo '<h1>Edit Test: ' . esc_html($test->test_name) . '</h1>';
            echo '<form method="post" action="">';
            echo '<input type="hidden" name="test_id" value="' . $test_id . '" />';
            echo '<label for="test_title">Test Title</label>';
            echo '<input type="text" name="test_title" value="' . esc_html($test->test_name) . '" required />';
            echo '<label for="test_description">Test Description</label>';
            echo '<textarea name="test_description" required>' . esc_html($test->test_description) . '</textarea>';
            echo '<input type="submit" name="update_test" value="Update Test" />';
            echo '<a href="' . admin_url('admin.php?page=disc_manage_tests') . '" class="button">Back to Tests</a>';
            echo '</form>';
        } else {
            echo '<p>Test not found.</p>';
        }
    }

    private function render_manage_questions($test_id) {
        // Implement the manage questions functionality here
        echo '<h1>Manage Questions for Test ID: ' . $test_id . '</h1>';
        echo '<a href="' . admin_url('admin.php?page=disc_manage_tests') . '" class="button">Back to Tests</a>';
        // Display questions table here
    }
}
?>
