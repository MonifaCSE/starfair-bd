<?php
/**
 * STAR FAIR - Admin Registrations Listing API
 * Returns all student registrations as JSON.
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
    // Retrieve registrations newest first
    $stmt = $pdo->query("SELECT `id`, `name`, `mobile`, `email`, `gender`, `programmes`, `created_at` FROM `registrations` ORDER BY `created_at` DESC");
    $registrations = $stmt->fetchAll();
    
    echo json_encode($registrations, JSON_UNESCAPED_SLASHES);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
