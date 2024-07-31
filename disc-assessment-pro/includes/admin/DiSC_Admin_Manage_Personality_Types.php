<?php
// Ensure this file is being included correctly
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

require_once __DIR__ . '/create-personality-type.php'; // Ensure the class is included

class DiSC_Admin_Manage_Personality_Types extends DiSC_Admin_Base {
    public function __construct($wpdb) {
        parent::__construct($wpdb);
        add_action('admin_menu', array($this, 'add_menu_item'));
    }

    public function add_menu_item($menu_title = 'Manage Personality Types', $menu_slug = 'disc_manage_personality_types', $capability = 'manage_options', $callback = null) {
        add_submenu_page('DiSC Assessment', $menu_title, $menu_title, $capability, $menu_slug, array($this, 'render_personality_types_manager'));
        add_submenu_page(null, 'Edit Personality Type', 'Edit Personality Type', $capability, 'edit_personality_type', array($this, 'display_edit_personality_type_page'));
        add_submenu_page(null, 'Create Personality Type', 'Create Personality Type', $capability, 'create_personality_type', array(new Create_Personality_Type($this->wpdb), 'render'));
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

            <a href="<?php echo admin_url('admin.php?page=create_personality_type'); ?>" class="button button-primary">Create New Type</a>

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
