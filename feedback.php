<?php
include 'Include/header.php';
include 'Include/db_config.php';
$role = $_SESSION['role'] ?? 'student'; 
$student_name = $_SESSION['name'] ?? 'Anonymous';

// Handle Adding Feedback
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_feedback'])) {
    if ($role !== 'student') die("Unauthorized action.");
    $feedback = $conn->real_escape_string($_POST['feedback']);
    $conn->query("INSERT INTO feedback (student_name, feedback) VALUES ('$student_name', '$feedback')");
}

// Handle Editing Feedback
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_feedback'])) {
    if ($role !== 'student') die("Unauthorized action.");
    $id = (int)$_POST['id'];
    $feedback = $conn->real_escape_string($_POST['feedback']);
    $conn->query("UPDATE feedback SET feedback='$feedback' WHERE id=$id");
}

// Handle Deleting Feedback (Student or Teacher)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_feedback'])) {
    $id = (int)$_POST['id'];
    $conn->query("DELETE FROM feedback WHERE id=$id");
}

// Handle Teacher Reply
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reply_feedback'])) {
    if ($role !== 'teacher') die("Unauthorized action.");
    $id = (int)$_POST['id'];
    $reply = $conn->real_escape_string($_POST['teacher_reply']);
    $conn->query("UPDATE feedback SET teacher_reply='$reply' WHERE id=$id");
}

// Fetch All Feedback
$result = $conn->query("SELECT * FROM feedback ORDER BY created_at DESC");
$feedbacks = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="Styles/styles.css">


</head>
<body>

<div class="container mt-4">
    <h2 class="text-center">💬 Feedback</h2>

    <!-- Student Feedback Form -->
    <?php if ($role === 'student') : ?>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addModal">➕ Add Feedback</button>
    <?php endif; ?>

    <!-- Feedback List -->
    <?php foreach ($feedbacks as $feedback) : ?>
        <div class="card mb-3">
            <div class="card-header">
                <strong>🧑‍🎓 <?= htmlspecialchars($feedback['student_name']) ?></strong>
                <small class="text-muted"> | <?= $feedback['created_at'] ?></small>
            </div>
            <div class="card-body">
                <p><?= nl2br(htmlspecialchars($feedback['feedback'])) ?></p>

                <!-- Teacher Reply -->
                <?php if ($feedback['teacher_reply']) : ?>
                    <div class="alert alert-success">
                        <strong>👨‍🏫 Teacher Reply:</strong> <?= nl2br(htmlspecialchars($feedback['teacher_reply'])) ?>
                    </div>
                <?php endif; ?>

                <!-- Edit/Delete for Student -->
                <?php if ($role === 'student' && $feedback['student_name'] === $student_name) : ?>
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $feedback['id'] ?>">✏️ Edit</button>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $feedback['id'] ?>">
                        <button type="submit" name="delete_feedback" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">🗑 Delete</button>
                    </form>
                <?php endif; ?>

                <!-- Teacher Reply Form -->
                <?php if ($role === 'teacher') : ?>
                    <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#replyModal<?= $feedback['id'] ?>">💬 Reply</button>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $feedback['id'] ?>">
                        <button type="submit" name="delete_feedback" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">🗑 Delete</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- Edit Modal -->
        <div class="modal fade" id="editModal<?= $feedback['id'] ?>" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">✏️ Edit Feedback</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form method="post">
                            <input type="hidden" name="id" value="<?= $feedback['id'] ?>">
                            <div class="mb-3">
                                <label class="form-label">Feedback:</label>
                                <textarea name="feedback" class="form-control" rows="3" required><?= htmlspecialchars($feedback['feedback']) ?></textarea>
                            </div>
                            <button type="submit" name="edit_feedback" class="btn btn-success">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reply Modal -->
        <div class="modal fade" id="replyModal<?= $feedback['id'] ?>" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">💬 Reply to Feedback</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form method="post">
                            <input type="hidden" name="id" value="<?= $feedback['id'] ?>">
                            <div class="mb-3">
                                <label class="form-label">Reply:</label>
                                <textarea name="teacher_reply" class="form-control" rows="3" required></textarea>
                            </div>
                            <button type="submit" name="reply_feedback" class="btn btn-info">Reply</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    <?php endforeach; ?>
</div>

<!-- Add Feedback Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">➕ Add Feedback</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">Feedback:</label>
                        <textarea name="feedback" class="form-control" rows="3" required></textarea>
                    </div>
                    <button type="submit" name="add_feedback" class="btn btn-success">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
