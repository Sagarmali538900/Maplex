<?php
session_start();
include 'db.php';

// Redirect if not logged in
if (!isset($_SESSION['admin'])) {
    header("Location: admin.php");
    exit();
}

// Handle delete
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM files WHERE id=?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage.php");
    exit();
}

// Fetch files
$files = [];
$result = $conn->query("SELECT * FROM files ORDER BY id DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $files[] = $row;
    }
}

// Capture page content
ob_start();
?>
<h2>Manage Files</h2>
<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Path</th>
            <th>Keywords</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($files)) : ?>
            <?php foreach ($files as $file) : ?>
                <tr>
                    <td><?= $file['id'] ?></td>
                    <td><?= htmlspecialchars($file['path']) ?></td>
                    <td><?= htmlspecialchars($file['keywords']) ?></td>
                    <td>
                        <a href="edit.php?id=<?= $file['id'] ?>" class="btn btn-primary btn-sm">Edit</a>
                        <a href="manage.php?delete_id=<?= $file['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr>
                <td colspan="4" class="text-center">No files added yet.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
<?php
$content = ob_get_clean();
include 'layout.php';
