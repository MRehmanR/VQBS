<?php
include 'Include/db_config.php';
include 'Include/header.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    echo "<script>alert('Access Denied! Only teacher can access this page.'); window.location.href='dashboard.php';</script>";
    exit();
}

if (isset($_POST['add_user'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Secure password hashing
    $role = $_POST['role'];
    $check_query = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($check_query);

    if ($result->num_rows > 0) {
        echo "<script>alert('Error: Email already exists!');</script>";
    } else {
        $insert_query = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', '$role')";
        
        if ($conn->query($insert_query)) {
            echo "<script>alert('User added successfully!'); window.location.href='manage_users.php';</script>";
        } else {
            echo "<script>alert('Error adding user.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New User</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="Styles/styles.css">
</head>
<body>
<div class="container mt-5">
    <h2>Add New User</h2>
    <form method="POST">
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="role" class="form-label">Role</label>
            <select name="role" class="form-select">
                <option value="student">Student</option>
                <option value="teacher">Teacher</option>
            </select>
        </div>
        <button type="submit" name="add_user" class="btn btn-success">Add User</button>
        <a href="manage_users.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
