<?php
/**
 * STAR FAIR - Admin Save Course API Endpoint (Create & Edit)
 * Handles course fields, hero image upload, gallery image uploads, and child items.
 */

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

require_once __DIR__ . '/../database.php';

if (empty($_SESSION['admin_logged_in'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access. Admin login required.']);
    exit;
}

function slugify($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return empty($text) ? 'n-a' : $text;
}

try {
    $id = intval($_POST['id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $slugInput = trim($_POST['slug'] ?? '');
    $category = trim($_POST['category'] ?? 'core_programme');
    $categoryBadge = trim($_POST['category_badge'] ?? 'CORE PROGRAMME');
    $shortDesc = trim($_POST['short_description'] ?? '');
    $fullDesc = trim($_POST['full_description'] ?? '');
    $duration = trim($_POST['duration'] ?? '');
    $admissionFee = trim($_POST['admission_fee'] ?? '');
    $courseFee = trim($_POST['course_fee'] ?? '');
    $eligibility = trim($_POST['eligibility'] ?? '');
    $ageReq = trim($_POST['age_requirement'] ?? '');
    $courseType = trim($_POST['course_type'] ?? '');
    $status = in_array($_POST['status'] ?? '', ['published', 'draft']) ? $_POST['status'] : 'published';
    $displayOrder = intval($_POST['display_order'] ?? 1);
    $metaTitle = trim($_POST['meta_title'] ?? '');
    $metaDescription = trim($_POST['meta_description'] ?? '');

    if (empty($title)) {
        echo json_encode(['success' => false, 'message' => 'Course Title is required.']);
        exit;
    }
    if (empty($shortDesc)) {
        echo json_encode(['success' => false, 'message' => 'Short Description is required.']);
        exit;
    }
    if (empty($fullDesc)) {
        echo json_encode(['success' => false, 'message' => 'Full Description is required.']);
        exit;
    }
    if (empty($duration)) {
        echo json_encode(['success' => false, 'message' => 'Duration is required.']);
        exit;
    }

    // Slug formatting & uniqueness check
    $baseSlug = !empty($slugInput) ? slugify($slugInput) : slugify($title);
    $slug = $baseSlug;
    $counter = 1;

    while (true) {
        $stmtSlug = $pdo->prepare("SELECT id FROM `courses` WHERE `slug` = ? AND `id` != ?");
        $stmtSlug->execute([$slug, $id]);
        if (!$stmtSlug->fetch()) {
            break;
        }
        $slug = $baseSlug . '-' . $counter;
        $counter++;
    }

    // Ensure upload folder for this course
    $courseUploadDir = UPLOAD_DIR . '/courses/' . $slug;
    if (!file_exists($courseUploadDir)) {
        @mkdir($courseUploadDir, 0755, true);
    }

    // Existing course lookup
    $existingCourse = null;
    if ($id > 0) {
        $stmtEx = $pdo->prepare("SELECT * FROM `courses` WHERE `id` = ?");
        $stmtEx->execute([$id]);
        $existingCourse = $stmtEx->fetch(PDO::FETCH_ASSOC);
    }

    // Hero Image Upload Processing
    $heroImagePath = $existingCourse['hero_image'] ?? 'assets/images/programs/fasion.jpg';

    if (isset($_FILES['hero_image_file']) && $_FILES['hero_image_file']['error'] === UPLOAD_ERR_OK) {
        $fileTmp = $_FILES['hero_image_file']['tmp_name'];
        $fileName = $_FILES['hero_image_file']['name'];
        $fileSize = $_FILES['hero_image_file']['size'];

        if ($fileSize > 5 * 1024 * 1024) {
            echo json_encode(['success' => false, 'message' => 'Hero image size exceeds 5MB limit.']);
            exit;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $fileTmp);
        finfo_close($finfo);

        $allowedTypes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp'
        ];

        if (!array_key_exists($mimeType, $allowedTypes)) {
            echo json_encode(['success' => false, 'message' => 'Invalid hero image format. Allowed: JPG, PNG, WEBP.']);
            exit;
        }

        $ext = $allowedTypes[$mimeType];
        $newFileName = 'hero_' . bin2hex(random_bytes(8)) . '.' . $ext;
        $targetPath = $courseUploadDir . '/' . $newFileName;

        if (move_uploaded_file($fileTmp, $targetPath)) {
            // Delete old hero file if inside uploads/courses/
            if (!empty($existingCourse['hero_image']) && str_contains($existingCourse['hero_image'], 'uploads/courses/')) {
                $oldFile = ROOT_PATH . '/' . ltrim($existingCourse['hero_image'], '/');
                if (file_exists($oldFile)) {
                    @unlink($oldFile);
                }
            }
            $heroImagePath = 'uploads/courses/' . $slug . '/' . $newFileName;
        }
    }

    $pdo->beginTransaction();

    if ($id > 0 && $existingCourse) {
        $sql = "UPDATE `courses` SET 
            `title` = ?, `slug` = ?, `category` = ?, `category_badge` = ?, `short_description` = ?, 
            `full_description` = ?, `hero_image` = ?, `duration` = ?, `admission_fee` = ?, `course_fee` = ?, 
            `eligibility` = ?, `age_requirement` = ?, `course_type` = ?, `status` = ?, `display_order` = ?, 
            `meta_title` = ?, `meta_description` = ?
            WHERE `id` = ?";
        $stmtSave = $pdo->prepare($sql);
        $stmtSave->execute([
            $title, $slug, $category, $categoryBadge, $shortDesc,
            $fullDesc, $heroImagePath, $duration, $admissionFee, $courseFee,
            $eligibility, $ageReq, $courseType, $status, $displayOrder,
            $metaTitle, $metaDescription, $id
        ]);
        $courseId = $id;
    } else {
        $sql = "INSERT INTO `courses` 
            (`title`, `slug`, `category`, `category_badge`, `short_description`, `full_description`, `hero_image`, `duration`, `admission_fee`, `course_fee`, `eligibility`, `age_requirement`, `course_type`, `status`, `display_order`, `meta_title`, `meta_description`) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmtSave = $pdo->prepare($sql);
        $stmtSave->execute([
            $title, $slug, $category, $categoryBadge, $shortDesc,
            $fullDesc, $heroImagePath, $duration, $admissionFee, $courseFee,
            $eligibility, $ageReq, $courseType, $status, $displayOrder,
            $metaTitle, $metaDescription
        ]);
        $courseId = $pdo->lastInsertId();
    }

    // Save Modules
    $pdo->prepare("DELETE FROM `course_modules` WHERE `course_id` = ?")->execute([$courseId]);
    $modules = $_POST['modules'] ?? [];
    if (is_array($modules)) {
        $stmtMod = $pdo->prepare("INSERT INTO `course_modules` (`course_id`, `module_name`, `display_order`) VALUES (?, ?, ?)");
        $modOrder = 1;
        foreach ($modules as $modName) {
            $modName = trim($modName);
            if (!empty($modName)) {
                $stmtMod->execute([$courseId, $modName, $modOrder++]);
            }
        }
    }

    // Save Schedules
    $pdo->prepare("DELETE FROM `course_schedules` WHERE `course_id` = ?")->execute([$courseId]);
    $schedules = $_POST['schedules'] ?? [];
    if (is_array($schedules)) {
        $stmtSch = $pdo->prepare("INSERT INTO `course_schedules` (`course_id`, `day_name`, `time_text`, `topic_text`, `display_order`) VALUES (?, ?, ?, ?, ?)");
        $schOrder = 1;
        foreach ($schedules as $sch) {
            if (!is_array($sch)) continue;
            $dayName = trim($sch['day_name'] ?? '');
            $timeText = trim($sch['time_text'] ?? '');
            $topicText = trim($sch['topic_text'] ?? '');
            if (!empty($dayName) || !empty($timeText) || !empty($topicText)) {
                $stmtSch->execute([$courseId, $dayName, $timeText, $topicText, $schOrder++]);
            }
        }
    }

    // Save Careers
    $pdo->prepare("DELETE FROM `course_careers` WHERE `course_id` = ?")->execute([$courseId]);
    $careers = $_POST['careers'] ?? [];
    if (is_array($careers)) {
        $stmtCar = $pdo->prepare("INSERT INTO `course_careers` (`course_id`, `career_title`, `display_order`) VALUES (?, ?, ?)");
        $carOrder = 1;
        foreach ($careers as $carTitle) {
            $carTitle = trim($carTitle);
            if (!empty($carTitle)) {
                $stmtCar->execute([$courseId, $carTitle, $carOrder++]);
            }
        }
    }

    // Process Existing Gallery Keep/Delete List
    $existingGalleries = $_POST['existing_galleries'] ?? []; // Array of existing gallery paths to keep
    $stmtCurGal = $pdo->prepare("SELECT * FROM `course_galleries` WHERE `course_id` = ?");
    $stmtCurGal->execute([$courseId]);
    $currentGalleries = $stmtCurGal->fetchAll(PDO::FETCH_ASSOC);

    $pdo->prepare("DELETE FROM `course_galleries` WHERE `course_id` = ?")->execute([$courseId]);

    $stmtGal = $pdo->prepare("INSERT INTO `course_galleries` (`course_id`, `image_path`, `alt_text`, `display_order`) VALUES (?, ?, ?, ?)");
    $galOrder = 1;

    // Re-insert kept existing gallery records
    if (is_array($existingGalleries)) {
        foreach ($existingGalleries as $gItem) {
            $imgPath = is_array($gItem) ? ($gItem['image_path'] ?? '') : $gItem;
            $altText = is_array($gItem) ? ($gItem['alt_text'] ?? '') : '';
            if (!empty($imgPath)) {
                $stmtGal->execute([$courseId, $imgPath, $altText, $galOrder++]);
            }
        }
    }

    // Handle Newly Uploaded Gallery Files
    if (isset($_FILES['gallery_files']) && is_array($_FILES['gallery_files']['name'])) {
        $allowedTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        for ($i = 0; $i < count($_FILES['gallery_files']['name']); $i++) {
            if ($_FILES['gallery_files']['error'][$i] === UPLOAD_ERR_OK) {
                $tmpFile = $_FILES['gallery_files']['tmp_name'][$i];
                $size = $_FILES['gallery_files']['size'][$i];
                
                if ($size <= 5 * 1024 * 1024) {
                    $mime = finfo_file($finfo, $tmpFile);
                    if (array_key_exists($mime, $allowedTypes)) {
                        $ext = $allowedTypes[$mime];
                        $gFileName = 'gallery_' . bin2hex(random_bytes(8)) . '.' . $ext;
                        $gTargetPath = $courseUploadDir . '/' . $gFileName;
                        if (move_uploaded_file($tmpFile, $gTargetPath)) {
                            $gPath = 'uploads/courses/' . $slug . '/' . $gFileName;
                            $stmtGal->execute([$courseId, $gPath, $title . ' Highlight', $galOrder++]);
                        }
                    }
                }
            }
        }
        finfo_close($finfo);
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => ($id > 0 ? 'Course updated successfully.' : 'New course created successfully.'),
        'course_id' => $courseId,
        'slug' => $slug
    ]);
} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to save course: ' . $e->getMessage()]);
}
