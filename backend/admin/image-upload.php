<?php
/**
 * STAR FAIR - Admin Image Upload API
 * Replaces a specific Event or Gallery page image slot.
 */

require_once __DIR__ . '/../../backend/config.php';

header('Content-Type: application/json; charset=utf-8');

// Strict session check
if (empty($_SESSION['admin_logged_in'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Access Denied. Authentication required.']);
    exit;
}

// 1. Gather & Sanitize POST parameters
$section = trim($_POST['section'] ?? '');
$page = trim($_POST['page'] ?? '');
$image_key = trim($_POST['image_key'] ?? '');

// 2. Security: Validate inputs against strict allowed values
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

// Check if request size exceeds post_max_size
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST) && isset($_SERVER['CONTENT_LENGTH'])) {
    $maxSize = ini_get('post_max_size');
    echo json_encode([
        'success' => false,
        'message' => "The upload failed because the request size exceeds the server's post_max_size limit ({$maxSize})."
    ]);
    exit;
}

// 3. Validate Uploaded File
if (!isset($_FILES['imageFile']) || $_FILES['imageFile']['error'] !== UPLOAD_ERR_OK) {
    $errorCode = $_FILES['imageFile']['error'] ?? null;
    $msg = 'Image file is required.';
    if ($errorCode === UPLOAD_ERR_INI_SIZE) {
        $maxUpload = ini_get('upload_max_filesize');
        $msg = "The image size exceeds the server's upload_max_filesize limit ({$maxUpload}).";
    }
    echo json_encode(['success' => false, 'message' => $msg]);
    exit;
}

$file = $_FILES['imageFile'];

// Limit file size to 5MB (as requested)
if ($file['size'] > 5 * 1024 * 1024) {
    echo json_encode(['success' => false, 'message' => 'Image file size cannot exceed 5MB.']);
    exit;
}

// Server-side MIME validation
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime_type = $finfo->file($file['tmp_name']);
$allowed_mimes = [
    'image/jpeg' => 'jpg',
    'image/jpg' => 'jpg',
    'image/png' => 'png',
    'image/webp' => 'webp'
];

if (!array_key_exists($mime_type, $allowed_mimes)) {
    echo json_encode(['success' => false, 'message' => 'Invalid image format. Allowed formats: JPG, JPEG, PNG, WEBP.']);
    exit;
}

$extension = $allowed_mimes[$mime_type];

// 4. Set up destination folder and generate unique filename
$dest_subfolder = UPLOAD_DIR . '/' . $section;
if (!is_dir($dest_subfolder)) {
    if (!mkdir($dest_subfolder, 0777, true)) {
        echo json_encode(['success' => false, 'message' => 'Failed to create destination folder on server.']);
        exit;
    }
    @chmod($dest_subfolder, 0777);
}

$filename = $image_key . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
$destination_path = $dest_subfolder . '/' . $filename;
$relative_path = 'uploads/' . $section . '/' . $filename;

if (!move_uploaded_file($file['tmp_name'], $destination_path)) {
    echo json_encode(['success' => false, 'message' => 'Failed to save the image on server. Check folder permissions.']);
    exit;
}
@chmod($destination_path, 0666);

// 5. Database Transaction: delete old custom image and write the new one
require_once __DIR__ . '/../database.php';

try {
    // Check if there was a previous custom upload for this slot
    $stmt = $pdo->prepare("SELECT `image_path` FROM `page_images` WHERE `section` = ? AND `page` = ? AND `image_key` = ?");
    $stmt->execute([$section, $page, $image_key]);
    $old_record = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($old_record) {
        // Delete old file if it exists in the uploads path
        $old_file = ROOT_PATH . '/' . $old_record['image_path'];
        if (is_file($old_file) && str_contains($old_record['image_path'], 'uploads/')) {
            @unlink($old_file);
        }
    }

    // Insert or update DB mapping
    $stmt = $pdo->prepare("INSERT INTO `page_images` (`section`, `page`, `image_key`, `image_path`) VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE `image_path` = ?, `updated_at` = CURRENT_TIMESTAMP");
    $stmt->execute([$section, $page, $image_key, $relative_path, $relative_path]);

    echo json_encode([
        'success' => true,
        'message' => 'Image replaced successfully.',
        'image_path' => $relative_path
    ]);

} catch (Exception $e) {
    // Cleanup the uploaded file on DB error
    @unlink($destination_path);
    echo json_encode(['success' => false, 'message' => 'Database error occurred: ' . $e->getMessage()]);
}
