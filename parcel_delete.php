<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';
require_login();

$id = (int) ($_GET['id'] ?? 0);
if ($id > 0) {
    $stmt = $pdo->prepare('DELETE FROM parcels WHERE id = ?');
    $stmt->execute([$id]);
}

header('Location: parcels.php');
exit;
