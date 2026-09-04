<?php
/**
 * STAR FAIR - Admin API to delete an official collaboration partner
 */

require_once __DIR__ . '/../../backend/config.php';

header('Content-Type: application/json; charset=utf-8');

// Strict session check
if (empty($_SESSION['admin_logged_in'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Access Denied. Authentication required.']);
    exit;
}

$id = filter_input(INPUT_POST, 'partnerId', FILTER_VALIDATE_INT);

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Invalid partner ID.']);
    exit;
}

require_once __DIR__ . '/../database.php';

try {
    $stmt = $pdo->prepare("SELECT * FROM `partners` WHERE `id` = ?");
    $stmt->execute([$id]);
    $partner = $stmt->fetch();

    if (!$partner) {
        echo json_encode(['success' => false, 'message' => 'Partner not found.']);
        exit;
    }

    // Delete image file from disk
    if (!empty($partner['image_path'])) {
        $file_path = ROOT_PATH . '/' . $partner['image_path'];
        if (is_file($file_path) && str_contains($partner['image_path'], 'uploads/')) {
            @unlink($file_path);
        }
    }

    // Delete database row
    $stmt = $pdo->prepare("DELETE FROM `partners` WHERE `id` = ?");
    $stmt->execute([$id]);

    echo json_encode(['success' => true, 'message' => 'Partner deleted successfully!']);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error occurred: ' . $e->getMessage()]);
}
