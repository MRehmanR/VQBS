<?php
include 'Include/header.php';
include 'Include/db_config.php';
$role = $_SESSION['role'] ?? 'student'; // Default role is student

// Handle Adding Announcement
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add'])) {
    if ($role !== 'teacher') {
        die("Unauthorized action.");
    }
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);

    $conn->query("INSERT INTO announcements (title, content) VALUES ('$title', '$content')");
}

// Handle Editing Announcement
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit'])) {
    if ($role !== 'teacher') {
        die("Unauthorized action.");
    }
    $id = (int)$_POST['id'];
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);

    $conn->query("UPDATE announcements SET title='$title', content='$content' WHERE id=$id");
}

// Handle Deleting Announcement
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    if ($role !== 'teacher') {
        die("Unauthorized action.");
    }
    $id = (int)$_POST['id'];
    $conn->query("DELETE FROM announcements WHERE id=$id");
}

// Fetch Announcements
$result = $conn->query("SELECT * FROM announcements ORDER BY created_at DESC");
$announcements = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements</title>
    <link rel="stylesheet" href="Styles/styles.css">
   
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-4">
    <h2 class="text-center">📢 Announcements</h2>

    <?php if ($role === 'teacher') : ?>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addModal">➕ Post Announcement</button>
    <?php endif; ?>

    <?php foreach ($announcements as $announcement) : ?>
        <div class="card mb-3">
            <div class="card-header">
                <strong><?= htmlspecialchars($announcement['title']) ?></strong>
                <small class="text-muted"> | <?= $announcement['created_at'] ?></small>
            </div>
            <div class="card-body">
                <p><?= nl2br(htmlspecialchars($announcement['content'])) ?></p>

                <?php if ($role === 'teacher') : ?>
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $announcement['id'] ?>">✏️ Edit</button>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $announcement['id'] ?>">
                        <button type="submit" name="delete" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">🗑 Delete</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- Edit Modal -->
        <div class="modal fade" id="editModal<?= $announcement['id'] ?>" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">✏️ Edit Announcement</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form method="post">
                            <input type="hidden" name="id" value="<?= $announcement['id'] ?>">
                            <div class="mb-3">
                                <label class="form-label">Title:</label>
                                <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($announcement['title']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Content:</label>
                                <textarea name="content" class="form-control" rows="5" required><?= htmlspecialchars($announcement['content']) ?></textarea>
                            </div>
                            <button type="submit" name="edit" class="btn btn-success">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">➕ Post New Announcement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">Title:</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Content:</label>
                        <textarea name="content" class="form-control" rows="5" required></textarea>
                    </div>
                    <button type="submit" name="add" class="btn btn-success">📢 Post Announcement</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
