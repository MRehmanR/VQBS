<?php
include 'Include/db_config.php';
include 'Include/header.php';

// User ID fetch karna session se
$id = isset($_SESSION['id']) ? $_SESSION['id'] : 0;

// Total questions, attempted, marks calculation
$totalQuestions = isset($_SESSION['questions']) ? count($_SESSION['questions']) : 0;
$attempted = isset($_SESSION['attempted_questions']) ? $_SESSION['attempted_questions'] : 0;
$totalMarks = $totalQuestions;
$obtainedMarks = isset($_SESSION['score']) ? $_SESSION['score'] : 0;
$percentage = ($totalMarks > 0) ? ($obtainedMarks / $totalMarks) * 100 : 0;

// User ka last attempt number fetch karna
$attempt_number = 1;
if ($id) {
    $query = "SELECT MAX(attempt_number) as last_attempt FROM user_performance WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if ($row && isset($row['last_attempt'])) {
        $attempt_number = $row['last_attempt'] + 1;
    }
    $stmt->close();
}

// Result insert karna database me
$attempt_date = date("Y-m-d H:i:s");

$query = "INSERT INTO user_performance (id, total_marks, obtained_marks, percentage, attempt_number, attempt_date) 
          VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($query);
if ($stmt) {
    $stmt->bind_param("iiidis", $id, $totalMarks, $obtainedMarks, $percentage, $attempt_number, $attempt_date);
    if ($stmt->execute()) {
        echo "✅ Data Inserted Successfully!";
    } else {
        echo "❌ Insert Error: " . $stmt->error;
    }
    $stmt->close();
} else {
    echo "❌ Query Preparation Failed: " . $conn->error;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Result</title>
    <link rel="stylesheet" href="Styles/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <div class="card">
        <div class="card-header text-center">
            <h2>Exam Result</h2>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <tr><th>Total Questions</th><td><?= $totalQuestions ?></td></tr>
                <tr><th>Attempted Questions</th><td><?= $attempted ?></td></tr>
                <tr><th>Total Marks</th><td><?= $totalMarks ?></td></tr>
                <tr><th>Obtained Marks</th><td><?= $obtainedMarks ?></td></tr>
                <tr><th>Percentage</th><td><?= round($percentage, 2) ?>%</td></tr>
                <tr><th>Remarks</th><td><?= ($percentage >= 50) ? 'Pass' : 'Fail' ?></td></tr>
            </table>
            <a href="exam.php" class="btn btn-primary">Retake Exam</a>
            <a href="index.php" class="btn btn-warning">Home</a>
        </div>
    </div>
</div>
</body>
</html>
