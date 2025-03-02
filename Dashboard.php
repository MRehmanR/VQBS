<?php
include 'Include/header.php';
$user_name = isset($_SESSION['name']) ? $_SESSION[('name')] : "Guest";
$role = isset($_SESSION['role']) ? $_SESSION['role'] : "guest";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="Styles/styles.css">
</head>
<body>
<div class="container mt-4">
    <h2 class="text-center">Welcome, <?php echo htmlspecialchars($user_name); ?> 🎉</h2>

    <?php if ($role == 'teacher'): ?>
        <!-- Teacher Dashboard -->
        <div class="card p-4 shadow mt-3">
            <h3 class="text-primary"><i class="bi bi-mortarboard"></i> Teacher Dashboard</h3>
            <p>Manage students, assignments, and exams here.</p>
            <div class="d-flex flex-wrap gap-3">
                <a href="add_question.php" class="btn btn-primary"><i class="bi bi-plus-square"></i> Add Questions</a>
                <a href="manage_students.php" class="btn btn-secondary"><i class="bi bi-people"></i> Manage Students</a>
                <a href="view_reports.php" class="btn btn-success"><i class="bi bi-file-earmark-bar-graph"></i> View Reports</a>
            </div>
        </div>

    <?php elseif ($role == 'student'): ?>
        <!-- Student Dashboard -->
        <div class="card p-4 shadow mt-3">
            <h3 class="text-success"><i class="bi bi-book"></i> Student Dashboard</h3>
            <p>Access exams, study materials, and results here.</p>
            <div class="d-flex flex-wrap gap-3">
                <a href="exam.php" class="btn btn-primary"><i class="bi bi-pencil"></i> Take Exam</a>
                <a href="result.php" class="btn btn-secondary"><i class="bi bi-bar-chart"></i> View Results</a>
                <a href="download_notes.php" class="btn btn-success"><i class="bi bi-file-earmark-arrow-down"></i> Notes</a>
            </div>
        </div>

    <?php else: ?>
        <!-- Guest View -->
        <div class="alert alert-warning mt-3 text-center">
            <i class="bi bi-exclamation-triangle"></i> You are not logged in. Please <a href="login.php">log in</a> to access the dashboard.
        </div>
    <?php endif; ?>
</div>

</body>
</html>
