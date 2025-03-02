<?php
include 'Include/db_config.php';
include 'Include/header.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    echo "<script>alert('Access Denied! Only teachers can access this page.'); window.location.href='index.php';</script>";
    exit();
}

// Check if question ID is set
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('Invalid Question ID!'); window.location.href='manage_questions.php';</script>";
    exit();
}

$question_id = intval($_GET['id']);
$query = "SELECT * FROM questions WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $question_id);
$stmt->execute();
$result = $stmt->get_result();
$question = $result->fetch_assoc();

if (!$question) {
    echo "<script>alert('Question not found!'); window.location.href='manage_questions.php';</script>";
    exit();
}

// Fetch subjects and topics
$subjects = $conn->query("SELECT * FROM subjects");
$topics = $conn->query("SELECT * FROM topics");

// Update Question
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $question_text = $_POST['question_text'];
    $subject_id = $_POST['subject_id'];
    $topic_id = $_POST['topic_id'];
    $difficulty = $_POST['difficulty'];

    $update_query = "UPDATE questions SET question_text = ?, subject_id = ?, topic_id = ?, difficulty = ? WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("siiii", $question_text, $subject_id, $topic_id, $difficulty, $question_id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Question Updated Successfully!'); window.location.href='manage_questions.php';</script>";
    } else {
        echo "<script>alert('Error Updating Question!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Question</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h2>Edit Question</h2>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Question</label>
            <textarea class="form-control" name="question_text" required><?= htmlspecialchars($question['question_text']) ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Subject</label>
            <select name="subject_id" class="form-select" required>
                <?php while ($row = $subjects->fetch_assoc()): ?>
                    <option value="<?= $row['id'] ?>" <?= ($row['id'] == $question['subject_id']) ? 'selected' : '' ?>><?= $row['subject_name'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Topic</label>
            <select name="topic_id" class="form-select" required>
                <?php while ($row = $topics->fetch_assoc()): ?>
                    <option value="<?= $row['id'] ?>" <?= ($row['id'] == $question['topic_id']) ? 'selected' : '' ?>><?= $row['topic_name'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Difficulty Level</label>
            <select name="difficulty" class="form-select" required>
                <option value="Easy" <?= ($question['difficulty'] == 'Easy') ? 'selected' : '' ?>>Easy</option>
                <option value="Medium" <?= ($question['difficulty'] == 'Medium') ? 'selected' : '' ?>>Medium</option>
                <option value="Hard" <?= ($question['difficulty'] == 'Hard') ? 'selected' : '' ?>>Hard</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Update Question</button>
        <a href="manage_questions.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

</body>
</html>
