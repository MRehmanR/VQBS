<?php

include 'Include/header.php';
$totalQuestions = count($_SESSION['questions']);
$attempted = $_SESSION['attempted_questions'];
$totalMarks = $totalQuestions;
$obtainedMarks = $_SESSION['score'];
$percentage = ($obtainedMarks / $totalMarks) * 100;
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
                <tr>
                    <th>Total Questions</th>
                    <td><?= $totalQuestions ?></td>
                </tr>
                <tr>
                    <th>Attempted Questions</th>
                    <td><?= $attempted ?></td>
                </tr>
                <tr>
                    <th>Total Marks</th>
                    <td><?= $totalMarks ?></td>
                </tr>
                <tr>
                    <th>Obtained Marks</th>
                    <td><?= $obtainedMarks ?></td>
                </tr>
                <tr>
                    <th>Percentage</th>
                    <td><?= round($percentage, 2) ?>%</td>
                </tr>
                <tr>
                    <th>Remarks</th>
                    <td><?= round($percentage, 2) ?>%</td>
                </tr>
            </table>
            <a href="exam.php" class="btn btn-primary">Retake Exam</a>
            <a href="exam.php" class="btn btn-warning">Exam</a>

        </div>
    </div>
</div>

</body>
</html>
