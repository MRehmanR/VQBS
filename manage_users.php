<?php
include 'Include/header.php';
include 'Include/db_config.php';
if (empty($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    echo "<script>alert('Access Denied! Only teacher can manage users.'); window.location.href='index.php';</script>";
    exit();
}
if (isset($_GET['delete_id'])) {
    $user_id = intval($_GET['delete_id']);
    $conn->query("DELETE FROM users WHERE id = '$user_id'");
    echo "<script>alert('User deleted successfully!'); window.location.href='manage_users.php';</script>";
}
$filter = isset($_GET['role']) ? $_GET['role'] : '';
$sql = "SELECT * FROM users";
if (!empty($filter)) {
    $sql .= " WHERE role = '$filter'";
}
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="Styles/styles.css">
</head>
<body>
<div class="container mt-5">
    <h2>Manage Users</h2>
    <a href="add_user.php" class="btn btn-primary mb-3">Add New User</a>
    <form method="GET" class="mb-3">
        <label>Filter by Role:</label>
        <select name="role" onchange="this.form.submit()" class="form-select w-25">
            <option value="">All</option>
            <option value="teacher" <?= ($filter == 'teacher') ? 'selected' : '' ?>>Teacher</option>
            <option value="student" <?= ($filter == 'student') ? 'selected' : '' ?>>Student</option>
        </select>
    </form>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['name'] ?></td>
                <td><?= $row['email'] ?></td>
                <td><?= ucfirst($row['role']) ?></td>
                <td>
                    <a href="edit_user.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="manage_users.php?delete_id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
</body>
</html>
