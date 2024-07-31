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

        // Handle the custom action
        if (isset($_GET['action']) && ($_GET['action'] === 'create_new_type' || $_GET['action'] === 'edit' && isset($_GET['type_id']))) {
            $create_personality_type = new Create_Personality_Type($wpdb);
            $create_personality_type->render();
            return;
        }

        ?>
        <div class="wrap">
            <h1>Manage Personality Types</h1>
            <a href="<?php echo admin_url('admin.php?page=disc_manage_personality_types&action=create_new_type'); ?>" class="page-title-action">Create New Type</a>
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
                                <a href="<?php echo admin_url('admin.php?page=disc_manage_personality_types&action=edit&type_id=' . $type->type_id); ?>" class="button">Edit</a>
                                <a href="<?php echo admin_url('admin-post.php?action=delete_type&type_id=' . $type->type_id); ?>" class="button" onclick="return confirm('Are you sure you want to delete this personality type?');">Delete</a>
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
$personality_types_manager = new DiSC_Admin_Manage_Personality_Types($wpdb);
?>
