<?php
require_once __DIR__ . '/DiSC_Admin_Base.php';

class Create_Personality_Type extends DiSC_Admin_Base {
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

        $type_id = isset($_GET['type_id']) ? intval($_GET['type_id']) : 0;
        $table_name = $wpdb->prefix . 'disc_personality_types';

        $type_name = '';
        $title = '';
        $adapted_description = '';
        $natural_description = '';
        $coaching_tips = '';

        if ($type_id > 0) {
            $type = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE type_id = %d", $type_id));
            if ($type) {
                $type_name = $type->type_name ?? '';
                $title = $type->title ?? '';
                $adapted_description = $type->adapted_description ?? '';
                $natural_description = $type->natural_description ?? '';
                $coaching_tips = $type->coaching_tips ?? '';
            }
        }

        ?>
        <div class="wrap">
            <h1><?php echo $type_id > 0 ? 'Edit Personality Type' : 'Create New Personality Type'; ?></h1>

            <a href="<?php echo admin_url('admin.php?page=disc_manage_personality_types'); ?>" class="button">Back to Personality Types</a>

            <form method="post">
                <input type="hidden" name="action" value="<?php echo $type_id > 0 ? 'edit' : 'add'; ?>">
                <?php if ($type_id > 0): ?>
                    <input type="hidden" name="type_id" value="<?php echo esc_attr($type_id); ?>">
                <?php endif; ?>

                <h3>Type Name</h3>
                <input type="text" name="type_name" value="<?php echo esc_attr($type_name); ?>" style="width: 100%;" required>

                <h3>Title</h3>
                <input type="text" name="title" value="<?php echo esc_attr($title); ?>" style="width: 100%;" required>

                <h3>Adapted Description</h3>
                <?php
                wp_editor(
                    $adapted_description,
                    'adapted_description',
                    array(
                        'wpautop'       => true,
                        'media_buttons' => true,
                        'textarea_name' => 'adapted_description',
                        'textarea_rows' => 10,
                        'teeny'         => false,
                        'quicktags'     => true
                    )
                );
                ?>

                <h3>Natural Description</h3>
                <?php
                wp_editor(
                    $natural_description,
                    'natural_description',
                    array(
                        'wpautop'       => true,
                        'media_buttons' => true,
                        'textarea_name' => 'natural_description',
                        'textarea_rows' => 10,
                        'teeny'         => false,
                        'quicktags'     => true
                    )
                );
                ?>

                <h3>Coaching Tips</h3>
                <?php
                wp_editor(
                    $coaching_tips,
                    'coaching_tips',
                    array(
                        'wpautop'       => true,
                        'media_buttons' => true,
                        'textarea_name' => 'coaching_tips',
                        'textarea_rows' => 10,
                        'teeny'         => false,
                        'quicktags'     => true
                    )
                );
                ?>

                <button type="submit" class="button button-primary"><?php echo $type_id > 0 ? 'Save Changes' : 'Add Type'; ?></button>
            </form>
        </div>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = sanitize_text_field($_POST['action']);

            $type_name = sanitize_text_field($_POST['type_name']);
            $title = sanitize_text_field($_POST['title']);
            $adapted_description = wp_kses_post($_POST['adapted_description']);
            $natural_description = wp_kses_post($_POST['natural_description']);
            $coaching_tips = wp_kses_post($_POST['coaching_tips']);

            if ($action === 'add') {
                $wpdb->insert(
                    $table_name,
                    [
                        'type_name' => $type_name,
                        'title' => $title,
                        'adapted_description' => $adapted_description,
                        'natural_description' => $natural_description,
                        'coaching_tips' => $coaching_tips,
                    ],
                    ['%s', '%s', '%s', '%s', '%s']
                );
            } elseif ($action === 'edit') {
                $type_id = intval($_POST['type_id']);

                $wpdb->update(
                    $table_name,
                    [
                        'type_name' => $type_name,
                        'title' => $title,
                        'adapted_description' => $adapted_description,
                        'natural_description' => $natural_description,
                        'coaching_tips' => $coaching_tips,
                    ],
                    ['type_id' => $type_id],
                    ['%s', '%s', '%s', '%s', '%s'],
                    ['%d']
                );
            }

            echo '<script type="text/javascript">window.location.href="' . admin_url('admin.php?page=disc_manage_personality_types') . '";</script>';
            exit;
        }
    }
}
?>
