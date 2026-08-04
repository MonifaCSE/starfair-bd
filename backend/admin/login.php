<?php
/**
 * STAR FAIR - Admin Login API
 * Hostinger & Local XAMPP/MAMP Compatible
 * Returns JSON response.
 */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed.']);
    exit;
}

require_once __DIR__ . '/../database.php';

header('Content-Type: application/json; charset=utf-8');

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Please fill in both email and password fields.']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM `admins` WHERE `email` = ? LIMIT 1");
    $stmt->execute([$email]);
    $admin = $stmt->fetch();
    
    if ($admin && password_verify($password, $admin['password_hash'])) {
        // Login success - establish PHP session variables
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_email'] = $admin['email'];
        $_SESSION['admin_id'] = $admin['id'];
        
        // Regenerate session ID to prevent session fixation attacks
        session_regenerate_id(true);
        
        echo json_encode(['success' => true, 'message' => 'Logged in successfully.']);
    } else {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Invalid email address or password.']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error during authentication.']);
}
