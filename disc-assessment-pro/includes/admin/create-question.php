<?php
require_once __DIR__ . '/DiSC_Admin_Base.php';

class Create_Question extends DiSC_Admin_Base {
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

        $question_id = isset($_GET['question_id']) ? intval($_GET['question_id']) : 0;
        $test_id = isset($_GET['test_id']) ? intval($_GET['test_id']) : 0; // Get test_id from the URL
        $table_name = $wpdb->prefix . 'disc_questions';

        $question_text = '';

        if ($question_id > 0) {
            $question = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE question_id = %d", $question_id));
            if ($question) {
                $question_text = $question->question_text ?? '';
            }
        }

        ?>
        <div class="wrap">
            <h1><?php echo $question_id > 0 ? 'Edit Question' : 'Create New Question'; ?></h1>

            <a href="<?php echo admin_url('admin.php?page=disc_manage_questions&test_id=' . $test_id); ?>" class="button">Back to Questions</a>

            <form method="post">
                <input type="hidden" name="action" value="<?php echo $question_id > 0 ? 'edit' : 'add'; ?>">
                <?php if ($question_id > 0): ?>
                    <input type="hidden" name="question_id" value="<?php echo esc_attr($question_id); ?>">
                <?php endif; ?>
                <input type="hidden" name="test_id" value="<?php echo esc_attr($test_id); ?>">

                <h3>Question Text</h3>
                <textarea name="question_text" style="width: 100%;" required><?php echo esc_textarea($question_text); ?></textarea>

                <h3>Possible Answers</h3>
                <div id="answers-container" style="margin-bottom: 20px;">
                    <?php
                    // If editing, display existing answers
                    if ($question_id > 0) {
                        $answers = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}disc_answers WHERE question_id = %d", $question_id));
                        foreach ($answers as $answer) {
                            ?>
                            <div class="answer">
                                <input class="answer-text" type="text" name="answer_text[]" value="<?php echo esc_attr($answer->answer_text); ?>" placeholder="Answer Text" required>
                                <div class="answer-group">
                                    <label>Adapted Style</label>
                                    <input type="number" name="score_d_adapted[]" value="<?php echo esc_attr($answer->score_d_adapted); ?>" placeholder="D" required>
                                    <input type="number" name="score_i_adapted[]" value="<?php echo esc_attr($answer->score_i_adapted); ?>" placeholder="I" required>
                                    <input type="number" name="score_s_adapted[]" value="<?php echo esc_attr($answer->score_s_adapted); ?>" placeholder="S" required>
                                    <input type="number" name="score_c_adapted[]" value="<?php echo esc_attr($answer->score_c_adapted); ?>" placeholder="C" required>
                                </div>
                                <div class="answer-group">
                                    <label>Natural Style</label>
                                    <input type="number" name="score_d_natural[]" value="<?php echo esc_attr($answer->score_d_natural); ?>" placeholder="D" required>
                                    <input type="number" name="score_i_natural[]" value="<?php echo esc_attr($answer->score_i_natural); ?>" placeholder="I" required>
                                    <input type="number" name="score_s_natural[]" value="<?php echo esc_attr($answer->score_s_natural); ?>" placeholder="S" required>
                                    <input type="number" name="score_c_natural[]" value="<?php echo esc_attr($answer->score_c_natural); ?>" placeholder="C" required>
                                </div>
                                <button type="button" class="remove-answer">Remove</button>
                            </div>
                            <?php
                        }
                    } else {
                        // If creating a new question, show an empty answer field
                        ?>
                        <div class="answer">
                            <input class="answer-text" type="text" name="answer_text[]" placeholder="Answer Text" required>
                            <div class="answer-group">
                                <label>Adapted Style</label>
                                <input type="number" name="score_d_adapted[]" placeholder="D" required>
                                <input type="number" name="score_i_adapted[]" placeholder="I" required>
                                <input type="number" name="score_s_adapted[]" placeholder="S" required>
                                <input type="number" name="score_c_adapted[]" placeholder="C" required>
                            </div>
                            <div class="answer-group">
                                <label>Natural Style</label>
                                <input type="number" name="score_d_natural[]" placeholder="D" required>
                                <input type="number" name="score_i_natural[]" placeholder="I" required>
                                <input type="number" name="score_s_natural[]" placeholder="S" required>
                                <input type="number" name="score_c_natural[]" placeholder="C" required>
                            </div>
                            <button type="button" class="remove-answer">Remove</button>
                        </div>
                        <?php
                    }
                    ?>
                </div>
                
                <div id="answers-container" style="margin-bottom: 20px;">
                    <div class="answer">
                        <input class="answer-text" type="text" name="answer_text[]" placeholder="Answer Text" required>
                        <div class="answer-group">
                            <label>Adapted Style</label>
                            <input type="number" name="score_d_adapted[]" placeholder="D" required>
                            <input type="number" name="score_i_adapted[]" placeholder="I" required>
                            <input type="number" name="score_s_adapted[]" placeholder="S" required>
                            <input type="number" name="score_c_adapted[]" placeholder="C" required>
                        </div>
                        <div class="answer-group">
                            <label>Natural Style</label>
                            <input type="number" name="score_d_natural[]" placeholder="D" required>
                            <input type="number" name="score_i_natural[]" placeholder="I" required>
                            <input type="number" name="score_s_natural[]" placeholder="S" required>
                            <input type="number" name="score_c_natural[]" placeholder="C" required>
                        </div>
                        <button type="button" class="remove-answer">Remove</button>
                    </div>
                </div>
                <button type="button" id="add-answer" class="button button-secondary">Add Answer</button>
                <button type="submit" class="button button-primary"><?php echo $question_id > 0 ? 'Save Changes' : 'Add Question'; ?></button>
            </form>
        </div>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = sanitize_text_field($_POST['action']);
            $question_text = sanitize_textarea_field($_POST['question_text']);

            if ($action === 'add') {
                $wpdb->insert(
                    $table_name,
                    [
                        'test_id' => $test_id, // Use the test_id from the URL
                        'question_text' => $question_text
                    ],
                    ['%d', '%s']
                );
            } elseif ($action === 'edit') {
                $question_id = intval($_POST['question_id']);

                $wpdb->update(
                    $table_name,
                    [
                        'question_text' => $question_text
                    ],
                    ['question_id' => $question_id],
                    ['%s'],
                    ['%d']
                );
            }

            echo '<script type="text/javascript">window.location.href="' . admin_url('admin.php?page=disc_manage_questions&test_id=' . $test_id) . '";</script>';
            exit;
        }
    }

}

?>
<style>
.answer {
    border: 1px solid #ccc;
    padding: 10px;
    margin-bottom: 10px;
    background-color: #f9f9f9;
    border-radius: 5px;
}

.answer .answer-text {
    width: 100%;
    margin-bottom: 5px;
}

.answer-group {
    border: 1px solid #ddd;
    padding: 10px;
    margin-bottom: 10px;
}

.answer-group label {
    display: block;
    font-weight: bold;
    margin-bottom: 5px;
}

.answer input {
    width: 60px;
    margin-right: 5px;
}

.remove-answer {
    background: #ff0000;
    color: #ffffff;
    border: none;
    padding: 5px 10px;
    cursor: pointer;
}

.remove-answer:hover {
    background: #cc0000;
}
</style>
