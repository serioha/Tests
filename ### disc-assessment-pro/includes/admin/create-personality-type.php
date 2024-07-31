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
        // Handle form submission
        if (isset($_POST['save_personality_type'])) {
            $type_name = sanitize_text_field($_POST['type_name']);
            $title = sanitize_text_field($_POST['title']);
            $adapted_description = sanitize_textarea_field($_POST['adapted_description']);
            $natural_description = sanitize_textarea_field($_POST['natural_description']);
            $coaching_tips = sanitize_textarea_field($_POST['coaching_tips']);

            global $wpdb;
            $wpdb->insert("{$wpdb->prefix}disc_personality_types", array(
                'type_name' => $type_name,
                'title' => $title,
                'adapted_description' => $adapted_description,
                'natural_description' => $natural_description,
                'coaching_tips' => $coaching_tips
            ));

            // Redirect to the personality types manager after saving
            wp_redirect(admin_url('admin.php?page=disc_manage_personality_types'));
            exit;
        }

        echo '<h1>Create New Personality Type</h1>';
        echo '<form method="post" action="">';
        echo '<label for="type_name">Type Name</label>';
        echo '<input type="text" name="type_name" required />';
        echo '<label for="title">Title</label>';
        echo '<input type="text" name="title" required />';
        echo '<label for="adapted_description">Adapted Description</label>';
        echo '<textarea name="adapted_description" required></textarea>';
        echo '<label for="natural_description">Natural Description</label>';
        echo '<textarea name="natural_description" required></textarea>';
        echo '<label for="coaching_tips">Coaching Tips</label>';
        echo '<textarea name="coaching_tips" required></textarea>';
        echo '<input type="submit" name="save_personality_type" value="Save" class="button button-primary" />';
        echo '</form>';
    }
}
?>
