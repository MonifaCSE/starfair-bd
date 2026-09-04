<?php
/**
 * STAR FAIR - PDO MySQL Connection Helper
 * Hostinger & Local XAMPP/MAMP Compatible
 */

require_once __DIR__ . '/config.php';

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    $pdo = new PDO($dsn, DB_USER, DB_PASSWORD, $options);
    
    // Auto-create page_images table if not present
    $pdo->exec("CREATE TABLE IF NOT EXISTS `page_images` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `section` VARCHAR(50) NOT NULL,
      `page` VARCHAR(100) NOT NULL,
      `image_key` VARCHAR(100) NOT NULL,
      `image_path` VARCHAR(255) NOT NULL,
      `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      UNIQUE KEY `page_image_key` (`section`, `page`, `image_key`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
} catch (PDOException $e) {
    // If the request is an API request, return JSON. Otherwise, display a clean error.
    $isJson = isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json');
    if ($isJson) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Database connection failed. Please contact administrator.'
        ]);
    } else {
        http_response_code(500);
        die("Database connection failed. Please ensure the database is running and configuration is correct.");
    }
    exit;
}
