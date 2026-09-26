<?php
/**
 * AJAX Multiple Photo Uploader Endpoint
 * Travel Memories Admin
 */

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

if (!is_logged_in()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$tripId = isset($_POST['trip_id']) ? (int)$_POST['trip_id'] : 0;
if ($tripId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid Trip ID']);
    exit;
}

if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No valid photo file uploaded']);
    exit;
}

$processed = process_image_upload($_FILES['photo'], 'uploads/trips');

if (!$processed) {
    echo json_encode(['success' => false, 'message' => 'Invalid image format or upload failed']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO photos 
        (trip_id, image_path, medium_path, thumbnail_path, caption, taken_date, display_order) 
        VALUES (?, ?, ?, ?, ?, CURDATE(), 0)");
    
    $caption = pathinfo($_FILES['photo']['name'], PATHINFO_FILENAME);
    $caption = str_replace(['_', '-'], ' ', $caption);

    $stmt->execute([
        $tripId,
        $processed['original'],
        $processed['medium'],
        $processed['thumbnail'],
        $caption
    ]);

    $photoId = $pdo->lastInsertId();

    echo json_encode([
        'success'   => true,
        'photo_id'  => $photoId,
        'thumbnail' => '/travel-memories/' . $processed['thumbnail'],
        'caption'   => $caption
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
