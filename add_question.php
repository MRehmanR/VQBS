<?php
include 'Include/header.php';
include 'Include/db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $question = trim($_POST['question']);
    $subject = trim($_POST['subject']);
    $topic = trim($_POST['topic']);
    $difficulty = $_POST['difficulty'];
    $type = $_POST['type']; // Subjective or Objective

    // Check database connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Insert Subject if it does not exist
    if (!empty($subject)) {
        $stmt = $conn->prepare("INSERT INTO subjects (subject_name) VALUES (?) ON DUPLICATE KEY UPDATE subject_name=subject_name");
        if (!$stmt) {
            die("SQL Error (Subject): " . $conn->error);
        }
        $stmt->bind_param("s", $subject);
        $stmt->execute();
        $stmt->close();
    }

    // Insert Topic if it does not exist
    if (!empty($topic)) {
        $stmt = $conn->prepare("INSERT INTO topics (topic_name) VALUES (?) ON DUPLICATE KEY UPDATE topic_name=topic_name");
        if (!$stmt) {
            die("SQL Error (Topic): " . $conn->error);
        }
        $stmt->bind_param("s", $topic);
        $stmt->execute();
        $stmt->close();
    }

    // Insert Question
    if ($type == 'objective') {
        $option1 = trim($_POST['option1']);
        $option2 = trim($_POST['option2']);
        $option3 = trim($_POST['option3']);
        $option4 = trim($_POST['option4']);
        $correct_option = $_POST['correct_option'];

        // Insert Objective Question
        $sql = "INSERT INTO questions (question, subject, topic, difficulty, type, option1, option2, option3, option4, correct_option) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("SQL Error (Objective Question): " . $conn->error);
        }
        $stmt->bind_param("ssssssssss", $question, $subject, $topic, $difficulty, $type, $option1, $option2, $option3, $option4, $correct_option);
    } else {
        // Insert Subjective Question
        $sql = "INSERT INTO questions (question, subject, topic, difficulty, type) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("SQL Error (Subjective Question): " . $conn->error);
        }
        $stmt->bind_param("sssss", $question, $subject, $topic, $difficulty, $type);
    }

    if ($stmt->execute()) {
        echo "<div class='alert alert-success text-center'>Question added successfully!</div>";
    } else {
        echo "<div class='alert alert-danger text-center'>Error: " . $stmt->error . "</div>";
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Question</title>
    <link rel="stylesheet" href="Styles/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center">Add a New Question</h2>
    
    <form action="" method="POST" class="p-4 shadow rounded">
        <div class="mb-3">
            <label class="form-label">Question</label>
            <textarea class="form-control" name="question" required></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Subject</label>
            <input type="text" class="form-control" name="subject" placeholder="Enter Subject Name" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Topic</label>
            <input type="text" class="form-control" name="topic" placeholder="Enter Topic Name" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Difficulty Level</label>
            <select class="form-select" name="difficulty" required>
                <option value="Easy">Easy</option>
                <option value="Medium">Medium</option>
                <option value="Hard">Hard</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Question Type</label>
            <select class="form-select" name="type" id="questionType" required onchange="toggleOptions()">
                <option value="subjective">Subjective</option>
                <option value="objective">Objective</option>
            </select>
        </div>

        <!-- Objective Question Options -->
        <div id="optionsContainer" style="display: none;">
            <div class="mb-3">
                <label class="form-label">Option 1</label>
                <input type="text" class="form-control" name="option1">
            </div>
            <div class="mb-3">
                <label class="form-label">Option 2</label>
                <input type="text" class="form-control" name="option2">
            </div>
            <div class="mb-3">
                <label class="form-label">Option 3</label>
                <input type="text" class="form-control" name="option3">
            </div>
            <div class="mb-3">
                <label class="form-label">Option 4</label>
                <input type="text" class="form-control" name="option4">
            </div>
            <div class="mb-3">
                <label class="form-label">Correct Answer</label>
                <select class="form-select" name="correct_option">
                    <option value="option1">Option 1</option>
                    <option value="option2">Option 2</option>
                    <option value="option3">Option 3</option>
                    <option value="option4">Option 4</option>
                </select>
            </div>
        </div>

        <button type="submit" class="btn btn-success w-100">Add Question</button>
    </form>
</div>

<script>
function toggleOptions() {
    var type = document.getElementById('questionType').value;
    var optionsContainer = document.getElementById('optionsContainer');
    if (type === 'objective') {
        optionsContainer.style.display = "block";
    } else {
        optionsContainer.style.display = "none";
    }
}
</script>

</body>
</html>
