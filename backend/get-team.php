<?php
/**
 * STAR FAIR - Public API to get dynamic team members
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/database.php';

try {
    $stmt = $pdo->query("SELECT `id`, `type`, `name`, `designation`, `bio`, `image_path`, `display_order` FROM `mentors` ORDER BY `type`, `display_order` ASC");
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $data = [
        'success' => true,
        'mentors' => [],
        'advisors' => [],
        'trainers' => []
    ];

    foreach ($results as $row) {
        $type = $row['type'];
        if (array_key_exists($type . 's', $data)) {
            $data[$type . 's'][] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'designation' => $row['designation'],
                'bio' => $row['bio'],
                'image_path' => $row['image_path']
            ];
        }
    }

    echo json_encode($data);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error occurred: ' . $e->getMessage()]);
}
