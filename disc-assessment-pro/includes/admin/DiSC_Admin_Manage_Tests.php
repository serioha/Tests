<?php
require_once __DIR__ . '/DiSC_Admin_Base.php';

class DiSC_Admin_Manage_Tests extends DiSC_Admin_Base {
    public function __construct($wpdb) {
        parent::__construct($wpdb);
        add_action('admin_menu', array($this, 'add_menu_item'));
        add_action('admin_post_delete_test', array($this, 'delete_test'));
        add_action('admin_post_import_json', array($this, 'import_json_file')); // Hook for importing JSON
    }

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
        global $wpdb; // Define $wpdb here
        // Display any messages
        if (isset($_GET['import_status'])) {
            if ($_GET['import_status'] === 'success') {
                echo '<div class="notice notice-success is-dismissible"><p>JSON imported successfully!</p></div>';
            } elseif ($_GET['import_status'] === 'error') {
                echo '<div class="notice notice-error is-dismissible"><p>Error importing JSON.</p></div>';
            }
        }

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
            <input type="file" id="json-file-input" style="display: none;" accept=".json" />
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><input type="checkbox" /></th>
                        <th>ID</th>
                        <th>Test Name</th>
                        <th>Shortcodes</th>
                        <th>No of Questions</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Manage Questions</th>
                        <th>View Results</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tests as $test): ?>
                        <tr>
                            <td><input type="checkbox" /></td>
                            <td><?php echo esc_html($test->test_id); ?></td>
                            <td><?php echo esc_html($test->test_name); ?></td>
                            <td>[display_tests id="<?php echo esc_html($test->test_id); ?>"]</td>
                            <td><?php echo esc_html($test->no_of_questions); ?></td>
                            <td><?php echo esc_html($test->created_at); ?></td>
                            <td><?php echo esc_html($test->updated_at); ?></td>
                            <td><a href="<?php echo admin_url('admin.php?page=disc_manage_questions&test_id=' . $test->test_id); ?>" class="button">Manage Questions</a></td>
                            <td><a href="<?php echo admin_url('admin.php?page=disc_view_results&test_id=' . $test->test_id); ?>" class="button">View Results</a></td>
                            <td>
                                <a href="<?php echo admin_url('admin.php?page=disc_manage_tests&action=edit&test_id=' . $test->test_id); ?>" class="button">Edit</a>
                                <a href="<?php echo admin_url('admin-post.php?action=delete_test&test_id=' . $test->test_id); ?>" class="button" onclick="return confirm('Are you sure you want to delete this test?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <script>
            document.getElementById('import-json').addEventListener('click', function() {
                document.getElementById('json-file-input').click();
            });

            document.getElementById('json-file-input').addEventListener('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const formData = new FormData();
                    formData.append('json_file', file);
                    formData.append('action', 'import_json');

                    fetch('<?php echo admin_url('admin-post.php'); ?>', {
                        method: 'POST',
                        body: formData,
                    }).then(response => {
                        window.location.href = '<?php echo admin_url('admin.php?page=disc_manage_tests&import_status=success'); ?>';
                    }).catch(error => {
                        window.location.href = '<?php echo admin_url('admin.php?page=disc_manage_tests&import_status=error'); ?>';
                    });
                }
            });
        </script>
        <?php
    }

    public function import_json_file() {
        global $wpdb; // Define $wpdb here
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        if (!isset($_FILES['json_file']) || $_FILES['json_file']['error'] !== UPLOAD_ERR_OK) {
            wp_redirect(admin_url('admin.php?page=disc_manage_tests&import_status=error'));
            exit;
        }

        $json_data = file_get_contents($_FILES['json_file']['tmp_name']);
        $data = json_decode($json_data, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            wp_redirect(admin_url('admin.php?page=disc_manage_tests&import_status=error'));
            exit;
        }

        // Insert test data into the database
        $test_id = $wpdb->insert("{$wpdb->prefix}disc_tests", array(
            'test_name' => sanitize_text_field($data['test_name']),
            'test_description' => sanitize_textarea_field($data['test_description']),
            'created_at' => current_time('mysql'),
            'updated_at' => current_time('mysql'),
        ));

        // Insert questions associated with the test
        if ($test_id) {
            foreach ($data['questions'] as $question) {
                $question_id = $wpdb->insert("{$wpdb->prefix}disc_questions", array(
                    'test_id' => $wpdb->insert_id,
                    'question_text' => sanitize_text_field($question['question']),
                ));

                // Insert answers associated with the question
                if ($question_id) {
                    foreach ($question['answers'] as $answer) {
                        $wpdb->insert("{$wpdb->prefix}disc_answers", array(
                            'question_id' => $wpdb->insert_id,
                            'answer_text' => sanitize_text_field($answer['text']),
                            'score_d_adapted' => intval($answer['adapted_score']['D']),
                            'score_i_adapted' => intval($answer['adapted_score']['I']),
                            'score_s_adapted' => intval($answer['adapted_score']['S']),
                            'score_c_adapted' => intval($answer['adapted_score']['C']),
                            'score_d_natural' => intval($answer['natural_score']['D']),
                            'score_i_natural' => intval($answer['natural_score']['I']),
                            'score_s_natural' => intval($answer['natural_score']['S']),
                            'score_c_natural' => intval($answer['natural_score']['C']),
                        ));
                    }
                }
            }
        }

        wp_redirect(admin_url('admin.php?page=disc_manage_tests&import_status=success'));
        exit;
    }

    public function delete_test() {
        global $wpdb; // Define $wpdb here
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
