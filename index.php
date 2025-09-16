<?php
include 'db.php'; // gives $conn (MySQLi)

$search = trim($_GET['search'] ?? '');
$files = [];

if ($search !== '') {
    $keywords = preg_split('/\s+/', $search);
    $conds = [];
    $params = [];
    $types  = '';

    foreach ($keywords as $kw) {
        if ($kw === '') continue;
        $conds[] = "(path LIKE ? OR keywords LIKE ?)";
        $params[] = "%$kw%";
        $params[] = "%$kw%";
        $types   .= "ss";
    }

    if ($conds) {
        $sql = "SELECT * FROM files WHERE " . implode(" OR ", $conds) . " ORDER BY id DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $files[] = $row;
        }
        $stmt->close();
    }
}

function highlight($text, $kws) {
    $e = htmlspecialchars($text);
    foreach ($kws as $kw) {
        $kw = trim($kw); if ($kw === '') continue;
        $e = preg_replace('/(' . preg_quote($kw, '/') . ')/i', '<mark>$1</mark>', $e);
    }
    return $e;
}

ob_start();
?>
<div class="card p-3 shadow-sm">
  <h4 class="mb-3">🔍 Search Files</h4>
  <form method="get" class="row g-2 mb-3">
    <div class="col-sm-8">
        <input type="text" name="search" class="form-control" 
               placeholder="Enter keywords..." 
               value="<?= htmlspecialchars($search) ?>">
    </div>
    <div class="col-sm-2">
        <button class="btn btn-primary w-100">Search</button>
    </div>
    <div class="col-sm-2">
        <a href="index.php" class="btn btn-outline-secondary w-100">Clear</a>
    </div>
  </form>

  <?php if ($search && empty($files)): ?>
    <div class="alert alert-warning">No results for "<b><?= htmlspecialchars($search) ?></b>".</div>
  <?php elseif (!empty($files)): $kws = preg_split('/\s+/', $search); ?>
    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead class="table-dark">
          <tr>
            <th>Path</th>
            <th>Keywords</th>
            <th>Copy</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($files as $f): ?>
          <tr>
            <td><?= highlight($f['path'], $kws) ?></td>
            <td><?= highlight($f['keywords'], $kws) ?></td>
            <td>
              <button class="btn btn-sm btn-success copy-btn" 
                      data-clipboard-text="<?= htmlspecialchars($f['path']) ?>">
                      Copy
              </button>
              <span class="copy-confirm text-success ms-2">Copied!</span>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- ⚠️ Note for drive letters -->
    <div class="alert alert-info mt-3">
      <strong>Note:</strong> Drive letters (e.g., <code>C:</code>, <code>D:</code>, <code>E:</code>) 
      may differ from one computer to another. Please update the drive name after copying the path.
    </div>

  <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
include 'layout.php';
?>
