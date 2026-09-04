<?php
/**
 * STAR FAIR - Admin Course Delete & Status Toggle API Endpoint
 */

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

require_once __DIR__ . '/../database.php';

if (empty($_SESSION['admin_logged_in'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access. Admin login required.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$id = intval($_POST['id'] ?? ($input['id'] ?? 0));
$action = trim($_POST['action'] ?? ($input['action'] ?? 'delete'));

if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid course ID.']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM `courses` WHERE `id` = ?");
    $stmt->execute([$id]);
    $course = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$course) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Course not found.']);
        exit;
    }

    if ($action === 'toggle_status') {
        $newStatus = ($course['status'] === 'published') ? 'draft' : 'published';
        $stmtStatus = $pdo->prepare("UPDATE `courses` SET `status` = ? WHERE `id` = ?");
        $stmtStatus->execute([$newStatus, $id]);

        echo json_encode([
            'success' => true,
            'message' => "Course status updated to '{$newStatus}'.",
            'status' => $newStatus
        ]);
        exit;
    }

    if ($action === 'delete') {
        // Fetch gallery images to clean up files
        $stmtGal = $pdo->prepare("SELECT image_path FROM `course_galleries` WHERE `course_id` = ?");
        $stmtGal->execute([$id]);
        $galleries = $stmtGal->fetchAll(PDO::FETCH_ASSOC);

        // Delete course record (Foreign Key CASCADE will delete modules, schedules, careers, galleries)
        $stmtDel = $pdo->prepare("DELETE FROM `courses` WHERE `id` = ?");
        $stmtDel->execute([$id]);

        // Clean up course uploads directory if safe
        $slug = $course['slug'];
        $courseDir = UPLOAD_DIR . '/courses/' . $slug;
        if (file_exists($courseDir) && is_dir($courseDir)) {
            $files = glob($courseDir . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    @unlink($file);
                }
            }
            @rmdir($courseDir);
        }

        echo json_encode([
            'success' => true,
            'message' => "Course '{$course['title']}' deleted successfully."
        ]);
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Unknown action.']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to process request: ' . $e->getMessage()]);
}
