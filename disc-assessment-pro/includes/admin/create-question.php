<?php
require_once __DIR__ . '/DiSC_Admin_Base.php';

class Create_Question extends DiSC_Admin_Base {
    public function __construct($wpdb) {
        parent::__construct($wpdb);
        add_action('admin_init', array($this, 'check_user_capability'));
    }

    public function check_user_capability() {
        if (!current_user_can('manage_options')) {
            wp_die('Sorry, you are not allowed to view this page.');
        }
    }

    public function render() {
        global $wpdb;

        $test_id = isset($_GET['test_id']) ? intval($_GET['test_id']) : 0;
        $question_id = isset($_GET['question_id']) ? intval($_GET['question_id']) : 0;

        $questions_table = $wpdb->prefix . 'disc_questions';
        $answers_table = $wpdb->prefix . 'disc_answers';

        $question_text = '';
        $answers = array();

        if ($question_id > 0) {
            $question = $wpdb->get_row($wpdb->prepare("SELECT * FROM $questions_table WHERE question_id = %d", $question_id));
            if ($question) {
                $question_text = $question->question_text;
                $answers = $wpdb->get_results($wpdb->prepare("SELECT * FROM $answers_table WHERE question_id = %d", $question_id), ARRAY_A);
            }
        }

        ?>
        <div class="wrap">
            <h1><?php echo $question_id > 0 ? 'Edit Question' : 'Create New Question'; ?></h1>

            <a href="<?php echo admin_url('admin.php?page=disc_manage_questions&test_id=' . $test_id); ?>" class="button">Back to Questions</a>
            <a href="<?php echo admin_url('admin.php?page=disc_manage_tests'); ?>" class="button">Back to Tests</a>

            <form id="question-form">
                <input type="hidden" name="question_id" value="<?php echo esc_attr($question_id); ?>">
                <input type="hidden" name="test_id" value="<?php echo esc_attr($test_id); ?>">

                <h3>Question Text</h3>
                <textarea name="question_text" id="question_text" rows="5" required><?php echo esc_textarea($question_text); ?></textarea>

                <h3>Answers</h3>
                <div id="answers">
                    <?php if (!empty($answers)) : ?>
                        <?php foreach ($answers as $index => $answer) : ?>
                            <div class="answer">
                                <textarea name="answers[<?php echo $index; ?>][text]" placeholder="Answer" required><?php echo esc_textarea($answer['answer_text']); ?></textarea>
                                <div class="answer-group">
                                    <label>Adapted Style</label>
                                    <input type="number" name="answers[<?php echo $index; ?>][score_d_adapted]" placeholder="D" value="<?php echo intval($answer['score_d_adapted']); ?>" required>
                                    <input type="number" name="answers[<?php echo $index; ?>][score_i_adapted]" placeholder="I" value="<?php echo intval($answer['score_i_adapted']); ?>" required>
                                    <input type="number" name="answers[<?php echo $index; ?>][score_s_adapted]" placeholder="S" value="<?php echo intval($answer['score_s_adapted']); ?>" required>
                                    <input type="number" name="answers[<?php echo $index; ?>][score_c_adapted]" placeholder="C" value="<?php echo intval($answer['score_c_adapted']); ?>" required>
                                </div>
                                <div class="answer-group">
                                    <label>Natural Style</label>
                                    <input type="number" name="answers[<?php echo $index; ?>][score_d_natural]" placeholder="D" value="<?php echo intval($answer['score_d_natural']); ?>" required>
                                    <input type="number" name="answers[<?php echo $index; ?>][score_i_natural]" placeholder="I" value="<?php echo intval($answer['score_i_natural']); ?>" required>
                                    <input type="number" name="answers[<?php echo $index; ?>][score_s_natural]" placeholder="S" value="<?php echo intval($answer['score_s_natural']); ?>" required>
                                    <input type="number" name="answers[<?php echo $index; ?>][score_c_natural]" placeholder="C" value="<?php echo intval($answer['score_c_natural']); ?>" required>
                                </div>
                                <button type="button" class="remove-answer">Remove Answer</button>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="answer">
                            <textarea name="answers[0][text]" placeholder="Answer" required></textarea>
                            <div class="answer-group">
                                <label>Adapted Style</label>
                                <input type="number" name="answers[0][score_d_adapted]" placeholder="D" required>
                                <input type="number" name="answers[0][score_i_adapted]" placeholder="I" required>
                                <input type="number" name="answers[0][score_s_adapted]" placeholder="S" required>
                                <input type="number" name="answers[0][score_c_adapted]" placeholder="C" required>
                            </div>
                            <div class="answer-group">
                                <label>Natural Style</label>
                                <input type="number" name="answers[0][score_d_natural]" placeholder="D" required>
                                <input type="number" name="answers[0][score_i_natural]" placeholder="I" required>
                                <input type="number" name="answers[0][score_s_natural]" placeholder="S" required>
                                <input type="number" name="answers[0][score_c_natural]" placeholder="C" required>
                            </div>
                            <button type="button" class="remove-answer">Remove Answer</button>
                        </div>
                    <?php endif; ?>
                </div>

                <button type="button" id="add_answer" class="button button-secondary">Add Answer</button>
                <button type="submit" class="button button-primary">Save Question</button>
            </form>

            <div id="save-message" style="display:none;" class="updated"><p>Question saved successfully.</p></div>
        </div>

        <script>
        document.getElementById('add_answer').addEventListener('click', function() {
            var answers = document.getElementById('answers');
            var index = answers.children.length;
            var answer = document.createElement('div');
            answer.classList.add('answer');
            answer.innerHTML = `
                <textarea name="answers[` + index + `][text]" placeholder="Answer" required></textarea>
                <div class="answer-group">
                    <label>Adapted Style</label>
                    <input type="number" name="answers[` + index + `][score_d_adapted]" placeholder="D" required>
                    <input type="number" name="answers[` + index + `][score_i_adapted]" placeholder="I" required>
                    <input type="number" name="answers[` + index + `][score_s_adapted]" placeholder="S" required>
                    <input type="number" name="answers[` + index + `][score_c_adapted]" placeholder="C" required>
                </div>
                <div class="answer-group">
                    <label>Natural Style</label>
                    <input type="number" name="answers[` + index + `][score_d_natural]" placeholder="D" required>
                    <input type="number" name="answers[` + index + `][score_i_natural]" placeholder="I" required>
                    <input type="number" name="answers[` + index + `][score_s_natural]" placeholder="S" required>
                    <input type="number" name="answers[` + index + `][score_c_natural]" placeholder="C" required>
                </div>
                <button type="button" class="remove-answer">Remove Answer</button>
            `;
            answers.appendChild(answer);

            answer.querySelector('.remove-answer').addEventListener('click', function() {
                answer.remove();
            });
        });

        document.querySelectorAll('.remove-answer').forEach(function(button) {
            button.addEventListener('click', function() {
                button.parentElement.remove();
            });
        });

        document.getElementById('question-form').addEventListener('submit', function(event) {
            event.preventDefault();
            var formData = new FormData(this);

            fetch('<?php echo admin_url('admin-ajax.php?action=save_question'); ?>', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('save-message').style.display = 'block';
                    setTimeout(() => {
                        document.getElementById('save-message').style.display = 'none';
                        window.location.href = '<?php echo admin_url('admin.php?page=disc_manage_tests&action=manage_questions&test_id=' . $test_id); ?>';
                    }, 3000);
                } else {
                    alert('Failed to save question.');
                }
            })
            .catch(error => console.error('Error:', error));
        });
        </script>

        <style>
        .wrap>h1 {
            font-size: 23px;
            font-weight: 400;
            margin: 1em 0;
            padding: 0;
            line-height: 1.3;
        }

        textarea {
            width: 100%;
        }

        .answer {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 10px;
            background-color: #f9f9f9;
        }

        .answer textarea {
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
        <?php
    }
}

global $wpdb;
$create_question = new Create_Question($wpdb);
$create_question->render();
?>
