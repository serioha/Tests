console.log('script.js loaded');

document.addEventListener('DOMContentLoaded', function() {
    console.log('Script loaded');
    document.getElementById('add-answer').addEventListener('click', function() {
        console.log('Add Answer button clicked');
        const answersContainer = document.getElementById('answers-container');
        const newAnswer = document.createElement('div');
        newAnswer.classList.add('answer');
        newAnswer.innerHTML = `
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
        `;
        answersContainer.appendChild(newAnswer);
        newAnswer.querySelector('.remove-answer').addEventListener('click', function() {
            if (answersContainer.children.length > 1) {
                newAnswer.remove();
            } else {
                alert('At least one answer block must remain.');
            }
        });
    });

    document.getElementById('answers-container').addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-answer')) {
            console.log('Remove Answer button clicked');
            const answerBlock = e.target.parentElement;
            const answersContainer = document.getElementById('answers-container');
            if (answersContainer.children.length > 1) {
                answerBlock.remove();
            } else {
                alert('At least one answer block must remain.');
            }
        }
    });

    document.querySelector('form').addEventListener('submit', function(e) {
        console.log('Form submitted');
        const answerInputs = document.querySelectorAll('.answer input');
        let allFilled = true;
        answerInputs.forEach(input => {
            if (!input.value) {
                allFilled = false;
                console.log('Missing value in input:', input);
            }
        });
        if (!allFilled) {
            e.preventDefault();
            alert('Please fill out all required fields.');
        }
    });
});
