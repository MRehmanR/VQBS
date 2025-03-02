<?php

include('Include/header.php');
include('Include/db_config.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    echo "<script>alert('Access Denied! Only teacher can access this page.'); window.location.href='index.php';</script>";
    exit();
}

$user_role = $_SESSION['role'];

if ($user_role === 'admin') {
    $progressQuery = "SELECT * FROM user_progress ORDER BY user_id, attempt_number ASC";
} else {
    $progressQuery = "SELECT * FROM user_progress WHERE user_id = ? ORDER BY attempt_number ASC";
    $stmt = $conn->prepare($progressQuery);
    $stmt->bind_param("i", $user_id);
}

$stmt->execute();
$userProgress = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);


if ($user_role === 'admin') {
    $averageQuery = "SELECT AVG(percentage) AS average_score FROM user_progress";
    $stmt = $conn->prepare($averageQuery);
} else {
    $averageQuery = "SELECT AVG(percentage) AS average_score FROM user_progress WHERE user_id = ?";
    $stmt = $conn->prepare($averageQuery);
    $stmt->bind_param("i", $user_id);
}

$stmt->execute();
$averageResult = $stmt->get_result()->fetch_assoc();
$averageScore = $averageResult['average_score'] ?? 0;
?>


<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="Styles/styles.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="analytics-container">
    <h2>User Performance</h2>

    <table>
        <tr>
            <th>Attempt</th>
            <th>Obtained Marks</th>
            <th>Total Marks</th>
            <th>Percentage</th>
            <th>Date</th>
        </tr>
        <?php foreach ($userProgress as $progress): ?>
        <tr>
            <td><?php echo $progress['attempt_number']; ?></td>
            <td><?php echo htmlspecialchars($progress['obtained_marks']); ?></td>
            <td><?php echo htmlspecialchars($progress['total_marks']); ?></td>
            <td><?php echo number_format($progress['percentage'], 2); ?>%</td>
            <td><?php echo htmlspecialchars($progress['attempt_date']); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <div class="average-section">
        <h3>Your Average Score: <?php echo number_format($averageScore, 2); ?>%</h3>
    </div>

    <canvas id="progressGraph" style="width: 800px; max-height: 400px; margin: auto;"></canvas>

<script>
    
    const ctx = document.getElementById('progressGraph').getContext('2d');
    const labels = <?php echo json_encode(array_column($userProgress, 'attempt_number')); ?>;
    const data = <?php echo json_encode(array_column($userProgress, 'percentage')); ?>;

    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Progress Over Attempts',
                data: data,
                borderColor: 'rgba(75, 192, 192, 1)',
                tension: 0.1
            }]
        },
        options: {
            maintainAspectRatio: false, 
            responsive: true,          
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Attempt Number'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Percentage (%)'
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top'
                }
            }
        }
    });
</script>
