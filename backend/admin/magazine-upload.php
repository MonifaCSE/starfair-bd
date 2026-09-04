<?php
/**
 * STAR FAIR - Admin Magazine Edition Upload API
 * Handles secure file uploads for PDF issues & cover images.
 * Protected by admin session check.
 */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed.']);
    exit;
}

require_once __DIR__ . '/../database.php';

header('Content-Type: application/json; charset=utf-8');

// Check if POST request size exceeds post_max_size
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST) && isset($_SERVER['CONTENT_LENGTH'])) {
    $maxSize = ini_get('post_max_size');
    echo json_encode([
        'success' => false,
        'message' => "The upload failed because the request size exceeds the server's post_max_size limit ({$maxSize}). Please compress your files or increase this limit in php.ini."
    ]);
    exit;
}

// Strict session check
if (empty($_SESSION['admin_logged_in'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Access Denied. Authentication required.']);
    exit;
}

try {
    $title = trim($_POST['magTitle'] ?? '');
    $description = trim($_POST['magDesc'] ?? '');
    $date_text = trim($_POST['magDate'] ?? '');
    $display_order = filter_input(INPUT_POST, 'magOrder', FILTER_VALIDATE_INT);

    if (empty($title) || empty($description) || empty($date_text) || $display_order === false) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all required edition metadata fields.']);
        exit;
    }

    // Create magazines upload folder if not exists
    if (!is_dir(MAGAZINE_UPLOAD_DIR)) {
        if (!mkdir(MAGAZINE_UPLOAD_DIR, 0755, true)) {
            echo json_encode(['success' => false, 'message' => 'Directory creation failed.']);
            exit;
        }
    }

    // --- 1. UPLOAD PDF ISSUE (Required) ---
    if (!isset($_FILES['magPdfFile'])) {
        echo json_encode(['success' => false, 'message' => 'Magazine PDF file is required.']);
        exit;
    }

    if ($_FILES['magPdfFile']['error'] !== UPLOAD_ERR_OK) {
        $errorCode = $_FILES['magPdfFile']['error'];
        $msg = 'Failed to upload PDF file.';
        if ($errorCode === UPLOAD_ERR_INI_SIZE) {
            $maxUpload = ini_get('upload_max_filesize');
            $msg = "The PDF file size exceeds the server's upload_max_filesize limit ({$maxUpload}). Please compress your PDF or increase this limit in php.ini.";
        } else if ($errorCode === UPLOAD_ERR_FORM_SIZE) {
            $msg = "The PDF file size exceeds the MAX_FILE_SIZE limit specified in the HTML form.";
        } else if ($errorCode === UPLOAD_ERR_PARTIAL) {
            $msg = "The file was only partially uploaded.";
        } else if ($errorCode === UPLOAD_ERR_NO_FILE) {
            $msg = "Magazine PDF file is required.";
        }
        echo json_encode(['success' => false, 'message' => $msg]);
        exit;
    }

    $pdf_file = $_FILES['magPdfFile'];
    
    // PDF Size validation (e.g. limit to 25MB)
    if ($pdf_file['size'] > 25 * 1024 * 1024) {
        echo json_encode(['success' => false, 'message' => 'PDF size cannot exceed 25MB.']);
        exit;
    }

    // PDF MIME check
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $pdf_mime = $finfo->file($pdf_file['tmp_name']);
    if (!array_key_exists($pdf_mime, $ALLOWED_PDF_TYPES)) {
        echo json_encode(['success' => false, 'message' => 'Invalid file format. Upload must be a valid PDF document.']);
        exit;
    }

    // Generate safe unique filename
    $pdf_filename = 'issue_' . bin2hex(random_bytes(10)) . '.pdf';
    $pdf_destination = MAGAZINE_UPLOAD_DIR . '/' . $pdf_filename;

    if (!move_uploaded_file($pdf_file['tmp_name'], $pdf_destination)) {
        echo json_encode(['success' => false, 'message' => 'Failed to save PDF on server.']);
        exit;
    }

    // --- 2. SAVE TO DATABASE ---
    // cover_path is always NULL — the PDF first page is rendered as cover dynamically by the frontend.
    $stmt = $pdo->prepare("INSERT INTO `magazines` (title, description, date_text, pdf_path, cover_path, display_order) VALUES (?, ?, ?, ?, NULL, ?)");
    $stmt->execute([
        $title,
        $description,
        $date_text,
        $pdf_filename,
        $display_order
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'New magazine edition published and uploaded successfully!'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Upload failed: ' . $e->getMessage()
    ]);
}
