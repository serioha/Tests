jQuery(document).ready(function($) {
    // Add Answer functionality
    $('#add-answer').click(function() {
        var answersContainer = $('#answers');
        var newAnswer = $('<div class="answer"></div>');
        newAnswer.html(`
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
        `);
        answersContainer.append(newAnswer);

        // Remove Answer functionality
        newAnswer.find('.remove-answer').click(function() {
            if (answersContainer.children().length > 1) {
                $(this).closest('.answer').remove();
            } else {
                alert('At least one answer block must remain.');
            }
        });
    });

    // Remove Answer functionality for existing answers
    $('#answers').on('click', '.remove-answer', function() {
        var answersContainer = $('#answers');
        if (answersContainer.children().length > 1) {
            $(this).closest('.answer').remove();
        } else {
            alert('At least one answer block must remain.');
        }
    });

    // Form submission handling
    $('#question-form').submit(function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: 'POST',
             {
                action: 'save_question',
                formData: formData
            },
            success: function(response) {
                if (response.success) {
                    // Display success message
                    $('#save-message').show();
                    setTimeout(function() {
                        $('#save-message').hide();
                        window.location.href = '<?php echo admin_url('admin.php?page=disc_manage_questions&test_id=' . $test_id); ?>';
                    }, 3000);
                } else {
                    alert('Failed to save question.');
                }
            },
            error: function(error) {
                console.error('Error:', error);
            }
        });
    });
});
