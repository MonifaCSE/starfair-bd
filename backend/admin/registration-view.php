<?php
/**
 * STAR FAIR - Admin Registration Detail & Secure Document Viewer API
 * Protected by admin session check.
 */

require_once __DIR__ . '/../database.php';

// Strict session check
if (empty($_SESSION['admin_logged_in'])) {
    http_response_code(403);
    die("Access Denied. Authentication required.");
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$file_type = $_GET['file'] ?? '';

if (!$id) {
    http_response_code(400);
    die("Bad Request. Invalid or missing Registration ID.");
}

try {
    // Fetch registration details
    $stmt = $pdo->prepare("SELECT * FROM `registrations` WHERE `id` = ? LIMIT 1");
    $stmt->execute([$id]);
    $registration = $stmt->fetch();
    
    if (!$registration) {
        http_response_code(404);
        die("Record not found.");
    }
    
    // --- SCENARIO A: Secure File Streaming ---
    if (!empty($file_type)) {
        $db_path = '';
        
        switch ($file_type) {
            case 'photo':
                $db_path = $registration['photo_path'];
                break;
            case 'nid':
                $db_path = $registration['nid_bc_path'];
                break;
            case 'portfolio':
                $db_path = $registration['portfolio_path'];
                break;
            default:
                http_response_code(400);
                die("Invalid file type requested.");
        }
        
        if (empty($db_path)) {
            http_response_code(404);
            die("File not uploaded for this registration.");
        }
        
        // Build absolute path on server
        $file_abs_path = REGISTRATION_UPLOAD_DIR . '/' . $db_path;
        
        // Prevent directory traversal attacks
        $real_base = realpath(REGISTRATION_UPLOAD_DIR);
        $real_file = realpath($file_abs_path);
        
        if ($real_file === false || !str_starts_with($real_file, $real_base)) {
            http_response_code(403);
            die("Access Denied: Invalid file path traversal attempt.");
        }
        
        if (!is_file($real_file)) {
            http_response_code(404);
            die("The requested file does not exist on the server storage.");
        }
        
        // Determine file MIME type securely
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime_type = $finfo->file($real_file);
        
        // Clear buffer and send file stream
        if (ob_get_level()) ob_end_clean();
        
        header('Content-Type: ' . $mime_type);
        header('Content-Length: ' . filesize($real_file));
        
        // Use inline display for images and PDFs, attachment for other formats
        $disposition = (str_starts_with($mime_type, 'image/') || $mime_type === 'application/pdf') ? 'inline' : 'attachment';
        header('Content-Disposition: ' . $disposition . '; filename="' . basename($real_file) . '"');
        
        readfile($real_file);
        exit;
    }
    
    // --- SCENARIO B: Return Registration Metadata (JSON) ---
    header('Content-Type: application/json; charset=utf-8');
    
    // Parse JSON arrays for programmatic display on client side
    $registration['programmes'] = json_decode($registration['programmes'], true) ?? [];
    $registration['events'] = json_decode($registration['events'], true) ?? [];
    
    // Transform absolute storage path back to secure download URLs
    // The client will fetch these secure proxy URLs instead of direct storage paths
    $registration['photoUrl'] = 'backend/admin/registration-view.php?id=' . $id . '&file=photo';
    $registration['nidBcUrl'] = 'backend/admin/registration-view.php?id=' . $id . '&file=nid';
    $registration['portfolioUrl'] = $registration['portfolio_path'] ? 'backend/admin/registration-view.php?id=' . $id . '&file=portfolio' : '';
    
    echo json_encode($registration, JSON_UNESCAPED_SLASHES);

} catch (PDOException $e) {
    http_response_code(500);
    die("Database query error: " . $e->getMessage());
}
