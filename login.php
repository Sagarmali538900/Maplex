<?php
session_start();

// If already logged in → go to manage.php
if (isset($_SESSION['admin'])) {
    header("Location: manage.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $host = 'localhost';
    $dbname = 'repo_db';
    $user = 'root';
    $pass = '';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("SELECT * FROM users WHERE username=? AND password=?");
        $stmt->execute([$_POST['username'], $_POST['password']]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin) {
            $_SESSION['admin'] = $admin['username'];
            header("Location: manage.php");
            exit;
        } else {
            $error = "Invalid username or password.";
        }
    } catch (PDOException $e) {
        $error = "Database connection failed: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Maplex Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #1e3c72, #2a5298);
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .login-card {
      background: #fff;
      border-radius: 15px;
      padding: 2rem;
      box-shadow: 0 8px 25px rgba(0,0,0,0.2);
      width: 380px;
    }
    .login-card h3 {
      text-align: center;
      margin-bottom: 1.5rem;
      font-weight: 600;
    }
    .form-control {
      border-radius: 10px;
    }
    .btn-login {
      width: 100%;
      border-radius: 10px;
      padding: 10px;
      font-weight: 500;
    }
    .error-msg {
      color: red;
      font-size: 0.9rem;
      text-align: center;
      margin-bottom: 1rem;
    }
  </style>
</head>
<body>

  <div class="login-card">
      <h3>Maplex Login</h3>
      <?php if ($error): ?>
          <div class="error-msg"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      <form method="post">
          <div class="mb-3">
              <label for="username" class="form-label">Username</label>
              <input type="text" name="username" id="username" class="form-control" required>
          </div>
          <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <input type="password" name="password" id="password" class="form-control" required>
          </div>
          <button type="submit" class="btn btn-primary btn-login">Login</button>
      </form>
  </div>

</body>
</html>
