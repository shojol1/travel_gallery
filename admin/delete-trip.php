<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

require_login();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $stmt = $pdo->prepare("DELETE FROM trips WHERE id = ?");
    $stmt->execute([$id]);
    set_flash('success', 'Trip and associated photos deleted successfully.');
}

header("Location: /travel-memories/admin/trips.php");
exit;
