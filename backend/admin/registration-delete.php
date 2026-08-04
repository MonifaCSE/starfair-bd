<?php
/**
 * STAR FAIR - Admin Delete Registration API
 * Deletes student database entry and their uploaded files.
 * Protected by admin session check.
 */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed.']);
    exit;
}

require_once __DIR__ . '/../database.php';

header('Content-Type: application/json; charset=utf-8');

// Strict session check
if (empty($_SESSION['admin_logged_in'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Access Denied. Authentication required.']);
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Missing or invalid registration ID.']);
    exit;
}

try {
    // 1. Retrieve the file paths first to locate the folder on disk
    $stmt = $pdo->prepare("SELECT `photo_path` FROM `registrations` WHERE `id` = ? LIMIT 1");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    
    if ($row) {
        $photo_path = $row['photo_path'];
        
        // Extract the unique directory name (e.g. from "subdir/photo.jpg")
        $parts = explode('/', $photo_path);
        if (count($parts) > 1) {
            $unique_id = $parts[0];
            $reg_folder = REGISTRATION_UPLOAD_DIR . '/' . $unique_id;
            
            // Delete folder and its contents securely if it exists
            $real_base = realpath(REGISTRATION_UPLOAD_DIR);
            $real_folder = realpath($reg_folder);
            
            if ($real_folder !== false && str_starts_with($real_folder, $real_base) && is_dir($real_folder)) {
                $files = glob($real_folder . '/*');
                foreach ($files as $file) {
                    if (is_file($file)) @unlink($file);
                }
                @rmdir($real_folder);
            }
        }
    }
    
    // 2. Delete database entry
    $stmt = $pdo->prepare("DELETE FROM `registrations` WHERE `id` = ?");
    $stmt->execute([$id]);
    
    echo json_encode(['success' => true, 'message' => 'Registration entry and all uploaded documents permanently deleted.']);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to delete record: ' . $e->getMessage()
    ]);
}
