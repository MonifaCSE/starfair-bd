<?php
/**
 * STAR FAIR - Admin API to delete a news article
 */

require_once __DIR__ . '/../../backend/config.php';

header('Content-Type: application/json; charset=utf-8');

// Strict session check
if (empty($_SESSION['admin_logged_in'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Access Denied. Authentication required.']);
    exit;
}

$id = filter_input(INPUT_POST, 'newsId', FILTER_VALIDATE_INT);

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Invalid news article ID.']);
    exit;
}

require_once __DIR__ . '/../database.php';

try {
    $stmt = $pdo->prepare("SELECT * FROM `news` WHERE `id` = ?");
    $stmt->execute([$id]);
    $article = $stmt->fetch();

    if (!$article) {
        echo json_encode(['success' => false, 'message' => 'News article not found.']);
        exit;
    }

    // Delete image file from disk
    if (!empty($article['image_path'])) {
        $file_path = ROOT_PATH . '/' . $article['image_path'];
        if (is_file($file_path) && str_contains($article['image_path'], 'uploads/')) {
            @unlink($file_path);
        }
    }

    // Delete database row
    $stmt = $pdo->prepare("DELETE FROM `news` WHERE `id` = ?");
    $stmt->execute([$id]);

    echo json_encode(['success' => true, 'message' => 'News article deleted successfully!']);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error occurred: ' . $e->getMessage()]);
}
