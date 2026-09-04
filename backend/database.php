<?php
/**
 * STAR FAIR - PDO MySQL Connection Helper
 * Hostinger & Local XAMPP/MAMP Compatible
 */

require_once __DIR__ . '/config.php';

try {
    $host = DB_HOST;
    $dsn = "mysql:host=" . $host . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    
    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASSWORD, $options);
    } catch (PDOException $ex) {
        // Fallback for macOS XAMPP CLI socket differences
        $socketPath = '/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock';
        if (file_exists($socketPath)) {
            $dsn = "mysql:unix_socket=" . $socketPath . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        } else {
            $dsn = "mysql:host=127.0.0.1;dbname=" . DB_NAME . ";charset=utf8mb4";
        }
        $pdo = new PDO($dsn, DB_USER, DB_PASSWORD, $options);
    }
    
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

    // Auto-create courses tables if not present
    $pdo->exec("CREATE TABLE IF NOT EXISTS `courses` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `title` VARCHAR(255) NOT NULL,
      `slug` VARCHAR(100) NOT NULL UNIQUE,
      `category` VARCHAR(50) NOT NULL DEFAULT 'core_programme',
      `category_badge` VARCHAR(100) NOT NULL DEFAULT 'CORE PROGRAMME',
      `short_description` TEXT NOT NULL,
      `full_description` LONGTEXT NOT NULL,
      `hero_image` VARCHAR(255) NOT NULL,
      `duration` VARCHAR(100) NOT NULL,
      `admission_fee` VARCHAR(255) DEFAULT NULL,
      `course_fee` VARCHAR(255) DEFAULT NULL,
      `eligibility` TEXT DEFAULT NULL,
      `age_requirement` VARCHAR(100) DEFAULT NULL,
      `course_type` VARCHAR(100) DEFAULT NULL,
      `status` ENUM('published', 'draft') NOT NULL DEFAULT 'published',
      `display_order` INT NOT NULL DEFAULT 1,
      `meta_title` VARCHAR(255) DEFAULT NULL,
      `meta_description` TEXT DEFAULT NULL,
      `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    $pdo->exec("CREATE TABLE IF NOT EXISTS `course_modules` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `course_id` INT NOT NULL,
      `module_name` VARCHAR(255) NOT NULL,
      `display_order` INT NOT NULL DEFAULT 1,
      FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    $pdo->exec("CREATE TABLE IF NOT EXISTS `course_schedules` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `course_id` INT NOT NULL,
      `day_name` VARCHAR(100) NOT NULL,
      `time_text` VARCHAR(255) NOT NULL,
      `topic_text` VARCHAR(255) DEFAULT NULL,
      `display_order` INT NOT NULL DEFAULT 1,
      FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    $pdo->exec("CREATE TABLE IF NOT EXISTS `course_careers` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `course_id` INT NOT NULL,
      `career_title` VARCHAR(255) NOT NULL,
      `display_order` INT NOT NULL DEFAULT 1,
      FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    $pdo->exec("CREATE TABLE IF NOT EXISTS `course_galleries` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `course_id` INT NOT NULL,
      `image_path` VARCHAR(255) NOT NULL,
      `alt_text` VARCHAR(255) DEFAULT NULL,
      `display_order` INT NOT NULL DEFAULT 1,
      FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE
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
