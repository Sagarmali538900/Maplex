<?php
// layout.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Maplex</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background:#f8f9fa; }
    @media (min-width: 768px) {
      .sidebar {
        width: 250px; position: fixed; top:0; left:0; height:100vh;
        background:#212529; padding-top:60px;
      }
      .content { 
          margin-left:250px; 
          padding: 100px 40px 40px; /* increased padding */
      }
      .sidebar .nav-link { color:#e9eef6; padding:10px 18px; font-size:1.15rem; }
      .sidebar .nav-link:hover { background:rgba(255,255,255,0.06); color:#fff; border-radius:6px; }
    }
    @media (max-width:767px){ 
        .content{padding:100px 20px 20px;} /* mobile padding */
    }
    .copy-confirm{display:none; font-weight:600;}
    table.table { width:100%; }
  </style>
</head>
<body>
<nav class="navbar navbar-dark bg-dark fixed-top">
  <div class="container-fluid">
    <button class="btn btn-outline-light d-md-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">☰</button>
    <span class="navbar-brand ms-2 fw-bold d-md-none" style="font-size:2rem;">Maplex</span>
  </div>
</nav>
<div class="sidebar d-none d-md-block">
  <h4 class="fw-bold text-white px-3 mb-4" style="font-size:2rem;">Maplex</h4>
  <ul class="nav flex-column">
    <li class="nav-item"><a class="nav-link" href="index.php">🔍 Search</a></li>
    <li class="nav-item"><a class="nav-link" href="add.php">➕ Add Path</a></li>
    <li class="nav-item"><a class="nav-link" href="manage.php">🗂️ Manage</a></li>
    <li class="nav-item"><a class="nav-link" href="export.php">⬇️ Export CSV</a></li>
    <li class="nav-item"><a class="nav-link" href="logout.php">🚪 Logout</a></li>
  </ul>
</div>
<div class="offcanvas offcanvas-start bg-dark text-white" id="mobileSidebar">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" style="font-size:2rem;">Maplex</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body">
    <ul class="nav flex-column">
      <li class="nav-item"><a class="nav-link text-white" href="index.php">🔍 Search</a></li>
      <li class="nav-item"><a class="nav-link text-white" href="add.php">➕ Add Path</a></li>
      <li class="nav-item"><a class="nav-link text-white" href="manage.php">🗂️ Manage</a></li>
      <li class="nav-item"><a class="nav-link text-white" href="export.php">⬇️ Export CSV</a></li>
      <li class="nav-item"><a class="nav-link text-white" href="logout.php">🚪 Logout</a></li>
    </ul>
  </div>
</div>
<div class="content">
  <?= isset($content) ? $content : "<p>No content</p>" ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/clipboard@2.0.11/dist/clipboard.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  if (typeof ClipboardJS !== 'undefined') {
    const clipboard = new ClipboardJS('.copy-btn');
    clipboard.on('success', function(e) {
      let btn = e.trigger;
      const confirmEl = btn.closest('td')?.querySelector('.copy-confirm') || btn.nextElementSibling;
      const original = btn.innerHTML;
      if (confirmEl) {
        confirmEl.style.display = 'inline-block';
        setTimeout(()=> confirmEl.style.display = 'none', 1400);
      } else {
        btn.innerHTML = 'Copied!';
        setTimeout(()=> btn.innerHTML = original, 1400);
      }
      e.clearSelection();
    });
  }
});
</script>
</body>
</html>
