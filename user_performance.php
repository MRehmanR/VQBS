<?php
include 'Include/db_config.php';
include 'Include/header.php';

// Session se user ka role aur ID lena
$user_id = isset($_SESSION['id']) ? $_SESSION['id'] : 0;
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : '';

// Agar teacher hai, to sab students ka data fetch karo
$users = [];
if ($user_role == 'teacher') {
    $query = "SELECT id, name FROM users WHERE role = 'student'";
    $result = $conn->query($query);
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

// Fetch Performance Data
$attempts = [];
$selected_id = $user_id; // Default student apni hi performance dekhega

// Teacher koi bhi student select kar sakta hai
if ($user_role == 'teacher' && !empty($_POST['id'])) {
    $selected_id = $_POST['id'];
}

// Fetching user performance data
$query = "SELECT attempt_number, total_marks, obtained_marks, percentage, attempt_date 
          FROM user_performance 
          WHERE id = ? 
          ORDER BY attempt_number ASC"; // Ordered correctly for graph

$stmt = $conn->prepare($query);
if ($stmt) {
    $stmt->bind_param("i", $selected_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $attempts[] = $row;
    }
    $stmt->close();
} else {
    die("Error fetching data: " . $conn->error);
}

// Convert data for Chart.js
$attemptNumbers = [];
$percentages = [];

foreach ($attempts as $attempt) {
    $attemptNumbers[] = "Attempt " . $attempt['attempt_number'];
    $percentages[] = round($attempt['percentage'], 2);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Performance</title>
    <link rel="stylesheet" href="Styles/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Chart.js -->
</head>
<body>
    <div class="container mt-5">
        <h2>User Performance</h2>

        <?php if ($user_role == 'teacher'): ?>
        <form method="POST" class="mb-3">
            <label>Select Student:</label>
            <select name="id" class="form-control" onchange="this.form.submit()">
                <option value="">-- Select Student --</option>
                <?php foreach ($users as $user): ?>
                    <option value="<?= $user['id']; ?>" <?= ($selected_id == $user['id']) ? 'selected' : ''; ?>>
                        <?= $user['name']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
        <?php endif; ?>
        
        <?php if (!empty($attempts)): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Attempt No.</th>
                        <th>Total Marks</th>
                        <th>Obtained Marks</th>
                        <th>Percentage</th>
                        <th>Attempt Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attempts as $attempt): ?>
                        <tr>
                            <td><?= $attempt['attempt_number']; ?></td>
                            <td><?= $attempt['total_marks']; ?></td>
                            <td><?= $attempt['obtained_marks']; ?></td>
                            <td><?= round($attempt['percentage'], 2); ?>%</td>
                            <td><?= $attempt['attempt_date']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Graph Section -->
            <h3>Performance</h3>
            <canvas id="performanceChart"></canvas>
            <div style="width: 500px; height: 300px; margin: auto;">


            <script>
                var ctx = document.getElementById('performanceChart').getContext('2d');
                var performanceChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: <?= json_encode($attemptNumbers); ?>,
                        datasets: [{
                            label: 'Percentage (%)',
                            data: <?= json_encode($percentages); ?>,
                            borderColor: 'green',
                            borderWidth: 3,
                            fill: false,
                            pointBackgroundColor: 'green',
                            pointRadius: 5,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top'
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false
                            }
                        },
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'Attempt Number'
                                }
                            },
                            y: {
                                beginAtZero: true,
                                suggestedMax: 100,
                                title: {
                                    display: true,
                                    text: 'Percentage (%)'
                                }
                            }
                        }
                    }
                });
            </script>

        <?php else: ?>
            <p>No records found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
