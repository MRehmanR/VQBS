<?php
session_start();
include 'Include/db_config.php';

if (empty($_SESSION['role']) || $_SESSION['role'] != 'teacher') {
    echo "<script>alert('Access Denied! Only teachers can access this page.'); window.location.href='index.php';</script>";
    exit();
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $question_id = intval($_GET['id']);

    $delete_query = "DELETE FROM questions WHERE id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $question_id);

    if ($stmt->execute()) {
        echo "<script>alert('Question Deleted Successfully!'); window.location.href='manage_questions.php';</script>";
    } else {
        echo "<script>alert('Error Deleting Question!'); window.location.href='manage_questions.php';</script>";
    }
} else {
    echo "<script>alert('Invalid Question ID!'); window.location.href='manage_questions.php';</script>";
}
?>
