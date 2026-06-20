<?php
require_once __DIR__ . '/auth.php';
$user = current_user();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle ?? 'SafeParcel') ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">SafeParcel</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
        <li class="nav-item"><a class="nav-link" href="track.php">Track Parcel</a></li>
        <?php if ($user): ?>
          <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
          <li class="nav-item"><a class="nav-link" href="parcels.php">Parcels</a></li>
          <li class="nav-item"><span class="nav-link small text-white-50"><?= e($user['name']) ?> (<?= e($user['role']) ?>)</span></li>
          <li class="nav-item"><a class="btn btn-light btn-sm" href="logout.php">Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="btn btn-light btn-sm" href="login.php">Admin / User Login</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<main>
