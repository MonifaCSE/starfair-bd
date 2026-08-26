<?php
/**
 * STAR FAIR - Student Registration Submit API
 * Hostinger & Local XAMPP/MAMP Compatible
 * Returns JSON responses.
 */

// Allow POST only
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed. Only POST requests are allowed.']);
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

try {
    // --- 1. COLLECT & SANITIZE FORM INPUTS ---
    $name = trim($_POST['name'] ?? '');
    $dob = trim($_POST['dob'] ?? '');
    $father_name = trim($_POST['father_name'] ?? '');
    $mother_name = trim($_POST['mother_name'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $blood_group = trim($_POST['blood_group'] ?? '');
    $nationality = trim($_POST['nationality'] ?? '');
    $occupation = trim($_POST['occupation'] ?? '');
    $education = trim($_POST['education'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    $alt_mobile = trim($_POST['alt_mobile'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $present_address = trim($_POST['present_address'] ?? '');
    $permanent_address = trim($_POST['permanent_address'] ?? '');
    $guardian_name = trim($_POST['guardian_name'] ?? '');
    $guardian_mobile = trim($_POST['guardian_mobile'] ?? '');
    $emergency_name = trim($_POST['emergency_name'] ?? '');
    $emergency_relation = trim($_POST['emergency_relation'] ?? '');
    
    // Arrays from checkbox lists
    $programmes_arr = $_POST['programmes'] ?? [];
    $events_arr = $_POST['events'] ?? [];
    
    $previous_experience = trim($_POST['previous_experience'] ?? '');
    $medical_conditions = trim($_POST['medical_conditions'] ?? '');
    $special_skills = trim($_POST['special_skills'] ?? '');
    $why_join = trim($_POST['why_join'] ?? '');

    // --- 2. SERVER-SIDE VALIDATION ---
    if (empty($name) || empty($dob) || empty($father_name) || empty($mother_name) || empty($gender) || 
        empty($blood_group) || empty($nationality) || empty($occupation) || empty($education) || 
        empty($mobile) || empty($email) || empty($present_address) || empty($permanent_address) || 
        empty($guardian_name) || empty($guardian_mobile) || empty($emergency_name) || empty($emergency_relation) || 
        empty($why_join)) {
        
        echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
        exit;
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Please provide a valid email address.']);
        exit;
    }

    // Validate phone format (Must be valid characters, min 8 digits)
    if (!preg_match('/^[+0-9\s\-]{8,20}$/', $mobile)) {
        echo json_encode(['success' => false, 'message' => 'Please provide a valid phone number.']);
        exit;
    }

    // Program & event validation
    if (empty($programmes_arr)) {
        echo json_encode(['success' => false, 'message' => 'Please select at least one training programme.']);
        exit;
    }

    // Convert arrays to JSON strings for consistent storage
    $programmes = json_encode($programmes_arr, JSON_UNESCAPED_UNICODE);
    $events = json_encode($events_arr, JSON_UNESCAPED_UNICODE);

    // --- 3. SECURE FILE UPLOADS ---
    
    // Generate a unique registration reference ID (for folder isolation)
    $unique_id = bin2hex(random_bytes(12)); // e.g. "4b8e2a1c0d5f..."
    $reg_folder = REGISTRATION_UPLOAD_DIR . '/' . $unique_id;

    // Verify files presence (Photo & NID are required)
    if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'Student Photo upload is required.']);
        exit;
    }
    if (!isset($_FILES['nid_bc']) || $_FILES['nid_bc']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'NID / Birth Certificate document is required.']);
        exit;
    }

    // Create unique registration directory if it does not exist
    if (!is_dir($reg_folder)) {
        if (!mkdir($reg_folder, 0755, true)) {
            echo json_encode(['success' => false, 'message' => 'Server directory creation failed. Contact developer.']);
            exit;
        }
    }

    // Helper logic to securely validate and process file upload
    $upload_file = function($file_input, $prefix) use ($reg_folder, $ALLOWED_DOC_TYPES, $unique_id) {
        $file = $_FILES[$file_input];
        
        // Basic upload error check
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Error during file upload: " . $file['error']);
        }
        
        // Size validation (MAX_FILE_SIZE = 5MB)
        if ($file['size'] > MAX_FILE_SIZE) {
            throw new Exception("File size exceeds 5MB limit for " . htmlspecialchars($file['name']));
        }
        
        // MIME-type verification (Do not trust client-sent mime type)
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime_type = $finfo->file($file['tmp_name']);
        
        if (!array_key_exists($mime_type, $ALLOWED_DOC_TYPES)) {
            throw new Exception("Invalid file type: " . htmlspecialchars($file['name']) . ". Only JPG, PNG, WEBP, and PDF documents are allowed.");
        }
        
        // Double check extension matches MIME type
        $ext = $ALLOWED_DOC_TYPES[$mime_type];
        $orig_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        // Reject mismatches or executable/dangerous extensions
        $dangerous_exts = ['php', 'phtml', 'phar', 'cgi', 'pl', 'py', 'sh', 'asp', 'aspx', 'jsp', 'js', 'html', 'htm', 'exe'];
        if (in_array($orig_ext, $dangerous_exts) || ($orig_ext !== 'jpg' && $orig_ext !== 'jpeg' && $orig_ext !== 'png' && $orig_ext !== 'webp' && $orig_ext !== 'pdf')) {
            throw new Exception("Security Alert: Suspicious file extension blocked.");
        }
        
        // Generate random, unique safe filename (Never use original filename to prevent directory traversal)
        $safe_filename = $prefix . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
        $destination = $reg_folder . '/' . $safe_filename;
        
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new Exception("Failed to save uploaded file on host server.");
        }
        
        // Return relative path for database storage (which is secure under the registrations folder)
        return $unique_id . '/' . $safe_filename;
    };

    // Upload files securely
    $photo_path = $upload_file('photo', 'photo');
    $nid_bc_path = $upload_file('nid_bc', 'nid_bc');
    
    // Portfolio is optional
    $portfolio_path = null;
    if (isset($_FILES['portfolio']) && $_FILES['portfolio']['error'] === UPLOAD_ERR_OK) {
        $portfolio_path = $upload_file('portfolio', 'portfolio');
    }

    // --- 4. DATABASE INSERTION ---
    $query = "INSERT INTO `registrations` (
        `name`, `dob`, `father_name`, `mother_name`, `gender`, `blood_group`, `nationality`, 
        `occupation`, `education`, `mobile`, `alt_mobile`, `email`, `present_address`, 
        `permanent_address`, `guardian_name`, `guardian_mobile`, `emergency_name`, 
        `emergency_relation`, `programmes`, `events`, `previous_experience`, 
        `medical_conditions`, `special_skills`, `why_join`, `photo_path`, `nid_bc_path`, `portfolio_path`
    ) VALUES (
        :name, :dob, :father_name, :mother_name, :gender, :blood_group, :nationality, 
        :occupation, :education, :mobile, :alt_mobile, :email, :present_address, 
        :permanent_address, :guardian_name, :guardian_mobile, :emergency_name, 
        :emergency_relation, :programmes, :events, :previous_experience, 
        :medical_conditions, :special_skills, :why_join, :photo_path, :nid_bc_path, :portfolio_path
    )";

    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':name' => $name,
        ':dob' => $dob,
        ':father_name' => $father_name,
        ':mother_name' => $mother_name,
        ':gender' => $gender,
        ':blood_group' => $blood_group,
        ':nationality' => $nationality,
        ':occupation' => $occupation,
        ':education' => $education,
        ':mobile' => $mobile,
        ':alt_mobile' => empty($alt_mobile) ? null : $alt_mobile,
        ':email' => $email,
        ':present_address' => $present_address,
        ':permanent_address' => $permanent_address,
        ':guardian_name' => $guardian_name,
        ':guardian_mobile' => $guardian_mobile,
        ':emergency_name' => $emergency_name,
        ':emergency_relation' => $emergency_relation,
        ':programmes' => $programmes,
        ':events' => $events,
        ':previous_experience' => empty($previous_experience) ? null : $previous_experience,
        ':medical_conditions' => empty($medical_conditions) ? null : $medical_conditions,
        ':special_skills' => empty($special_skills) ? null : $special_skills,
        ':why_join' => $why_join,
        ':photo_path' => $photo_path,
        ':nid_bc_path' => $nid_bc_path,
        ':portfolio_path' => $portfolio_path
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Your admission application has been successfully submitted and saved to our secure database!'
    ]);

} catch (Exception $e) {
    // If files were uploaded, clean them up on failure
    if (isset($reg_folder) && is_dir($reg_folder)) {
        // Delete all files in the directory
        $files = glob($reg_folder . '/*');
        foreach ($files as $file) {
            if (is_file($file)) @unlink($file);
        }
        @rmdir($reg_folder);
    }
    
    echo json_encode([
        'success' => false,
        'message' => 'Failed to process application: ' . $e->getMessage()
    ]);
}
