<?php
/**
 * Helper & Utility Functions
 * Travel Memories Website
 */

if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
    session_start();
}

require_once __DIR__ . '/../config/database.php';

/**
 * Escape strings for safe HTML output
 */
function e(?string $string): string {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Generate clean URL slug from text
 */
function slugify(string $text): string {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $translit = @iconv('utf-8', 'us-ascii//TRANSLIT//IGNORE', $text);
    if ($translit !== false) {
        $text = $translit;
    }
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return empty($text) ? 'trip-' . time() . '-' . rand(100, 999) : $text;
}

/**
 * Get dynamic image URL (Serves directly from shojolworld.com live server)
 */
function get_image_url(?string $path, string $type = 'medium'): string {
    if (empty($path)) {
        return 'https://shojolworld.com/travel/uploads/1745249050_IMG_1740.jpg';
    }
    
    // If it's already a full HTTP URL
    if (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0) {
        return $path;
    }

    $filename = basename($path);

    // If filename has a timestamp or is stored in uploads/trips
    if (preg_match('/^\d{9,11}_/', $filename) || strpos($path, 'uploads/trips') !== false) {
        // If it's a custom uploaded image that exists locally (new admin upload)
        $cleanPath = ltrim($path, '/');
        $localFile = __DIR__ . '/../' . $cleanPath;
        if (file_exists($localFile) && (strpos($filename, 'img_') === 0)) {
            return '/travel-memories/' . $cleanPath;
        }

        // Direct live URL from shojolworld.com
        if ($type === 'thumb') {
            return 'https://shojolworld.com/travel/uploads/thumbs/' . rawurlencode($filename);
        }
        return 'https://shojolworld.com/travel/uploads/' . rawurlencode($filename);
    }

    return '/travel-memories/' . ltrim($path, '/');
}

/**
 * Get dynamic site settings from DB
 */
function get_site_setting(string $key, string $default = ''): string {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $val = $stmt->fetchColumn();
        return $val !== false ? $val : $default;
    } catch (Exception $e) {
        return $default;
    }
}

/**
 * Calculate dynamic stats from MySQL database
 */
function get_trip_stats(): array {
    global $pdo;
    try {
        $stmtTrips = $pdo->query("SELECT COUNT(*) FROM trips WHERE published = 1");
        $totalTrips = (int) $stmtTrips->fetchColumn();

        $stmtPlaces = $pdo->query("SELECT COUNT(DISTINCT location) FROM trips WHERE published = 1");
        $totalPlaces = (int) $stmtPlaces->fetchColumn();

        $stmtDistricts = $pdo->query("SELECT COUNT(DISTINCT district) FROM trips WHERE published = 1");
        $totalDistricts = (int) $stmtDistricts->fetchColumn();

        $stmtPhotos = $pdo->query("SELECT COUNT(*) FROM photos p JOIN trips t ON p.trip_id = t.id WHERE t.published = 1");
        $totalPhotos = (int) $stmtPhotos->fetchColumn();

        return [
            'trips'     => $totalTrips,
            'places'    => $totalPlaces,
            'districts' => $totalDistricts,
            'photos'    => $totalPhotos
        ];
    } catch (Exception $e) {
        return ['trips' => 0, 'places' => 0, 'districts' => 0, 'photos' => 0];
    }
}

/**
 * Format date cleanly
 */
function format_date(?string $dateStr, string $format = 'd M Y'): string {
    if (empty($dateStr)) return '';
    $timestamp = strtotime($dateStr);
    return $timestamp ? date($format, $timestamp) : '';
}

/**
 * Calculate trip duration in days
 */
function calculate_duration(string $start_date, ?string $end_date): string {
    if (empty($end_date)) return '1 Day';
    $start = new DateTime($start_date);
    $end = new DateTime($end_date);
    $diff = $start->diff($end)->days + 1;
    return $diff . ($diff > 1 ? ' Days' : ' Day');
}

/**
 * Image processing function using GD Library
 */
function process_image_upload(array $file, string $targetFolder = 'uploads/trips'): ?array {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
    $allowedExts  = ['jpg', 'jpeg', 'png', 'webp'];

    $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $finfo   = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($fileExt, $allowedExts) || !in_array($mimeType, $allowedMimes)) {
        return null;
    }

    $baseDir   = __DIR__ . '/../' . rtrim($targetFolder, '/');
    $origDir   = $baseDir . '/original/';
    $mediumDir = $baseDir . '/medium/';
    $thumbDir  = $baseDir . '/thumb/';

    foreach ([$origDir, $mediumDir, $thumbDir] as $d) {
        if (!is_dir($d)) {
            mkdir($d, 0755, true);
        }
    }

    $filename   = uniqid('img_', true) . '_' . bin2hex(random_bytes(4)) . '.' . $fileExt;
    $origPath   = $origDir . $filename;
    $mediumPath = $mediumDir . $filename;
    $thumbPath  = $thumbDir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $origPath)) {
        return null;
    }

    if (extension_loaded('gd')) {
        resize_image_gd($origPath, $mediumPath, 1400, 1400, false);
        resize_image_gd($origPath, $thumbPath, 500, 500, true);
    } else {
        copy($origPath, $mediumPath);
        copy($origPath, $thumbPath);
    }

    $relBase = rtrim($targetFolder, '/') . '/';
    return [
        'original'  => $relBase . 'original/' . $filename,
        'medium'    => $relBase . 'medium/' . $filename,
        'thumbnail' => $relBase . 'thumb/' . $filename,
    ];
}

/**
 * Resizes an image using GD with aspect ratio & optional cropping
 */
function resize_image_gd(string $sourcePath, string $destPath, int $maxWidth, int $maxHeight, bool $crop = false): bool {
    $info = getimagesize($sourcePath);
    if (!$info) return false;

    list($origW, $origH, $type) = $info;

    switch ($type) {
        case IMAGETYPE_JPEG:
            $srcImg = @imagecreatefromjpeg($sourcePath);
            break;
        case IMAGETYPE_PNG:
            $srcImg = @imagecreatefrompng($sourcePath);
            break;
        case IMAGETYPE_WEBP:
            $srcImg = @imagecreatefromwebp($sourcePath);
            break;
        default:
            return false;
    }

    if (!$srcImg) return false;

    if ($crop) {
        $srcRatio = $origW / $origH;
        $targetRatio = $maxWidth / $maxHeight;

        if ($srcRatio > $targetRatio) {
            $cropH = $origH;
            $cropW = (int) ($origH * $targetRatio);
            $cropX = (int) (($origW - $cropW) / 2);
            $cropY = 0;
        } else {
            $cropW = $origW;
            $cropH = (int) ($origW / $targetRatio);
            $cropX = 0;
            $cropY = (int) (($origH - $cropH) / 2);
        }

        $dstImg = imagecreatetruecolor($maxWidth, $maxHeight);
        imagealphablending($dstImg, false);
        imagesavealpha($dstImg, true);
        imagecopyresampled($dstImg, $srcImg, 0, 0, $cropX, $cropY, $maxWidth, $maxHeight, $cropW, $cropH);
    } else {
        $ratio = min($maxWidth / $origW, $maxHeight / $origH);
        if ($ratio >= 1.0) {
            $newW = $origW;
            $newH = $origH;
        } else {
            $newW = (int) ($origW * $ratio);
            $newH = (int) ($origH * $ratio);
        }

        $dstImg = imagecreatetruecolor($newW, $newH);
        imagealphablending($dstImg, false);
        imagesavealpha($dstImg, true);
        imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $newW, $newH, $origW, $origH);
    }

    $saved = false;
    switch ($type) {
        case IMAGETYPE_JPEG:
            $saved = imagejpeg($dstImg, $destPath, 88);
            break;
        case IMAGETYPE_PNG:
            $saved = imagepng($dstImg, $destPath, 6);
            break;
        case IMAGETYPE_WEBP:
            $saved = imagewebp($dstImg, $destPath, 85);
            break;
    }

    imagedestroy($srcImg);
    imagedestroy($dstImg);
    return $saved;
}

/**
 * Authentication Helpers
 */
function is_logged_in(): bool {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function require_login(): void {
    if (!is_logged_in()) {
        header('Location: /travel-memories/admin/login.php');
        exit;
    }
}

/**
 * Flash messaging system
 */
function set_flash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
