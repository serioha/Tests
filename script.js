// Fetch all questions from the API
fetch('https://example.com/api/questions')
    .then(response => response.json())
    .then(data => {
        // Process the questions data
        console.log(data.questions);
    });

// Create a new question object
const question = {
    text: 'What is the capital of France?',
    options: ['A) Paris', 'B) London', 'C) Berlin', 'D) Madrid'],
    correct_answer: 'A) Paris'
};

// Send a POST request to create a new question
fetch('https://example.com/api/questions', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json'
    },
    body: JSON.stringify(question)
})
    .then(response => response.json())
    .then(data => {
        // Process the response data
        console.log(data);
    });
