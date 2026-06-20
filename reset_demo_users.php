<?php
require_once __DIR__ . '/config/db.php';

$adminPassword = password_hash('password', PASSWORD_DEFAULT);
$userPassword = password_hash('password', PASSWORD_DEFAULT);

$stmt = $pdo->prepare(
    "INSERT INTO users (name, email, mobile, role, password)
     VALUES (?, ?, ?, ?, ?)
     ON DUPLICATE KEY UPDATE
       name = VALUES(name),
       mobile = VALUES(mobile),
       role = VALUES(role),
       password = VALUES(password)"
);

$stmt->execute(['SafeParcel Admin', 'admin@safeparcel.test', '01700000001', 'admin', $adminPassword]);
$stmt->execute(['SafeParcel User', 'user@safeparcel.test', '01700000002', 'user', $userPassword]);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Demo Users Reset - SafeParcel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <main class="container py-5">
    <div class="card border-0 shadow-sm mx-auto" style="max-width: 620px;">
      <div class="card-body p-4 p-md-5">
        <h1 class="h3 fw-bold">Demo users reset successfully</h1>
        <p class="text-muted">You can now log in with these accounts:</p>
        <ul>
          <li>Admin: <strong>admin@safeparcel.test</strong> / <strong>password</strong></li>
          <li>User: <strong>user@safeparcel.test</strong> / <strong>password</strong></li>
        </ul>
        <a class="btn btn-primary" href="login.php">Go to Login</a>
      </div>
    </div>
  </main>
</body>
</html>
