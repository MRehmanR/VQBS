<?php
session_start();
include 'Include/header.php';
include 'Include/db_config.php';

// Ensure only admin can delete users
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    echo "<script>alert('Access Denied! Only admins can access this page.'); window.location.href='dashboard.php';</script>";
    exit();
}

// Check if user ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('Invalid User ID'); window.location.href='manage_users.php';</script>";
    exit();
}

$user_id = $_GET['id'];

// Prevent admin from deleting themselves
if ($user_id == $_SESSION['user_id']) {
    echo "<script>alert('You cannot delete your own account!'); window.location.href='manage_users.php';</script>";
    exit();
}

// Delete user query
$delete_query = "DELETE FROM users WHERE id = '$user_id'";

if ($conn->query($delete_query)) {
    echo "<script>alert('User deleted successfully'); window.location.href='manage_users.php';</script>";
} else {
    echo "<script>alert('Error deleting user'); window.location.href='manage_users.php';</script>";
}
?>
