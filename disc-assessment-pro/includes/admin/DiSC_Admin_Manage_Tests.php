<?php
require_once __DIR__ . '/DiSC_Admin_Base.php';

class DiSC_Admin_Manage_Tests extends DiSC_Admin_Base {
    public function __construct($wpdb) {
        parent::__construct($wpdb);
        add_action('admin_menu', array($this, 'add_menu_item'));
        add_action('admin_post_delete_test', array($this, 'delete_test'));
    } // End of import_json_file method

    public function add_menu_item($menu_title = 'Manage Tests', $menu_slug = 'disc_manage_tests', $capability = 'manage_options', $callback = null) {
        add_menu_page(
            $menu_title,
            'Tests',
            $capability,
            $menu_slug,
            array($this, 'display_tests_page'),
            'dashicons-welcome-learn-more',
            6
        );
    }

    public function display_tests_page() {
        // Display any messages
        if (isset($_GET['import_status'])) {
            if ($_GET['import_status'] === 'success') {
                echo '<div class="notice notice-success is-dismissible"><p>JSON imported successfully!</p></div>';
            } elseif ($_GET['import_status'] === 'error') {
                echo '<div class="notice notice-error is-dismissible"><p>Error importing JSON.</p></div>';
            }
        }
        // This block is misplaced and should be removed
        $tests = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}disc_tests");

        // Handle the custom action
        if (isset($_GET['action']) && ($_GET['action'] === 'create_new_test' || $_GET['action'] === 'edit' && isset($_GET['test_id']))) {
            $create_test = new Create_Test($wpdb);
            $create_test->render();
            return;
        }

        ?>
        <div class="wrap">
            <h1>Manage Tests</h1>
            <a href="<?php echo admin_url('admin.php?page=disc_manage_tests&action=create_new_test'); ?>" class="page-title-action">Create New Test</a>
            <button id="import-json" class="page-title-action">Import JSON</button>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Test Name</th>
                        <th>Description</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tests as $test): ?>
                        <tr>
                            <td><?php echo esc_html($test->test_id); ?></td>
                            <td><?php echo esc_html($test->test_name); ?></td>
                            <td><?php echo wp_kses_post($test->test_description); ?></td>
                            <td><?php echo esc_html($test->created_at); ?></td>
                            <td><?php echo esc_html($test->updated_at); ?></td>
                            <td>
                                <a href="<?php echo admin_url('admin.php?page=disc_manage_tests&action=edit&test_id=' . $test->test_id); ?>" class="button">Edit</a>
                                <a href="<?php echo admin_url('admin-post.php?action=delete_test&test_id=' . $test->test_id); ?>" class="button" onclick="return confirm('Are you sure you want to delete this test?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    public function import_json_file() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        $json_data = file_get_contents($_FILES['json_file']['tmp_name']);
        $data = json_decode($json_data, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            wp_redirect(admin_url('admin.php?page=disc_manage_tests&import_status=error'));
            exit;
        }

        global $wpdb;
        foreach ($data as $test) {
            $wpdb->insert("{$wpdb->prefix}disc_tests", array(
                'test_name' => sanitize_text_field($test['test_name']),
                'test_description' => sanitize_textarea_field($test['test_description']),
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql'),
            ));
        }

        wp_redirect(admin_url('admin.php?page=disc_manage_tests&import_status=success'));
        exit;
    }
        global $wpdb;

        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        if (isset($_GET['test_id'])) {
            $test_id = intval($_GET['test_id']);
            $wpdb->delete("{$wpdb->prefix}disc_tests", array('test_id' => $test_id));
        }

        wp_redirect(admin_url('admin.php?page=disc_manage_tests'));
        exit;
    }
}

global $wpdb;
$tests_manager = new DiSC_Admin_Manage_Tests($wpdb);
?>
