<?php

function validate_json_structure($data) {
    if (!isset($data['test_name']) || !isset($data['test_description']) || !isset($data['questions'])) {
        return ['status' => 'error', 'message' => 'Missing required fields: test_name, test_description, or questions.'];
    }

    if (!is_array($data['questions'])) {
        return ['status' => 'error', 'message' => 'Questions should be an array.'];
    }

    foreach ($data['questions'] as $question) {
        if (!isset($question['question']) || !isset($question['answers'])) {
            return ['status' => 'error', 'message' => 'Each question must have a question text and answers.'];
        }

        if (!is_array($question['answers'])) {
            return ['status' => 'error', 'message' => 'Answers should be an array.'];
        }

        foreach ($question['answers'] as $answer) {
            if (!isset($answer['text']) || !isset($answer['adapted_score']) || !isset($answer['natural_score'])) {
                return ['status' => 'error', 'message' => 'Each answer must have text, adapted_score, and natural_score.'];
            }

            if (!is_array($answer['adapted_score']) || !is_array($answer['natural_score'])) {
                return ['status' => 'error', 'message' => 'Scores must be arrays.'];
            }
        }
    }

    return ['status' => 'success', 'message' => 'JSON structure is valid.'];
}

// Example usage
$json_file_path = 'C:\Users\Admin\Development\Plugins\Tests\temp\JSON\adapted-and-natural-styles-validated-6.json';
$json_data = json_decode(file_get_contents($json_file_path), true);
$result = validate_json_structure($json_data);
echo json_encode($result);
?>
