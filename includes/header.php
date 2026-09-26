<?php
if (!defined('DB_HOST')) {
    require_once __DIR__ . '/../config/database.php';
}
require_once __DIR__ . '/functions.php';

$pageTitle = $pageTitle ?? get_site_setting('site_title', 'MY TRAVEL MEMORIES');
$pageDesc  = $pageDesc ?? get_site_setting('site_subtitle', 'Places I\'ve Been, Moments I\'ve Captured.');
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle); ?> | Travel Journal</title>
    <meta name="description" content="<?= e($pageDesc); ?>">
    
    <!-- Open Graph SEO -->
    <meta property="og:title" content="<?= e($pageTitle); ?>">
    <meta property="og:description" content="<?= e($pageDesc); ?>">
    <meta property="og:type" content="website">

    <!-- CSS Assets -->
    <link rel="stylesheet" href="/travel-memories/assets/css/style.css">
</head>
<body>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-custom sticky-top">
    <div class="container">
        <a class="navbar-brand" href="/travel-memories/index.php">
            MY <span>JOURNEY</span>
        </a>
        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <i class="fas fa-bars fs-4 text-dark"></i>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?= ($currentPage == 'index.php') ? 'active' : ''; ?>" href="/travel-memories/index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($currentPage == 'trips.php' || $currentPage == 'trip.php') ? 'active' : ''; ?>" href="/travel-memories/trips.php">Trips</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($currentPage == 'gallery.php') ? 'active' : ''; ?>" href="/travel-memories/gallery.php">Gallery</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($currentPage == 'timeline.php') ? 'active' : ''; ?>" href="/travel-memories/timeline.php">Timeline</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($currentPage == 'locations.php') ? 'active' : ''; ?>" href="/travel-memories/locations.php">Locations</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($currentPage == 'years.php') ? 'active' : ''; ?>" href="/travel-memories/years.php">Years</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($currentPage == 'about.php') ? 'active' : ''; ?>" href="/travel-memories/about.php">About</a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-3">
                <form action="/travel-memories/search.php" method="GET" class="d-flex position-relative">
                    <input class="form-control rounded-pill pe-5 ps-3 py-1 fs-6 bg-light border-0" type="search" name="q" placeholder="Search trips..." required>
                    <button class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-secondary me-2 p-0" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
                
                <?php if (is_logged_in()): ?>
                    <a href="/travel-memories/admin/index.php" class="btn btn-sm btn-outline-custom rounded-pill">
                        <i class="fas fa-user-shield me-1"></i> Admin
                    </a>
                <?php else: ?>
                    <a href="/travel-memories/admin/login.php" class="text-secondary fs-5" title="Admin Login">
                        <i class="fas fa-lock"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
