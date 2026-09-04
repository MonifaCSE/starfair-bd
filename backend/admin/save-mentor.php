<?php
/**
 * STAR FAIR - Admin API to save or update team member profiles (Mentors, Advisors, Trainers)
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
$name = trim($_POST['name'] ?? '');
$designation = trim($_POST['designation'] ?? '');
$bio = trim($_POST['bio'] ?? '');

if ($id <= 0 || empty($name) || empty($designation)) {
    echo json_encode(['success' => false, 'message' => 'Required profile details are missing.']);
    exit;
}

require_once __DIR__ . '/../database.php';

try {
    // Verify profile exists in database
    $stmt = $pdo->prepare("SELECT `id`, `type`, `image_path` FROM `mentors` WHERE `id` = ?");
    $stmt->execute([$id]);
    $member = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$member) {
        echo json_encode(['success' => false, 'message' => 'Profile not found.']);
        exit;
    }

    $image_path = $member['image_path'];

    // Handle optional file upload
    if (isset($_FILES['imageFile']) && $_FILES['imageFile']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['imageFile'];

        if ($file['size'] > 5 * 1024 * 1024) {
            echo json_encode(['success' => false, 'message' => 'Image file size cannot exceed 5MB.']);
            exit;
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime_type = $finfo->file($file['tmp_name']);
        $allowed_mimes = [
            'image/jpeg' => 'jpg',
            'image/jpg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp'
        ];

        if (!array_key_exists($mime_type, $allowed_mimes)) {
            echo json_encode(['success' => false, 'message' => 'Invalid format. Allowed formats: JPG, JPEG, PNG, WEBP.']);
            exit;
        }

        $extension = $allowed_mimes[$mime_type];
        
        // Setup folder uploads/mentors
        $dest_folder = UPLOAD_DIR . '/mentors';
        if (!is_dir($dest_folder)) {
            if (!mkdir($dest_folder, 0777, true)) {
                echo json_encode(['success' => false, 'message' => 'Failed to create destination folder.']);
                exit;
            }
            @chmod($dest_folder, 0777);
        }

        // Generate unique safe filename
        $filename = $member['type'] . '_' . $id . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
        $destination_path = $dest_folder . '/' . $filename;
        $relative_path = 'uploads/mentors/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destination_path)) {
            echo json_encode(['success' => false, 'message' => 'Failed to save profile image on server.']);
            exit;
        }
        @chmod($destination_path, 0666);

        // Delete old custom file if it existed
        if ($image_path && str_contains($image_path, 'uploads/')) {
            $old_file = ROOT_PATH . '/' . $image_path;
            if (is_file($old_file)) {
                @unlink($old_file);
            }
        }

        $image_path = $relative_path;
    }

    // Update database record
    $stmt = $pdo->prepare("UPDATE `mentors` SET `name` = ?, `designation` = ?, `bio` = ?, `image_path` = ?, `updated_at` = CURRENT_TIMESTAMP WHERE `id` = ?");
    $stmt->execute([$name, $designation, $bio, $image_path, $id]);

    echo json_encode([
        'success' => true,
        'message' => 'Profile updated successfully.',
        'image_path' => $image_path
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error occurred: ' . $e->getMessage()]);
}
