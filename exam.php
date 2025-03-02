<?php

include 'Include/db_config.php';
include 'Include/header.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    echo "<script>alert('Access Denied! Only students can access this page.'); window.location.href='index.php';</script>";
    exit();
}

$subjects = $conn->query("SELECT subject_name FROM subjects");
$topics = $conn->query("SELECT topic_name FROM topics");

$where = [];
if (!empty($_POST['subject'])) {
    $where[] = "subject = '" . $conn->real_escape_string($_POST['subject']) . "'";
}
if (!empty($_POST['topic'])) {
    $where[] = "topic = '" . $conn->real_escape_string($_POST['topic']) . "'";
}
$where_clause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

$query = "SELECT id, question, type, option1, option2, option3, option4, correct_option FROM questions $where_clause ORDER BY RAND()";
$questions = $conn->query($query);

$questions_array = [];
while ($row = $questions->fetch_assoc()) {
    $questions_array[] = $row;
}
$_SESSION['questions'] = $questions_array;
$_SESSION['current_question'] = 0;
$_SESSION['score'] = 0;
$_SESSION['attempted_questions'] = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Exam</title>
    <link rel="stylesheet" href="Styles/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { background-color: #f8f9fa; }
        .exam-container {
            max-width: 600px; background: #fff; padding: 20px;
            border-radius: 10px; box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .timer { font-size: 18px; font-weight: bold; color: red; text-align: right; }
        .btn-next { width: 100%; }
    </style>
</head>
<body>

<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="exam-container">
        <h2 class="text-center">Practice Exam</h2>
        <form method="POST" id="examFilters">
            <div class="mb-3">
                <label class="form-label">Select Subject:</label>
                <select name="subject" class="form-select" required>
                    <option value="">Select Subject</option>
                    <?php while ($row = $subjects->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($row['subject_name']) ?>"><?= htmlspecialchars($row['subject_name']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Select Topic:</label>
                <select name="topic" class="form-select" required>
                    <option value="">Select Topic</option>
                    <?php while ($row = $topics->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($row['topic_name']) ?>"><?= htmlspecialchars($row['topic_name']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-100">Start Exam</button>
        </form>

        <div id="examContainer" style="display:none;">
            <p class="timer" id="timer">90s</p>
            <h4 id="questionText" class="mt-3"></h4>
            <div id="options" class="mt-2"></div>
            <button id="nextBtn" class="btn btn-success mt-3 btn-next">Next</button>
        </div>
    </div>
</div>

<script>
let questions = <?php echo json_encode($_SESSION['questions']); ?>;
let currentIndex = 0;
let timer;

$(document).ready(function () {
    if (questions.length > 0) {
        $('#examFilters').hide();
        $('#examContainer').show();
        loadQuestion();
    }
});

function loadQuestion() {
    if (currentIndex >= questions.length) {
        window.location.href = 'result.php';
        return;
    }

    let question = questions[currentIndex];
    $('#questionText').text(question.question);
    let optionsHtml = '';

    if (question.type === 'objective') {
        optionsHtml += `<div class="form-check">
                            <input class="form-check-input" type="radio" name="answer" value="option1"> 
                            <label class="form-check-label">${question.option1}</label>
                        </div>`;
        optionsHtml += `<div class="form-check">
                            <input class="form-check-input" type="radio" name="answer" value="option2"> 
                            <label class="form-check-label">${question.option2}</label>
                        </div>`;
        optionsHtml += `<div class="form-check">
                            <input class="form-check-input" type="radio" name="answer" value="option3"> 
                            <label class="form-check-label">${question.option3}</label>
                        </div>`;
        optionsHtml += `<div class="form-check">
                            <input class="form-check-input" type="radio" name="answer" value="option4"> 
                            <label class="form-check-label">${question.option4}</label>
                        </div>`;
    } else {
        optionsHtml += `<textarea name="answer" class="form-control mt-2"></textarea>`;
    }

    $('#options').html(optionsHtml);
    startTimer();
}

function startTimer() {
    let timeLeft = 90;
    clearInterval(timer);
    $('#timer').text(timeLeft + 's');
    timer = setInterval(() => {
        timeLeft--;
        $('#timer').text(timeLeft + 's');
        if (timeLeft <= 0) {
            clearInterval(timer);
            saveAnswer("Not Attempted");
            nextQuestion();
        }
    }, 1000);
}

$('#nextBtn').click(function () {
    let selectedAnswer = $('input[name="answer"]:checked').val();
    saveAnswer(selectedAnswer || "Not Attempted");
    nextQuestion();
});

function saveAnswer(answer) {
    let correctAnswer = questions[currentIndex].correct_option;
    let score = (answer === correctAnswer) ? 1 : 0;
    
    $.post('update_score.php', { answer: answer, score: score });
    
    currentIndex++;
    loadQuestion();
}
</script>

</body>
</html>
