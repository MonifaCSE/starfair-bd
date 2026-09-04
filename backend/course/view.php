<?php
/**
 * STAR FAIR - Public Course Detail API Endpoint
 * Accepts ?slug=... or ?id=... and returns complete published course details.
 */

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

require_once __DIR__ . '/../database.php';

try {
    $slug = trim($_GET['slug'] ?? '');
    $id = intval($_GET['id'] ?? 0);
    $preview = !empty($_GET['preview']) && !empty($_SESSION['admin_logged_in']);

    if (empty($slug) && $id <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Course identifier (slug or id) required.']);
        exit;
    }

    if (!empty($slug)) {
        if ($preview) {
            $stmt = $pdo->prepare("SELECT * FROM `courses` WHERE `slug` = ?");
            $stmt->execute([$slug]);
        } else {
            $stmt = $pdo->prepare("SELECT * FROM `courses` WHERE `slug` = ? AND `status` = 'published'");
            $stmt->execute([$slug]);
        }
    } else {
        if ($preview) {
            $stmt = $pdo->prepare("SELECT * FROM `courses` WHERE `id` = ?");
            $stmt->execute([$id]);
        } else {
            $stmt = $pdo->prepare("SELECT * FROM `courses` WHERE `id` = ? AND `status` = 'published'");
            $stmt->execute([$id]);
        }
    }

    $course = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$course) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Course not found or not published.']);
        exit;
    }

    $courseId = $course['id'];

    // Modules
    $stmtMod = $pdo->prepare("SELECT `id`, `module_name`, `display_order` FROM `course_modules` WHERE `course_id` = ? ORDER BY `display_order` ASC, `id` ASC");
    $stmtMod->execute([$courseId]);
    $course['modules'] = $stmtMod->fetchAll(PDO::FETCH_ASSOC);

    // Schedules
    $stmtSch = $pdo->prepare("SELECT `id`, `day_name`, `time_text`, `topic_text`, `display_order` FROM `course_schedules` WHERE `course_id` = ? ORDER BY `display_order` ASC, `id` ASC");
    $stmtSch->execute([$courseId]);
    $course['schedules'] = $stmtSch->fetchAll(PDO::FETCH_ASSOC);

    // Careers
    $stmtCar = $pdo->prepare("SELECT `id`, `career_title`, `display_order` FROM `course_careers` WHERE `course_id` = ? ORDER BY `display_order` ASC, `id` ASC");
    $stmtCar->execute([$courseId]);
    $course['careers'] = $stmtCar->fetchAll(PDO::FETCH_ASSOC);

    // Galleries
    $stmtGal = $pdo->prepare("SELECT `id`, `image_path`, `alt_text`, `display_order` FROM `course_galleries` WHERE `course_id` = ? ORDER BY `display_order` ASC, `id` ASC");
    $stmtGal->execute([$courseId]);
    $course['galleries'] = $stmtGal->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'course' => $course
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Unable to load course details.'
    ]);
}
