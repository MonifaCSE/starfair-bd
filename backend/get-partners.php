<?php
/**
 * STAR FAIR - Public API to get official collaboration partners
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/database.php';

try {
    $stmt = $pdo->query("SELECT `id`, `name`, `image_path`, `display_order` FROM `partners` ORDER BY `display_order` ASC, `created_at` DESC");
    $partners = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'partners' => $partners
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred: ' . $e->getMessage()
    ]);
}
