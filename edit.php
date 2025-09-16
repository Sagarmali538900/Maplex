<?php
session_start();
if(!isset($_SESSION['admin'])) {
    header("Location: admin.php");
    exit;
}

$pdo = new PDO("mysql:host=localhost;dbname=repo_db","root","");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM files WHERE id=?");
$stmt->execute([$id]);
$file = $stmt->fetch(PDO::FETCH_ASSOC);

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $stmt = $pdo->prepare("UPDATE files SET path=?, keywords=? WHERE id=?");
    $stmt->execute([$_POST['path'], $_POST['keywords'], $id]);
    header("Location: manage.php");
    exit;
}
?>

<!-- Use same layout as add.php -->
<?php include 'layout.php'; ?>

<div class="content">
    <h3>Edit File</h3>
    <form method="post">
        <div class="mb-3">
            <label>Path</label>
            <input type="text" name="path" value="<?= htmlspecialchars($file['path']) ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Keywords</label>
            <input type="text" name="keywords" value="<?= htmlspecialchars($file['keywords']) ?>" class="form-control" required>
        </div>
        <button class="btn btn-success">Update</button>
        <a href="manage.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
