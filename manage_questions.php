<?php
include 'Include/db_config.php';
include 'Include/header.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    echo "<script>alert('Access Denied! Only teachers can access this page.'); window.location.href='index.php';</script>";
    exit();
}

// Show success message when a question is added
if (isset($_GET['success']) && $_GET['success'] == 1) {
    echo "<script>alert('Question added successfully!');</script>";
}

// Fetch subjects and topics
$subjects = $conn->query("SELECT DISTINCT subject FROM questions");
$topics = $conn->query("SELECT DISTINCT topic FROM questions");

// Apply filters if set
$where = [];
if (!empty($_GET['subject'])) {
    $where[] = "subject = '" . $conn->real_escape_string($_GET['subject']) . "'";
}
if (!empty($_GET['topic'])) {
    $where[] = "topic = '" . $conn->real_escape_string($_GET['topic']) . "'";
}
if (!empty($_GET['difficulty'])) {
    $where[] = "difficulty = '" . $conn->real_escape_string($_GET['difficulty']) . "'";
}
$where_clause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

// Fetch questions
$query = "SELECT id, type, question, subject, topic, difficulty, option1, option2, option3, option4, correct_option
          FROM questions
          $where_clause
          ORDER BY subject, topic, difficulty";

$questions = $conn->query($query);

// Debugging SQL errors
if (!$questions) {
    die("Query Error: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Questions</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="Styles/styles.css">
</head>
<body>

<div class="container mt-5">
    <h2>Manage Questions</h2>

    <!-- Add New Question Button -->
    <a href="add_question.php" class="btn btn-success mb-3">Add New Question</a>

    <!-- Filter Form -->
    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-3">
            <label class="form-label">Filter by Subject</label>
            <select name="subject" class="form-select">
                <option value="">All Subjects</option>
                <?php while ($row = $subjects->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($row['subject']) ?>" <?= (isset($_GET['subject']) && $_GET['subject'] == $row['subject']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($row['subject']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Filter by Topic</label>
            <select name="topic" class="form-select">
                <option value="">All Topics</option>
                <?php while ($row = $topics->fetch_assoc()): ?>
                    <option value="<?= htmlspecialchars($row['topic']) ?>" <?= (isset($_GET['topic']) && $_GET['topic'] == $row['topic']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($row['topic']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Filter by Difficulty</label>
            <select name="difficulty" class="form-select">
                <option value="">All Levels</option>
                <option value="Easy" <?= (isset($_GET['difficulty']) && $_GET['difficulty'] == 'Easy') ? 'selected' : '' ?>>Easy</option>
                <option value="Medium" <?= (isset($_GET['difficulty']) && $_GET['difficulty'] == 'Medium') ? 'selected' : '' ?>>Medium</option>
                <option value="Hard" <?= (isset($_GET['difficulty']) && $_GET['difficulty'] == 'Hard') ? 'selected' : '' ?>>Hard</option>
            </select>
        </div>

        <div class="col-md-3 align-self-end">
            <button type="submit" class="btn btn-primary">Apply Filters</button>
        </div>
    </form>

    <!-- Questions Table -->
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Type</th>
                <th>Question</th>
                <th>Subject</th>
                <th>Topic</th>
                <th>Difficulty</th>
                <th>Options</th>
                <th>Correct Answer</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($questions->num_rows > 0): ?>
                <?php while ($row = $questions->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['type']) ?></td>
                        <td><?= htmlspecialchars($row['question']) ?></td>
                        <td><?= htmlspecialchars($row['subject']) ?></td>
                        <td><?= htmlspecialchars($row['topic']) ?></td>
                        <td><?= htmlspecialchars($row['difficulty']) ?></td>
                        <td>
                            <?php if ($row['type'] == 'objective'): ?>
                                1. <?= htmlspecialchars($row['option1']) ?><br>
                                2. <?= htmlspecialchars($row['option2']) ?><br>
                                3. <?= htmlspecialchars($row['option3']) ?><br>
                                4. <?= htmlspecialchars($row['option4']) ?>
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </td>
                        <td><?= $row['correct_option'] ? strtoupper($row['correct_option']) : 'N/A' ?></td>
                        <td>
                            <a href="edit_question.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="delete_question.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="9" class="text-center">No questions found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

</div>

</body>
</html>
