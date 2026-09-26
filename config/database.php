<?php
/**
 * Database Configuration & Connection Setup
 * Supports Localhost XAMPP MySQL, Remote MySQL Env Vars, & SQLite Fallback
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

    $isVercel = isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || strpos($_SERVER['HTTP_HOST'] ?? '', 'vercel.app') !== false;

    // 1. If remote MySQL env var DB_HOST is explicitly set on Vercel
    if ($isVercel && getenv('DB_HOST')) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            return $pdo;
        } catch (PDOException $e) {
            // Fallthrough to SQLite fallback
        }
    }

    // 2. Localhost XAMPP MySQL connection
    if (!$isVercel) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo->exec("USE `" . DB_NAME . "`");

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
            // Fallthrough to SQLite
        }
    }

    // 3. Fallback to embedded SQLite database (Works out-of-the-box on Vercel!)
    $sqliteFile = __DIR__ . '/../database/travel_memories.sqlite';
    if (file_exists($sqliteFile)) {
        try {
            $pdo = new PDO('sqlite:' . $sqliteFile);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            // Register custom MySQL-compatible functions in SQLite
            if (method_exists($pdo, 'sqliteCreateFunction')) {
                @$pdo->sqliteCreateFunction('YEAR', function($dateStr) {
                    if (empty($dateStr)) return null;
                    return date('Y', strtotime($dateStr));
                }, 1);

                @$pdo->sqliteCreateFunction('CURDATE', function() {
                    return date('Y-m-d');
                }, 0);

                @$pdo->sqliteCreateFunction('NOW', function() {
                    return date('Y-m-d H:i:s');
                }, 0);
            }

            return $pdo;
        } catch (PDOException $e) {
            // Ignore
        }
    }

    die("<div style='font-family: sans-serif; padding: 30px; background: #fff5f5; color: #900; border: 1px solid #fcc; margin: 50px auto; max-width: 600px; border-radius: 8px;'>
        <h2>Database Connection Error</h2>
        <p>Could not connect to MySQL database or SQLite fallback.</p>
    </div>");
}

// Global PDO instance
$pdo = getDBConnection();
