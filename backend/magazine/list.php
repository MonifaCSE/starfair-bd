<?php
/**
 * STAR FAIR - Magazines List API
 * Hostinger & Local XAMPP/MAMP Compatible
 * Returns JSON array of published magazines.
 */

require_once __DIR__ . '/../database.php';

header('Content-Type: application/json; charset=utf-8');

try {
    // Query magazines sorted by display_order ascending, then newest first
    $stmt = $pdo->query("SELECT * FROM `magazines` ORDER BY `display_order` ASC, `created_at` DESC");
    $rows = $stmt->fetchAll();
    
    $magazinesList = [];
    
    foreach ($rows as $row) {
        // Construct full relative URLs for frontend consumption
        $pdfUrl = 'uploads/magazines/' . $row['pdf_path'];
        
        $coverUrl = '';
        if (!empty($row['cover_path'])) {
            $coverUrl = 'uploads/magazines/' . $row['cover_path'];
        }
        
        $magazinesList[] = [
            'id'          => $row['id'],
            'title'       => $row['title'],
            'description' => $row['description'],
            'dateText'    => $row['date_text'],
            'pdfUrl'      => $pdfUrl,
            'coverUrl'    => $coverUrl,
            'order'       => (int)$row['display_order']
        ];
    }
    
    echo json_encode($magazinesList, JSON_UNESCAPED_SLASHES);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error querying magazines database: ' . $e->getMessage()
    ]);
}
