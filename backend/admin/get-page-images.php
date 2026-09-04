<?php
/**
 * STAR FAIR - Admin API to get custom images for a page
 */

require_once __DIR__ . '/../../backend/config.php';

header('Content-Type: application/json; charset=utf-8');

// Strict session check
if (empty($_SESSION['admin_logged_in'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Access Denied. Authentication required.']);
    exit;
}

$page = $_GET['page'] ?? '';

// Security: Validate page strictly against allowed pages
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

if (!in_array($page, $allowed_pages, true)) {
    echo json_encode(['success' => false, 'message' => 'Invalid page parameter.']);
    exit;
}

require_once __DIR__ . '/../database.php';

try {
    $stmt = $pdo->prepare("SELECT `image_key`, `image_path` FROM `page_images` WHERE `page` = ?");
    $stmt->execute([$page]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $images = [];
    foreach ($results as $row) {
        $images[$row['image_key']] = $row['image_path'];
    }

    echo json_encode(['success' => true, 'images' => $images]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error occurred: ' . $e->getMessage()]);
}
