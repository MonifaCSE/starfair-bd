<?php
/**
 * STAR FAIR - Admin API to reset team member profile image back to default
 */

require_once __DIR__ . '/../../backend/config.php';

header('Content-Type: application/json; charset=utf-8');

// Strict session check
if (empty($_SESSION['admin_logged_in'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Access Denied. Authentication required.']);
    exit;
}

$id = intval($_POST['id'] ?? 0);

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Profile ID is required.']);
    exit;
}

require_once __DIR__ . '/../database.php';

try {
    $stmt = $pdo->prepare("SELECT `id`, `image_path` FROM `mentors` WHERE `id` = ?");
    $stmt->execute([$id]);
    $member = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$member) {
        echo json_encode(['success' => false, 'message' => 'Profile not found.']);
        exit;
    }

    $image_path = $member['image_path'];

    // Delete custom uploaded file if it exists
    if ($image_path && str_contains($image_path, 'uploads/')) {
        $file_path = ROOT_PATH . '/' . $image_path;
        if (is_file($file_path)) {
            @unlink($file_path);
        }
    }

    // Set image path back to NULL (restores default)
    $stmt = $pdo->prepare("UPDATE `mentors` SET `image_path` = NULL, `updated_at` = CURRENT_TIMESTAMP WHERE `id` = ?");
    $stmt->execute([$id]);

    echo json_encode(['success' => true, 'message' => 'Profile image restored to default successfully.']);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error occurred: ' . $e->getMessage()]);
}
