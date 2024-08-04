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
});
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('add-answer').addEventListener('click', function() {
        const answersContainer = document.getElementById('answers-container');
        const newAnswer = document.createElement('div');
        newAnswer.classList.add('answer');
        newAnswer.innerHTML = `
            <input type="text" name="answer_text[]" placeholder="Answer Text" required>
            <input type="number" name="score_d_adapted[]" placeholder="D Adapted Score" required>
            <input type="number" name="score_i_adapted[]" placeholder="I Adapted Score" required>
            <input type="number" name="score_s_adapted[]" placeholder="S Adapted Score" required>
            <input type="number" name="score_c_adapted[]" placeholder="C Adapted Score" required>
            <input type="number" name="score_d_natural[]" placeholder="D Natural Score" required>
            <input type="number" name="score_i_natural[]" placeholder="I Natural Score" required>
            <input type="number" name="score_s_natural[]" placeholder="S Natural Score" required>
            <input type="number" name="score_c_natural[]" placeholder="C Natural Score" required>
            <button type="button" class="remove-answer">Remove</button>
        `;
        answersContainer.appendChild(newAnswer);
    });

    document.getElementById('answers-container').addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-answer')) {
            e.target.parentElement.remove();
        }
    });
});
