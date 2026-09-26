<?php
/**
 * Database Configuration & Connection Setup
 * Travel Memories Website
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'travel_memories');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

function getDBConnection() {
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    try {
        // First attempt connection to MySQL server
        $dsn = "mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        
        // Ensure database exists
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `" . DB_NAME . "`");

        // Check if tables exist, if not run initialization SQL
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
        die("<div style='font-family: sans-serif; padding: 30px; background: #fff5f5; color: #900; border: 1px solid #fcc; margin: 50px auto; max-width: 600px; border-radius: 8px;'>
            <h2>Database Connection Error</h2>
            <p>Could not connect to the MySQL database. Please verify your XAMPP MySQL service is running.</p>
            <p><small>Details: " . htmlspecialchars($e->getMessage()) . "</small></p>
        </div>");
    }
}

// Global PDO instance
$pdo = getDBConnection();
