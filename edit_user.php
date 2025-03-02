<?php
include 'Include/db_config.php';
include 'Include/header.php';

// Ensure only admin can access this page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    echo "<script>alert('Access Denied! Only teacher can access this page.'); window.location.href='dashboard.php';</script>";
    exit();
}

// Check if user ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('Invalid User ID'); window.location.href='manage_users.php';</script>";
    exit();
}

$user_id = $_GET['id'];

// Fetch user details
$result = $conn->query("SELECT * FROM users WHERE id = '$user_id'");
if ($result->num_rows == 0) {
    echo "<script>alert('User not found'); window.location.href='manage_users.php';</script>";
    exit();
}

$user = $result->fetch_assoc();

// Handle form submission for updating user
if (isset($_POST['update'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    $update_query = "UPDATE users SET name='$name', email='$email', role='$role' WHERE id='$user_id'";

    if ($conn->query($update_query)) {
        echo "<script>alert('User updated successfully'); window.location.href='manage_users.php';</script>";
    } else {
        echo "<script>alert('Error updating user');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Edit User</h2>
    <form method="POST">
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="role" class="form-label">Role</label>
            <select name="role" class="form-select">
                <option value="student" <?php echo ($user['role'] == 'student') ? 'selected' : ''; ?>>Student</option>
                <option value="teacher" <?php echo ($user['role'] == 'teacher') ? 'selected' : ''; ?>>Teacher</option>
            </select>
        </div>
        <button type="submit" name="update" class="btn btn-primary">Update User</button>
        <a href="manage_users.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
