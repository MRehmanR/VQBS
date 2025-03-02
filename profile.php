<?php
include 'Include/db_config.php';
include 'Include/header.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    echo "Access Denied!";
    exit;
}

$student_id = $_SESSION['role'];
$message = "";
$query = "SELECT name, email FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$stmt->bind_result($name, $email);
$stmt->fetch();
$stmt->close();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_name = trim($_POST['name']);
    $new_email = trim($_POST['email']);

    // Check if email already exists (excluding current student)
    $email_check = "SELECT id FROM users WHERE email = ? AND id != ?";
    $stmt = $conn->prepare($email_check);
    $stmt->bind_param("si", $new_email, $student_id);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $message = "<div class='alert alert-danger'>Email already in use!</div>";
    } else {
        $update_query = "UPDATE users SET name = ?, email = ? WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("ssi", $new_name, $new_email, $student_id);
        
        if ($stmt->execute()) {
            $_SESSION['name'] = $new_name;
            $message = "<div class='alert alert-success'>Profile updated successfully!</div>";
        } else {
            $message = "<div class='alert alert-danger'>Error updating profile!</div>";
        }
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="Styles/styles.css">
</head>
<body>
<div class="container mt-5">
    <h2>Student Profile</h2>
    <?php echo $message; ?>
    <form method="post">
        <div class="mb-3">
            <label for="name" class="form-label">Full Name</label>
            <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($name); ?>" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($email); ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Profile</button>
        <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </form>
</div>
</body>
</html>
