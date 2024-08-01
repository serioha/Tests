<?php
require_once __DIR__ . '/DiSC_Admin_Base.php';

class Create_Test extends DiSC_Admin_Base {
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

        $test_id = isset($_GET['test_id']) ? intval($_GET['test_id']) : 0;
        $table_name = $wpdb->prefix . 'disc_tests';

        $test_name = '';
        $test_description = '';

        if ($test_id > 0) {
            $test = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE test_id = %d", $test_id));
            if ($test) {
                $test_name = $test->test_name ?? '';
                $test_description = $test->test_description ?? '';
            }
        }

        ?>
        <div class="wrap" style="clear: both;">
            <h1><?php echo $test_id > 0 ? 'Edit Test' : 'Create New Test'; ?></h1>

            <a href="<?php echo admin_url('admin.php?page=disc_manage_tests'); ?>" class="button">Back to Tests</a>

            <form method="post">
                <input type="hidden" name="action" value="<?php echo $test_id > 0 ? 'edit' : 'add'; ?>">
                <?php if ($test_id > 0): ?>
                    <input type="hidden" name="test_id" value="<?php echo esc_attr($test_id); ?>">
                <?php endif; ?>

                <h3>Test Name</h3>
                <input type="text" name="test_name" value="<?php echo esc_attr($test_name); ?>" style="width: 100%;" required>

                <h3>Test Description</h3>
                <?php
                wp_editor(
                    $test_description,
                    'test_description',
                    array(
                        'wpautop'       => true,
                        'media_buttons' => true,
                        'textarea_name' => 'test_description',
                        'textarea_rows' => 10,
                        'teeny'         => false,
                        'quicktags'     => true
                    )
                );
                ?>

                <button type="submit" class="button button-primary"><?php echo $test_id > 0 ? 'Save Changes' : 'Add Test'; ?></button>
            </form>
        </div>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = sanitize_text_field($_POST['action']);

            $test_name = sanitize_text_field($_POST['test_name']);
            $test_description = wp_kses_post($_POST['test_description']);
            $current_time = current_time('mysql');

            if ($action === 'add') {
                $wpdb->insert(
                    $table_name,
                    [
                        'test_name' => $test_name,
                        'test_description' => $test_description,
                        'created_at' => $current_time,
                        'updated_at' => $current_time,
                    ],
                    ['%s', '%s', '%s', '%s']
                );
            } elseif ($action === 'edit') {
                $test_id = intval($_POST['test_id']);

                $wpdb->update(
                    $table_name,
                    [
                        'test_name' => $test_name,
                        'test_description' => $test_description,
                        'updated_at' => $current_time,
                    ],
                    ['test_id' => $test_id],
                    ['%s', '%s', '%s'],
                    ['%d']
                );
            }

            echo '<script type="text/javascript">window.location.href="' . admin_url('admin.php?page=disc_manage_tests') . '";</script>';
            exit;
        }
    }
}
?>
