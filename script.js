// JavaScript for the Create/Edit question page

document.addEventListener('DOMContentLoaded', function () {
    const addAnswerButton = document.getElementById('add-answer');
    const answersContainer = document.getElementById('answers-container');

    addAnswerButton.addEventListener('click', function () {
        const newAnswer = document.createElement('div');
        newAnswer.innerHTML = `
            <div class="answer">
                <label for="answer-new">Answer:</label>
                <input type="text" id="answer-new" name="answers[]">
                <button type="button" class="remove-answer">Remove</button>
            </div>
        `;
        answersContainer.appendChild(newAnswer);
    });

    answersContainer.addEventListener('click', function (event) {
        if (event.target.classList.contains('remove-answer')) {
            event.target.parentNode.remove();
        }
    });
});
