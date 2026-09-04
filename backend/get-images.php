<?php
/**
 * STAR FAIR - Public API to get dynamic page images mapping
 * Accept GET ?page=page-name.html
 */

header('Content-Type: application/json; charset=utf-8');

$page = $_GET['page'] ?? '';

// Security: Validate allowed page inputs strictly
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

require_once __DIR__ . '/database.php';

try {
    // Fetch custom dynamic images for this page AND global settings (like the logo)
    $stmt = $pdo->prepare("SELECT `image_key`, `image_path`, UNIX_TIMESTAMP(`updated_at`) AS `t` FROM `page_images` WHERE `page` = ? OR `page` = 'global'");
    $stmt->execute([$page]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $images = [];
    foreach ($results as $row) {
        $images[$row['image_key']] = [
            'path' => $row['image_path'],
            't' => $row['t']
        ];
    }

    echo json_encode(['success' => true, 'images' => $images]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error occurred.']);
}
