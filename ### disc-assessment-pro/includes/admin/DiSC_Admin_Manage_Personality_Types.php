<?php
require_once __DIR__ . '/DiSC_Admin_Base.php';

class DiSC_Admin_Manage_Personality_Types extends DiSC_Admin_Base {
    public function __construct($wpdb) {
        parent::__construct($wpdb);
        add_action('admin_menu', array($this, 'add_menu_item'));
    }

    public function add_menu_item($menu_title = 'Manage Personality Types', $menu_slug = 'disc_manage_personality_types', $capability = 'manage_options', $callback = null) {
        add_menu_page(
            $menu_title,
            'Personality Types',
            $capability,
            $menu_slug,
            array($this, 'display_personality_types_page'),
            'dashicons-admin-users',
            6
        );
    }

    public function display_personality_types_page() {
        global $wpdb;
        $types = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}disc_personality_types");

        ?>
        <div class="wrap">
            <h1>Manage Personality Types</h1>
            <a href="<?php echo admin_url('admin.php?page=create_personality_type'); ?>" class="page-title-action">Create New Type</a>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Type Name</th>
                        <th>Title</th>
                        <th>Adapted Description</th>
                        <th>Natural Description</th>
                        <th>Coaching Tips</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($types as $type): ?>
                        <tr>
                            <td><?php echo esc_html($type->type_id); ?></td>
                            <td><?php echo esc_html($type->type_name); ?></td>
                            <td><?php echo esc_html($type->title); ?></td>
                            <td><?php echo wp_kses_post($type->adapted_description); ?></td>
                            <td><?php echo wp_kses_post($type->natural_description); ?></td>
                            <td><?php echo wp_kses_post($type->coaching_tips); ?></td>
                            <td>
                                <a href="<?php echo admin_url('admin.php?page=edit_personality_type&type_id=' . $type->type_id); ?>" class="button">Edit</a>
                                <a href="<?php echo admin_url('admin-post.php?action=delete_type&type_id=' . $type->type_id); ?>" class="button" onclick="return confirm('Are you sure you want to delete this personality type?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    public function display_edit_personality_type_page() {
        if (!isset($_GET['type_id'])) {
            wp_die(__('No personality type specified for editing.'));
        }

        $type_id = intval($_GET['type_id']);
        $file_path = plugin_dir_path(__FILE__) . 'edit-personality-type.php';
        if (file_exists($file_path)) {
            include $file_path;
        } else {
            wp_die(__('The requested file could not be found. Please ensure the file exists in the correct directory.'));
        }
    }
}

global $wpdb;
$personality_types_manager = new DiSC_Admin_Manage_Personality_Types($wpdb);
?>
