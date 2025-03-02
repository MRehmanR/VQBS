<?php
session_start();
include 'Include/db_config.php';

// Ensure student is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    echo "<script>alert('Access denied! Please log in as a student.'); window.location.href='login.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];
$total_questions = count($_POST['answer']);
$correct_count = 0;
$feedback = [];

foreach ($_POST['answer'] as $question_id => $user_answer) {
    $correct_answer = $_POST['correct'][$question_id];
    $explanation = $_POST['explanation'][$question_id];

    if (trim(strtolower($user_answer)) == trim(strtolower($correct_answer))) {
        $correct_count++;
        $status = "Correct";
    } else {
        $status = "Incorrect";
    }

    // Store feedback
    $feedback[] = [
        'question_id' => $question_id,
        'user_answer' => $user_answer,
        'correct_answer' => $correct_answer,
        'status' => $status,
        'explanation' => $explanation
    ];

    // Save attempt in database
    $stmt = $conn->prepare("INSERT INTO student_attempts (student_id, question_id, user_answer, correct_answer, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $user_id, $question_id, $user_answer, $correct_answer, $status);
    $stmt->execute();
}

// Calculate Score
$score = ($correct_count / $total_questions) * 100;
?>

<h2>Practice Result</h2>
<p><strong>Score:</strong> <?= round($score, 2) ?>%</p>

<table border="1">
    <tr>
        <th>Question ID</th>
        <th>Your Answer</th>
        <th>Correct Answer</th>
        <th>Status</th>
        <th>Explanation</th>
    </tr>
    <?php foreach ($feedback as $row): ?>
        <tr>
            <td><?= $row['question_id'] ?></td>
            <td><?= $row['user_answer'] ?></td>
            <td><?= $row['correct_answer'] ?></td>
            <td><?= $row['status'] ?></td>
            <td><?= $row['explanation'] ?></td>
        </tr>
    <?php endforeach; ?>
</table>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="Styles/styles.css">
<a href="practice_question.php">Try Again</a>
