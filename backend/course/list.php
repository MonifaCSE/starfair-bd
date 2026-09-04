<?php
/**
 * STAR FAIR - Public Course List API Endpoint
 * Returns JSON list of all published courses ordered by display_order.
 */

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

require_once __DIR__ . '/../database.php';

try {
    $category = $_GET['category'] ?? '';

    if (!empty($category)) {
        $stmt = $pdo->prepare("SELECT `id`, `title`, `slug`, `category`, `category_badge`, `short_description`, `hero_image`, `duration`, `admission_fee`, `course_fee`, `eligibility`, `age_requirement`, `course_type`, `display_order` 
                               FROM `courses` 
                               WHERE `status` = 'published' AND `category` = ? 
                               ORDER BY `display_order` ASC, `id` ASC");
        $stmt->execute([$category]);
    } else {
        $stmt = $pdo->prepare("SELECT `id`, `title`, `slug`, `category`, `category_badge`, `short_description`, `hero_image`, `duration`, `admission_fee`, `course_fee`, `eligibility`, `age_requirement`, `course_type`, `display_order` 
                               FROM `courses` 
                               WHERE `status` = 'published' 
                               ORDER BY `display_order` ASC, `id` ASC");
        $stmt->execute();
    }

    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'count' => count($courses),
        'courses' => $courses
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Unable to fetch courses.'
    ]);
}
