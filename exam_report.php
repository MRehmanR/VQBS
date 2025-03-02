<?php
include 'Include/db_config.php';
include 'Include/header.php';

// Fetch exam performance data
$query = "SELECT u.name, er.total_questions, er.correct_answers, er.score, er.exam_date
          FROM exam_results er
          JOIN users u ON er.user_id = u.id
          ORDER BY er.exam_date DESC";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Exam Reports</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Exam Reports</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Student</th>
                <th>Total Questions</th>
                <th>Correct Answers</th>
                <th>Score</th>
                <th>Exam Date</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['total_questions']; ?></td>
                    <td><?php echo $row['correct_answers']; ?></td>
                    <td><?php echo $row['score']; ?></td>
                    <td><?php echo $row['exam_date']; ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
</body>
</html>
