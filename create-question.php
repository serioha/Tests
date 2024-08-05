<?php
// Create/Edit question page

// Connect to the database
$db = new PDO('mysql:host=localhost;dbname=mydb', 'username', 'password');

// Get the question ID from the URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get the question data from the database
if ($id > 0) {
    $stmt = $db->prepare('SELECT * FROM questions WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $question = $stmt->fetch();
} else {
    $question = [
        'id' => 0,
        'title' => '',
        'answers' => [['text' => ''], ['text' => ''], ['text' => '']],
    ];
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate the submitted data
    $title = trim($_POST['title']);
    $answers = array_map('trim', $_POST['answers']);

    // Check if the title is empty
    if (empty($title)) {
        $error = 'Title cannot be empty!';
    } else {
        // Insert or update the question data in the database
        if ($id > 0) {
            $stmt = $db->prepare('UPDATE questions SET title = :title, answers = :answers WHERE id = :id');
            $result = $stmt->execute(['title' => $title, 'answers' => json_encode($answers), 'id' => $id]);
        } else {
            $stmt = $db->prepare('INSERT INTO questions (title, answers) VALUES (:title, :answers)');
            $result = $stmt->execute(['title' => $title, 'answers' => json_encode($answers)]);
            $id = $db->lastInsertId();
        }

        if ($result) {
            header('Location: questions.php?message=Question saved successfully!');
        } else {
            $error = 'Failed to save the question!';
        }
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Create/Edit Question</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <script src="script.js"></script>
</head>
<body>
    <h1>Create/Edit Question</h1>
    <?php if (isset($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="post">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" value="<?= htmlspecialchars($question['title']) ?>">
        <div id="answers-container">
            <?php foreach ($question['answers'] as $index => $answer): ?>
                <div class="answer">
                    <label for="answer-<?= $index ?>">Answer <?= $index + 1 ?>: </label>
                    <input type="text" id="answer-<?= $index ?>" name="answers[]" value="<?= htmlspecialchars($answer['text']) ?>">
                    <button type="button" class="remove-answer">Remove</button>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" id="add-answer">Add Answer</button>
        <input type="submit" value="Save Question">
    </form>
</body>
</html>
