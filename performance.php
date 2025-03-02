<?php
include 'Include/header.php';
include 'Include/db_config.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    echo "<script>alert('Access Denied! Only students can access this page.'); window.location.href='index.php';</script>";
    exit();
}

// Fetch user performance data
$query = "SELECT attempted_questions, obtained_marks FROM user_performance WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

$attempted = $result['attempted_questions'] ?? 0;
$obtained = $result['obtained_marks'] ?? 0;
$total = max($attempted, 1); // Avoid division by zero
$percentage = ($obtained / $total) * 100;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Performance Tracker</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">User Performance</h2>
        <canvas id="performanceChart"></canvas>
    </div>

    <script>
        const ctx = document.getElementById('performanceChart').getContext('2d');
        const performanceChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Total Questions', 'Attempted Questions', 'Obtained Marks', 'Percentage'],
                datasets: [{
                    label: 'Performance Data',
                    data: [<?= $total ?>, <?= $attempted ?>, <?= $obtained ?>, <?= round($percentage, 2) ?>],
                    backgroundColor: ['#007bff', '#ffc107', '#28a745', '#dc3545']
                }]
            }
        });
    </script>
</body>
</html>
