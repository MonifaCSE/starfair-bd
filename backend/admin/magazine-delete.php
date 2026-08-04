<?php
/**
 * STAR FAIR - Admin Delete Magazine Edition API
 * Deletes magazine metadata and associated PDF and cover files.
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
    echo json_encode(['success' => false, 'message' => 'Missing or invalid magazine ID.']);
    exit;
}

try {
    // 1. Fetch magazine files to delete from disk
    $stmt = $pdo->prepare("SELECT pdf_path, cover_path FROM `magazines` WHERE `id` = ? LIMIT 1");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    
    if ($row) {
        $pdf_path = $row['pdf_path'];
        $cover_path = $row['cover_path'];
        
        $real_base = realpath(MAGAZINE_UPLOAD_DIR);
        
        // Securely delete PDF file
        if (!empty($pdf_path)) {
            $pdf_abs = MAGAZINE_UPLOAD_DIR . '/' . $pdf_path;
            $real_pdf = realpath($pdf_abs);
            if ($real_pdf !== false && str_starts_with($real_pdf, $real_base) && is_file($real_pdf)) {
                @unlink($real_pdf);
            }
        }
        
        // Securely delete cover image file
        if (!empty($cover_path)) {
            $cover_abs = MAGAZINE_UPLOAD_DIR . '/' . $cover_path;
            $real_cover = realpath($cover_abs);
            if ($real_cover !== false && str_starts_with($real_cover, $real_base) && is_file($real_cover)) {
                @unlink($real_cover);
            }
        }
    }
    
    // 2. Delete database entry
    $stmt = $pdo->prepare("DELETE FROM `magazines` WHERE `id` = ?");
    $stmt->execute([$id]);
    
    echo json_encode(['success' => true, 'message' => 'Magazine edition and associated files permanently deleted.']);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to delete magazine: ' . $e->getMessage()
    ]);
}
