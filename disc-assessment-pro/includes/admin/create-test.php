<?php
require_once __DIR__ . '/DiSC_Admin_Base.php';

class Create_Test extends DiSC_Admin_Base {
    public function __construct($wpdb) {
        parent::__construct($wpdb);
    }

    public function render() {
        // Check user capability
        if (!current_user_can('manage_options')) {
            return $this->render_access_denied();
        }

        // Handle form submission
        if (isset($_POST['save_test'])) {
            $test_title = sanitize_text_field($_POST['test_title']);
            $test_description = sanitize_textarea_field($_POST['test_description']);

            global $wpdb;
            $wpdb->insert("{$wpdb->prefix}disc_tests", array(
                'test_name' => $test_title,
                'test_description' => $test_description,
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql')
            ));

            // Redirect to the tests page after saving
            wp_redirect(admin_url('admin.php?page=disc_manage_tests'));
            exit;
        }

        echo '<h1>Create New Test</h1>';
        echo '<form method="post" action="">';
        echo '<label for="test_title">Test Title</label>';
        echo '<input type="text" name="test_title" required />';
        echo '<label for="test_description">Test Description</label>';
        echo '<textarea name="test_description" required></textarea>';
        echo '<input type="submit" name="save_test" value="Save Test" />';
        echo '<a href="' . admin_url('admin.php?page=disc_manage_tests') . '" class="button">Back to Tests</a>';
        echo '</form>';
    }
}

global $wpdb;
$create_test = new Create_Test($wpdb);
$create_test->render();
?>
