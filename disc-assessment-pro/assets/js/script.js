document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('import-json').addEventListener('click', function() {
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = '.json';
        input.onchange = e => {
            const file = e.target.files[0];
            const formData = new FormData();
            formData.append('json_file', file);
            formData.append('action', 'import_json_file');

            fetch('admin-post.php', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                location.reload(); // Reload the page to see the changes
            })
            .catch(error => {
                console.error('Error:', error);
            });
        };
        input.click();
    });
    document.getElementById('add-answer').addEventListener('click', function() {
        const answersContainer = document.getElementById('answers-container');
        const newAnswer = document.createElement('div');
        newAnswer.classList.add('answer');
        newAnswer.innerHTML = `
            <input type="text" name="answer_text[]" placeholder="Answer Text" required>
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
        const answerInputs = document.querySelectorAll('.answer input');
        let allFilled = true;
        answerInputs.forEach(input => {
            if (!input.value) {
                allFilled = false;
            }
        });
        if (!allFilled) {
            e.preventDefault();
            alert('Please fill out all required fields.');
        }
    });
        if (e.target.classList.contains('remove-answer')) {
            e.target.parentElement.remove();
        }
    });

