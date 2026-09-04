<?php
/**
 * STAR FAIR - Admin API to add or edit an official collaboration partner
 */

require_once __DIR__ . '/../../backend/config.php';

header('Content-Type: application/json; charset=utf-8');

// Strict session check
if (empty($_SESSION['admin_logged_in'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Access Denied. Authentication required.']);
    exit;
}

$id = filter_input(INPUT_POST, 'partnerId', FILTER_VALIDATE_INT);
$name = trim($_POST['partnerName'] ?? '');
$order = filter_input(INPUT_POST, 'partnerOrder', FILTER_VALIDATE_INT);

if (empty($name) || $order === false) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
    exit;
}

require_once __DIR__ . '/../database.php';

try {
    $image_path = null;
    $has_upload = isset($_FILES['partnerLogo']) && $_FILES['partnerLogo']['error'] !== UPLOAD_ERR_NO_FILE;

    // Check if editing
    $existing = null;
    if ($id) {
        $stmt = $pdo->prepare("SELECT * FROM `partners` WHERE `id` = ?");
        $stmt->execute([$id]);
        $existing = $stmt->fetch();
        if (!$existing) {
            echo json_encode(['success' => false, 'message' => 'Partner profile not found.']);
            exit;
        }
    }

    // Process file upload
    if ($has_upload) {
        $file = $_FILES['partnerLogo'];
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'File upload error: ' . $file['error']]);
            exit;
        }

        if ($file['size'] > 5 * 1024 * 1024) {
            echo json_encode(['success' => false, 'message' => 'Logo file size cannot exceed 5MB.']);
            exit;
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime_type = $finfo->file($file['tmp_name']);
        
        $allowed_mimes = [
            'image/jpeg' => 'jpg',
            'image/jpg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/svg+xml' => 'svg'
        ];

        if (!array_key_exists($mime_type, $allowed_mimes)) {
            echo json_encode(['success' => false, 'message' => 'Invalid image format. Allowed formats: JPG, PNG, WEBP, SVG.']);
            exit;
        }

        $extension = $allowed_mimes[$mime_type];
        
        // Create directory
        $dest_dir = UPLOAD_DIR . '/partners';
        if (!is_dir($dest_dir)) {
            mkdir($dest_dir, 0755, true);
        }

        $filename = 'partner_' . bin2hex(random_bytes(8)) . '.' . $extension;
        $destination = $dest_dir . '/' . $filename;
        $relative_path = 'uploads/partners/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            echo json_encode(['success' => false, 'message' => 'Failed to save logo file on server.']);
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
        // Keep old image if editing, or fail if creating new without logo
        if ($existing) {
            $image_path = $existing['image_path'];
        } else {
            echo json_encode(['success' => false, 'message' => 'Logo image is required for new partner.']);
            exit;
        }
    }

    if ($existing) {
        $stmt = $pdo->prepare("UPDATE `partners` SET `name` = ?, `image_path` = ?, `display_order` = ? WHERE `id` = ?");
        $stmt->execute([$name, $image_path, $order, $id]);
        $msg = 'Partner updated successfully!';
    } else {
        $stmt = $pdo->prepare("INSERT INTO `partners` (`name`, `image_path`, `display_order`) VALUES (?, ?, ?)");
        $stmt->execute([$name, $image_path, $order]);
        $msg = 'Partner added successfully!';
    }

    echo json_encode(['success' => true, 'message' => $msg]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error occurred: ' . $e->getMessage()]);
}
