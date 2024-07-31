<?php
require_once __DIR__ . '/DiSC_Admin_Base.php';

class Edit_Personality_Type extends DiSC_Admin_Base {
    public function __construct($wpdb) {
        parent::__construct($wpdb);
    }

    public function render() {
        // Check user capability
        if (!current_user_can('manage_options')) {
            return $this->render_access_denied();
        }

        // Handle form submission
        if (isset($_POST['update_personality_type'])) {
            $type_id = intval($_POST['type_id']);
            $type_name = sanitize_text_field($_POST['type_name']);
            $title = sanitize_text_field($_POST['title']);
            $adapted_description = sanitize_textarea_field($_POST['adapted_description']);
            $natural_description = sanitize_textarea_field($_POST['natural_description']);
            $coaching_tips = sanitize_textarea_field($_POST['coaching_tips']);

            global $wpdb;
            $wpdb->update("{$wpdb->prefix}disc_personality_types", array(
                'type_name' => $type_name,
                'title' => $title,
                'adapted_description' => $adapted_description,
                'natural_description' => $natural_description,
                'coaching_tips' => $coaching_tips
            ), array('type_id' => $type_id));

            // Redirect to the personality types manager after updating
            wp_redirect(admin_url('admin.php?page=disc_manage_personality_types'));
            exit;
        }

        // Fetch the personality type to edit
        if (isset($_GET['type_id'])) {
            $type_id = intval($_GET['type_id']);
            $type = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}disc_personality_types WHERE type_id = %d", $type_id));

            if ($type) {
                echo '<h1>Edit Personality Type: ' . esc_html($type->type_name) . '</h1>';
                echo '<form method="post" action="">';
                echo '<input type="hidden" name="type_id" value="' . esc_html($type->type_id) . '" />';
                echo '<label for="type_name">Type Name</label>';
                echo '<input type="text" name="type_name" value="' . esc_html($type->type_name) . '" required />';
                echo '<label for="title">Title</label>';
                echo '<input type="text" name="title" value="' . esc_html($type->title) . '" required />';
                echo '<label for="adapted_description">Adapted Description</label>';
                echo '<textarea name="adapted_description" required>' . esc_html($type->adapted_description) . '</textarea>';
                echo '<label for="natural_description">Natural Description</label>';
                echo '<textarea name="natural_description" required>' . esc_html($type->natural_description) . '</textarea>';
                echo '<label for="coaching_tips">Coaching Tips</label>';
                echo '<textarea name="coaching_tips" required>' . esc_html($type->coaching_tips) . '</textarea>';
                echo '<input type="submit" name="update_personality_type" value="Update Personality Type" />';
                echo '<a href="' . admin_url('admin.php?page=disc_manage_personality_types') . '" class="button">Back to Personality Types</a>';
                echo '</form>';
            } else {
                echo '<p>Personality type not found.</p>';
            }
        } else {
            echo '<p>No personality type specified for editing.</p>';
        }
    }
}

global $wpdb;
$edit_personality_type = new Edit_Personality_Type($wpdb);
$edit_personality_type->render();
?>
