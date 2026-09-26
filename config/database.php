<?php
/**
 * Database Configuration & Connection Setup
 * Supports Localhost XAMPP & Vercel Environment Variables
 * Travel Memories Website
 */

// Dynamic Base URL detection for Vercel vs Localhost
if (!defined('BASE_URL')) {
    $host = $_SERVER['HTTP_HOST'] ?? '';
    if (strpos($host, 'vercel.app') !== false || isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL'])) {
        define('BASE_URL', '');
    } else {
        define('BASE_URL', '/travel-memories');
    }
}

// Database Credentials (from Environment Variables or XAMPP defaults)
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_PORT', getenv('DB_PORT') ?: '3306');
define('DB_NAME', getenv('DB_NAME') ?: 'travel_memories');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');
define('DB_CHARSET', 'utf8mb4');

function getDBConnection() {
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    try {
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        // On Vercel, if DB_NAME is specified, connect directly to that DB
        if (getenv('DB_NAME')) {
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            return $pdo;
        }

        // Localhost auto database & schema creation
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `" . DB_NAME . "`");

        // Check if tables exist
        $checkTable = $pdo->query("SHOW TABLES LIKE 'trips'");
        if ($checkTable->rowCount() === 0) {
            $sqlFile = __DIR__ . '/../database/travel_memories.sql';
            if (file_exists($sqlFile)) {
                $sqlContent = file_get_contents($sqlFile);
                $pdo->exec($sqlContent);
            }
        }

        return $pdo;
    } catch (PDOException $e) {
        // Friendly Vercel setup advice if database connection fails
        $isVercel = isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || strpos($_SERVER['HTTP_HOST'] ?? '', 'vercel.app') !== false;
        
        if ($isVercel) {
            die("<div style='font-family: system-ui, sans-serif; padding: 40px; background: #000; color: #fff; text-align: center; min-height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center;'>
                <h1 style='color: #d4af37;'>Vercel Deployment Connected!</h1>
                <p style='max-width: 600px; color: #ccc; line-height: 1.6;'>PHP Runtime is running properly on Vercel. To connect your MySQL database on Vercel, please set your Environment Variables (DB_HOST, DB_NAME, DB_USER, DB_PASS) in Vercel Project Settings.</p>
                <div style='background: #111; padding: 15px 25px; border-radius: 8px; border: 1px solid #333; margin-top: 20px; font-family: monospace; color: #00ff66;'>
                    DB_HOST, DB_NAME, DB_USER, DB_PASS
                </div>
            </div>");
        }

        die("<div style='font-family: sans-serif; padding: 30px; background: #fff5f5; color: #900; border: 1px solid #fcc; margin: 50px auto; max-width: 600px; border-radius: 8px;'>
            <h2>Database Connection Error</h2>
            <p>Could not connect to the MySQL database. Please verify your XAMPP MySQL service is running.</p>
            <p><small>Details: " . htmlspecialchars($e->getMessage()) . "</small></p>
        </div>");
    }
}

// Global PDO instance
$pdo = getDBConnection();
