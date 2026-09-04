<?php
/**
 * STAR FAIR - Public API to get news articles
 */

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/database.php';

try {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if ($id) {
        // Fetch single news article
        $stmt = $pdo->prepare("SELECT `id`, `title`, `category`, `summary`, `content`, `image_path`, `is_featured`, `published_date` FROM `news` WHERE `id` = ?");
        $stmt->execute([$id]);
        $article = $stmt->fetch();

        if ($article) {
            echo json_encode([
                'success' => true,
                'article' => $article
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Article not found.'
            ]);
        }
    } else {
        // Fetch all news articles
        $stmt = $pdo->query("SELECT `id`, `title`, `category`, `summary`, `image_path`, `is_featured`, `published_date` FROM `news` ORDER BY `display_order` ASC, `published_date` DESC, `created_at` DESC");
        $news = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'news' => $news
        ]);
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred: ' . $e->getMessage()
    ]);
}
