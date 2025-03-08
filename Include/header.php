<?php
session_start();
if (!isset($_SESSION['role'])) {
    $_SESSION['role'] = 'guest';
}

$role = $_SESSION['role'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VQBS</title>
   
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="bi bi-bank"></i> Virtual Question Bank
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php if ($role === 'teacher') { ?>
                    <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_questions.php"><i class="bi bi-plus-square"></i>Questions</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_users.php"><i class="bi bi-people"></i> Users</a></li>
                    <li class="nav-item"><a class="nav-link" href="user_performance.php"><i class="bi bi-file-earmark-bar-graph"></i> Reports</a></li>
                    <li class="nav-item"><a class="nav-link" href="announcement.php"><i class="bi bi-bell"></i> Annoucment</a></li>
                    <li class="nav-item"><a class="nav-link" href="feedback.php"><i class="bi bi-chat-dots"></i>feedback</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>

                   

                <?php } elseif ($role === 'student') { ?>
                    <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="bi bi-person"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="profile.php"><i class="bi bi-person-circle"></i> Profile </a></li>
                    <li class="nav-item"><a class="nav-link" href="exam.php"><i class="bi bi-book"></i> Exam</a></li>
                    <li class="nav-item"><a class="nav-link" href="result.php"><i class="bi bi-clipboard-check"></i>Results</a></li>
                    <li class="nav-item"><a class="nav-link" href="announcement.php"><i class="bi bi-bell"></i>Annoucment</a></li>
                    <li class="nav-item"><a class="nav-link" href="feedback.php"><i class="bi bi-chat-dots"></i>Feedback</a></li>
                    <li class="nav-item"><a class="nav-link" href="user_performance.php"><i class="bi bi-chat-dots"></i>Report</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>

                <?php } else { ?>
                    <li class="nav-item"><a class="nav-link" href="about.php"><i class="bi bi-info-circle"></i> About Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php"><i class="bi bi-envelope"></i> Contact</a></li>
                    <li class="nav-item"><a class="nav-link" href="login.php"><i class="bi bi-box-arrow-in-right"></i> Login</a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
</nav>

</body>
</html>
