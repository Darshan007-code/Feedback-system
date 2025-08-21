<?php
/**
 * Database Configuration File
 * 
 * This file contains the database connection settings for the feedback system.
 * Update these values according to your local database setup.
 */

// Database configuration
define('DB_HOST', 'localhost');     // Database host (usually localhost)
define('DB_NAME', 'feedback_system'); // Database name
define('DB_USER', 'root');          // Database username (default for XAMPP/WAMP)
define('DB_PASS', '');              // Database password (empty for default XAMPP/WAMP setup)
define('DB_CHARSET', 'utf8mb4');    // Character set

// Error reporting (set to false in production)
define('DEBUG_MODE', true);

// Set error reporting based on debug mode
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

/**
 * Create database connection
 * 
 * @return PDO|false Returns PDO connection object or false on failure
 */
function getDatabaseConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
        
    } catch (PDOException $e) {
        if (DEBUG_MODE) {
            error_log("Database connection failed: " . $e->getMessage());
        }
        return false;
    }
}

/**
 * Test database connection
 * 
 * @return bool Returns true if connection successful, false otherwise
 */
function testDatabaseConnection() {
    $pdo = getDatabaseConnection();
    if ($pdo) {
        try {
            $pdo->query('SELECT 1');
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
    return false;
}

/**
 * Create database and table if they don't exist
 * 
 * @return bool Returns true if successful, false otherwise
 */
function initializeDatabase() {
    try {
        // Connect without specifying database name first
        $dsn = "mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Create database if it doesn't exist
        $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        
        // Select the database
        $pdo->exec("USE " . DB_NAME);
        
        // Create feedback table if it doesn't exist
        $sql = "CREATE TABLE IF NOT EXISTS feedback (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(255) NOT NULL,
            rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
            comments TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $pdo->exec($sql);
        return true;
        
    } catch (PDOException $e) {
        if (DEBUG_MODE) {
            error_log("Database initialization failed: " . $e->getMessage());
        }
        return false;
    }
}

// Auto-initialize database if this file is included
if (basename($_SERVER['PHP_SELF']) !== 'schema.sql') {
    initializeDatabase();
}
?>
