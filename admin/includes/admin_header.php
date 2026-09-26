<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

require_login();
$adminUser = $_SESSION['user_name'] ?? 'Admin';
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Travel Memories</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --sidebar-width: 250px;
            --admin-bg: #f4f6f9;
        }
        body {
            background-color: var(--admin-bg);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        .admin-sidebar {
            width: var(--sidebar-width);
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            background: #1e2229;
            color: #adb5bd;
            z-index: 1000;
            padding-top: 20px;
        }
        .admin-sidebar .nav-link {
            color: #adb5bd;
            padding: 12px 25px;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s;
        }
        .admin-sidebar .nav-link:hover,
        .admin-sidebar .nav-link.active {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.08);
            border-left: 4px solid #2d5a4c;
        }
        .admin-main {
            margin-left: var(--sidebar-width);
            padding: 30px;
        }
        @media (max-width: 991px) {
            .admin-sidebar { position: static; width: 100%; height: auto; }
            .admin-main { margin-left: 0; }
        }
    </style>
</head>
<body>

<aside class="admin-sidebar">
    <div class="px-4 mb-4">
        <a href="/travel-memories/admin/index.php" class="text-white text-decoration-none h5 fw-bold font-serif d-block">
            <i class="fas fa-compass text-warning me-2"></i> Travel Admin
        </a>
        <div class="small text-muted mt-1">Logged in as <?= e($adminUser); ?></div>
    </div>

    <ul class="nav flex-column">
        <li class="nav-item">
            <a href="/travel-memories/admin/index.php" class="nav-link <?= ($currentPage == 'index.php') ? 'active' : ''; ?>">
                <i class="fas fa-chart-pie"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a href="/travel-memories/admin/trips.php" class="nav-link <?= ($currentPage == 'trips.php' || $currentPage == 'add-trip.php' || $currentPage == 'edit-trip.php') ? 'active' : ''; ?>">
                <i class="fas fa-route"></i> Manage Trips
            </a>
        </li>
        <li class="nav-item">
            <a href="/travel-memories/admin/photos.php" class="nav-link <?= ($currentPage == 'photos.php') ? 'active' : ''; ?>">
                <i class="fas fa-images"></i> Manage Photos
            </a>
        </li>
        <li class="nav-item">
            <a href="/travel-memories/admin/settings.php" class="nav-link <?= ($currentPage == 'settings.php') ? 'active' : ''; ?>">
                <i class="fas fa-cog"></i> Settings
            </a>
        </li>
        <li class="nav-item mt-4 border-top border-secondary pt-3">
            <a href="/travel-memories/index.php" class="nav-link text-warning" target="_blank">
                <i class="fas fa-external-link-alt"></i> View Website
            </a>
        </li>
        <li class="nav-item">
            <a href="/travel-memories/admin/logout.php" class="nav-link text-danger">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </li>
    </ul>
</aside>

<main class="admin-main">
    <?php $flash = get_flash(); if ($flash): ?>
        <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
            <?= e($flash['message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
