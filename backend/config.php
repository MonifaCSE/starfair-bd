<?php
/**
 * STAR FAIR - Environment & Database Configuration File
 * Hostinger & Local XAMPP/MAMP Compatible
 */

// --- 1. DATABASE CREDENTIALS ---
// Change these placeholders when deploying to Hostinger
define('DB_HOST', 'localhost');
define('DB_NAME', 'starfair_db');
define('DB_USER', 'root');
define('DB_PASSWORD', '');

// --- 2. SECURITY CONFIGURATION ---
// PHP Session Cookie Protection
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    
    // Set cookie secure flag if HTTPS is enabled
    $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
    ini_set('session.cookie_secure', $isSecure ? 1 : 0);
    
    // Set Session Cookie Lifetime (30 minutes)
    session_set_cookie_params([
        'lifetime' => 1800,
        'path' => '/',
        'domain' => '',
        'secure' => $isSecure,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    
    session_start();
}

// --- 3. FILESYSTEM PATHS & STORAGE ---
define('ROOT_PATH', dirname(__DIR__));
define('UPLOAD_DIR', ROOT_PATH . '/uploads');
define('REGISTRATION_UPLOAD_DIR', UPLOAD_DIR . '/registrations');
define('MAGAZINE_UPLOAD_DIR', UPLOAD_DIR . '/magazines');

// --- 4. UPLOAD SECURITY PARAMETERS ---
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5 MB Max File Size

// Allowed MIME types and extensions for student documents (Photo, NID, Portfolio)
$ALLOWED_DOC_TYPES = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/webp' => 'webp',
    'application/pdf' => 'pdf'
];

// Allowed MIME types and extensions for Magazine covers
$ALLOWED_COVER_TYPES = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/webp' => 'webp'
];

// Allowed MIME types and extensions for Magazine PDFs
$ALLOWED_PDF_TYPES = [
    'application/pdf' => 'pdf'
];
