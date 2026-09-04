<?php
/**
 * STAR FAIR - Admin Image Reset API
 * Deletes custom uploaded image and removes its database mapping to restore default asset.
 */

require_once __DIR__ . '/../../backend/config.php';

header('Content-Type: application/json; charset=utf-8');

// Strict session check
if (empty($_SESSION['admin_logged_in'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Access Denied. Authentication required.']);
    exit;
}

$section = trim($_POST['section'] ?? '');
$page = trim($_POST['page'] ?? '');
$image_key = trim($_POST['image_key'] ?? '');

$allowed_sections = ['main', 'courses', 'events', 'gallery', 'global'];
$allowed_pages = [
    'index.html',
    'course.html',
    'registration.html',
    'magazine.html',
    'contact.html',
    'privacy.html',
    'award.html',
    'advisor-trainers.html',
    '404.html',
    'modeling.html',
    'acting.html',
    'hiphop-dance.html',
    'classical-dance.html',
    'photography.html',
    'pageant.html',
    'makeup.html',
    'fine-arts.html',
    'communication.html',
    'grooming.html',
    'poetry.html',
    'hosting.html',
    'digital-marketing.html',
    'kids-modelling.html',
    'fashion-show.html',
    'campaign-shoot.html',
    'photo-gallery.html',
    'kids-portfolio.html',
    'teenager-portfolio.html',
    'models-portfolio.html',
    'global'
];

if (!in_array($section, $allowed_sections, true) || !in_array($page, $allowed_pages, true)) {
    echo json_encode(['success' => false, 'message' => 'Invalid section or page selection.']);
    exit;
}

if (!preg_match('/^[a-zA-Z0-9_-]+$/', $image_key)) {
    echo json_encode(['success' => false, 'message' => 'Invalid image key format.']);
    exit;
}

require_once __DIR__ . '/../database.php';

try {
    // 1. Get the current image path from database
    $stmt = $pdo->prepare("SELECT `image_path` FROM `page_images` WHERE `section` = ? AND `page` = ? AND `image_key` = ?");
    $stmt->execute([$section, $page, $image_key]);
    $record = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($record) {
        // 2. Delete the custom uploaded file (only if it is in the uploads directory)
        $file_path = ROOT_PATH . '/' . $record['image_path'];
        if (is_file($file_path) && str_contains($record['image_path'], 'uploads/')) {
            @unlink($file_path);
        }

        // 3. Remove DB mapping
        $stmt = $pdo->prepare("DELETE FROM `page_images` WHERE `section` = ? AND `page` = ? AND `image_key` = ?");
        $stmt->execute([$section, $page, $image_key]);
    }

    echo json_encode(['success' => true, 'message' => 'Image reset to default successfully.']);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error occurred: ' . $e->getMessage()]);
}
