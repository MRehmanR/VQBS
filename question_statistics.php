<?php
include 'Include/db_config.php';
include 'Include/header.php';

// Fetch question usage data
$query = "SELECT q.question_text, COUNT(qa.id) AS attempts, 
                 SUM(qa.is_correct) AS correct_answers, 
                 AVG(qa.time_taken) AS avg_time
          FROM questions q
          LEFT JOIN question_attempts qa ON q.id = qa.question_id
          GROUP BY q.id";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Question Statistics</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Question Statistics</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Question</th>
                <th>Attempts</th>
                <th>Correct Answers</th>
                <th>Average Time (Sec)</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['question_text']; ?></td>
                    <td><?php echo $row['attempts']; ?></td>
                    <td><?php echo $row['correct_answers']; ?></td>
                    <td><?php echo round($row['avg_time'], 2); ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
</body>
</html>
