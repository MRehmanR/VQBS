<?php
session_start();
include 'Include/db_config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $answer = $_POST['answer'];
    $score = $_POST['score'];
    $user_id = $_SESSION['user_id'];
    if (!isset($_SESSION['answers'])) {
        $_SESSION['answers'] = [];
    }
    if (!isset($_SESSION['score'])) {
        $_SESSION['score'] = 0;
    }
    if (!isset($_SESSION['attempted_questions'])) {
        $_SESSION['attempted_questions'] = 0;
    }

    $_SESSION['answers'][] = $answer;
    $_SESSION['score'] += $score;
    $_SESSION['attempted_questions']++;
    $stmt = $conn->prepare("INSERT INTO user_performance (user_id, attempted_questions, obtained_marks) 
                            VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE 
                            attempted_questions = attempted_questions + 1, 
                            obtained_marks = obtained_marks + ?");
    $stmt->bind_param("iiii", $user_id, $_SESSION['attempted_questions'], $_SESSION['score'], $score);
    $stmt->execute();
    $stmt->close();

    echo json_encode(["status" => "success"]);
}
?>
