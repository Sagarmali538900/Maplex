<?php
session_start();
include 'db.php';

// Redirect if not logged in
if (!isset($_SESSION['admin'])) {
    header("Location: admin.php");
    exit();
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $path = $_POST['path'] ?? '';
    $keywords = $_POST['keywords'] ?? '';

    if ($path && $keywords) {
        $stmt = $conn->prepare("INSERT INTO files (path, keywords) VALUES (?, ?)");
        $stmt->bind_param("ss", $path, $keywords);
        if ($stmt->execute()) {
            $message = "File added successfully!";
        } else {
            $message = "Failed to add file.";
        }
        $stmt->close();
    } else {
        $message = "All fields are required!";
    }
}

// Capture page content
ob_start();
?>
<h2>Add New File</h2>
<?php if ($message): ?>
    <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>
<form method="POST">
    <div class="mb-3">
        <label class="form-label">Path</label>
        <input type="text" name="path" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Keywords</label>
        <input type="text" name="keywords" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-success">Add File</button>
</form>
<?php
$content = ob_get_clean();
include 'layout.php';
