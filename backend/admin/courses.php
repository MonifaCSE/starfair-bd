<?php
/**
 * STAR FAIR - Admin Course Directory API Endpoint
 * Returns all courses (published and draft) for the Admin Panel.
 */

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

require_once __DIR__ . '/../database.php';

if (empty($_SESSION['admin_logged_in'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access. Admin login required.']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM `courses` ORDER BY `display_order` ASC, `id` ASC");
    $stmt->execute();
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($courses as &$c) {
        $cId = $c['id'];
        
        $stmtMod = $pdo->prepare("SELECT `id`, `module_name`, `display_order` FROM `course_modules` WHERE `course_id` = ? ORDER BY `display_order` ASC, `id` ASC");
        $stmtMod->execute([$cId]);
        $c['modules'] = $stmtMod->fetchAll(PDO::FETCH_ASSOC);

        $stmtSch = $pdo->prepare("SELECT `id`, `day_name`, `time_text`, `topic_text`, `display_order` FROM `course_schedules` WHERE `course_id` = ? ORDER BY `display_order` ASC, `id` ASC");
        $stmtSch->execute([$cId]);
        $c['schedules'] = $stmtSch->fetchAll(PDO::FETCH_ASSOC);

        $stmtCar = $pdo->prepare("SELECT `id`, `career_title`, `display_order` FROM `course_careers` WHERE `course_id` = ? ORDER BY `display_order` ASC, `id` ASC");
        $stmtCar->execute([$cId]);
        $c['careers'] = $stmtCar->fetchAll(PDO::FETCH_ASSOC);

        $stmtGal = $pdo->prepare("SELECT `id`, `image_path`, `alt_text`, `display_order` FROM `course_galleries` WHERE `course_id` = ? ORDER BY `display_order` ASC, `id` ASC");
        $stmtGal->execute([$cId]);
        $c['galleries'] = $stmtGal->fetchAll(PDO::FETCH_ASSOC);
    }

    echo json_encode([
        'success' => true,
        'count' => count($courses),
        'courses' => $courses
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to fetch course directory.']);
}
