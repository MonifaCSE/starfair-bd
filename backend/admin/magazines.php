<?php
/**
 * STAR FAIR - Admin Magazines Listing API
 * Returns all magazines as JSON.
 * Protected by admin session check.
 */

require_once __DIR__ . '/../database.php';

header('Content-Type: application/json; charset=utf-8');

// Strict session check
if (empty($_SESSION['admin_logged_in'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Access Denied. Authentication required.']);
    exit;
}

try {
    $stmt = $pdo->query("SELECT * FROM `magazines` ORDER BY `display_order` ASC, `created_at` DESC");
    $rows = $stmt->fetchAll();
    
    $magazinesList = [];
    foreach ($rows as $row) {
        $magazinesList[] = [
            'id'          => $row['id'],
            'title'       => $row['title'],
            'description' => $row['description'],
            'dateText'    => $row['date_text'],
            'pdfUrl'      => '../uploads/magazines/' . $row['pdf_path'],
            'coverUrl'    => !empty($row['cover_path']) ? '../uploads/magazines/' . $row['cover_path'] : '',
            'order'       => (int)$row['display_order']
        ];
    }
    
    echo json_encode($magazinesList, JSON_UNESCAPED_SLASHES);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
