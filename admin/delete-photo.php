<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

require_login();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$tripId = isset($_GET['trip_id']) ? (int)$_GET['trip_id'] : 0;

if ($id > 0) {
    $stmtSelect = $pdo->prepare("SELECT * FROM photos WHERE id = ?");
    $stmtSelect->execute([$id]);
    $photo = $stmtSelect->fetch();

    if ($photo) {
        // Delete image files from disk if exists
        foreach (['image_path', 'medium_path', 'thumbnail_path'] as $col) {
            if (!empty($photo[$col])) {
                $fullPath = __DIR__ . '/../' . ltrim($photo[$col], '/');
                if (file_exists($fullPath)) {
                    @unlink($fullPath);
                }
            }
        }

        $stmtDel = $pdo->prepare("DELETE FROM photos WHERE id = ?");
        $stmtDel->execute([$id]);
        set_flash('success', 'Photo deleted successfully.');
    }
}

$redirect = $tripId > 0 ? "/travel-memories/admin/photos.php?trip_id=" . $tripId : "/travel-memories/admin/photos.php";
header("Location: " . $redirect);
exit;
