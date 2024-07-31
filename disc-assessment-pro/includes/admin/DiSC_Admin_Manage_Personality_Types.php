<?php
// Ensure this file is being included correctly
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class DiSC_Admin_Manage_Personality_Types extends DiSC_Admin_Base {
    public function __construct($wpdb) {
        parent::__construct($wpdb);
    }

    public function add_menu_item($menu_title, $menu_slug, $capability, $callback = null) {
        add_submenu_page('DiSC Assessment', 'Manage Personality Types', 'Manage Personality Types', $capability, $menu_slug, array($this, 'render_personality_types_manager'));
        add_submenu_page(null, 'Edit Personality Type', 'Edit Personality Type', $capability, 'edit_personality_type', array($this, 'display_edit_personality_type_page'));
    }

    public function render_personality_types_manager() {
        // Check if the user has the right permissions
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'disc_personality_types';

        // Fetch personality types
        $types = $wpdb->get_results("SELECT * FROM $table_name");

        ?>
        <div class="wrap">
            <h1>Manage Personality Types</h1>

            <a href="<?php echo admin_url('admin.php?page=edit_personality_type'); ?>" class="button button-primary">Create New Type</a>

            <h2>Existing Personality Types</h2>
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
        include plugin_dir_path(__FILE__) . 'edit-personality-type.php';
    }
}

global $wpdb;
$personality_types_manager = new DiSC_Admin_Manage_Personality_Types($wpdb);
$personality_types_manager->add_menu_item('Manage Personality Types', 'disc_manage_personality_types', 'manage_options');
?>
