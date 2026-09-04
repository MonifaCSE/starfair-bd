<?php
/**
 * STAR FAIR - Admin API to add or edit a news article
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
$title = trim($_POST['newsTitle'] ?? '');
$category = trim($_POST['newsCategory'] ?? 'General');
$summary = trim($_POST['newsSummary'] ?? '');
$content = trim($_POST['newsContent'] ?? '');
$order = filter_input(INPUT_POST, 'newsOrder', FILTER_VALIDATE_INT);
$published_date = trim($_POST['newsDate'] ?? '');
$is_featured = isset($_POST['isFeatured']) ? 1 : 0;

if (empty($title) || empty($summary) || empty($content) || empty($published_date) || $order === false) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
    exit;
}

require_once __DIR__ . '/../database.php';

try {
    $image_path = null;
    $has_upload = isset($_FILES['newsImage']) && $_FILES['newsImage']['error'] !== UPLOAD_ERR_NO_FILE;

    // Check if editing
    $existing = null;
    if ($id) {
        $stmt = $pdo->prepare("SELECT * FROM `news` WHERE `id` = ?");
        $stmt->execute([$id]);
        $existing = $stmt->fetch();
        if (!$existing) {
            echo json_encode(['success' => false, 'message' => 'News article not found.']);
            exit;
        }
    }

    // Process file upload
    if ($has_upload) {
        $file = $_FILES['newsImage'];
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'File upload error: ' . $file['error']]);
            exit;
        }

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
            echo json_encode(['success' => false, 'message' => 'Invalid image format. Allowed formats: JPG, PNG, WEBP.']);
            exit;
        }

        $extension = $allowed_mimes[$mime_type];
        
        // Create directory
        $dest_dir = UPLOAD_DIR . '/news';
        if (!is_dir($dest_dir)) {
            mkdir($dest_dir, 0755, true);
        }

        $filename = 'news_' . bin2hex(random_bytes(8)) . '.' . $extension;
        $destination = $dest_dir . '/' . $filename;
        $relative_path = 'uploads/news/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            echo json_encode(['success' => false, 'message' => 'Failed to save news image on server.']);
            exit;
        }

        $image_path = $relative_path;

        // Delete old image if updating
        if ($existing && !empty($existing['image_path'])) {
            $old_file = ROOT_PATH . '/' . $existing['image_path'];
            if (is_file($old_file) && str_contains($existing['image_path'], 'uploads/')) {
                @unlink($old_file);
            }
        }
    } else {
        // Keep old image if editing, or fail if creating new without image
        if ($existing) {
            $image_path = $existing['image_path'];
        } else {
            echo json_encode(['success' => false, 'message' => 'Featured cover image is required for new articles.']);
            exit;
        }
    }

    // Begin database transaction to ensure featured state consistency
    $pdo->beginTransaction();

    if ($is_featured === 1) {
        // Unset all other featured news items
        $pdo->exec("UPDATE `news` SET `is_featured` = 0");
    }

    if ($existing) {
        $stmt = $pdo->prepare("UPDATE `news` SET `title` = ?, `category` = ?, `summary` = ?, `content` = ?, `image_path` = ?, `is_featured` = ?, `display_order` = ?, `published_date` = ? WHERE `id` = ?");
        $stmt->execute([$title, $category, $summary, $content, $image_path, $is_featured, $order, $published_date, $id]);
        $msg = 'News article updated successfully!';
    } else {
        $stmt = $pdo->prepare("INSERT INTO `news` (`title`, `category`, `summary`, `content`, `image_path`, `is_featured`, `display_order`, `published_date`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $category, $summary, $content, $image_path, $is_featured, $order, $published_date]);
        $msg = 'News article published successfully!';
    }

    $pdo->commit();

    echo json_encode(['success' => true, 'message' => $msg]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['success' => false, 'message' => 'Database error occurred: ' . $e->getMessage()]);
}
