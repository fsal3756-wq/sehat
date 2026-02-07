<?php
require 'config.php';

header('Content-Type: application/json');

$keyword = $_GET['keyword'] ?? '';

if (strlen($keyword) >= 2) {
    $stmt = $pdo->prepare("SELECT id, name, calories, proteins, fat, carbohydrate, image 
                           FROM makanan_dataset 
                           WHERE name LIKE ? 
                           ORDER BY 
                               CASE 
                                   WHEN name LIKE ? THEN 1
                                   WHEN name LIKE ? THEN 2
                                   ELSE 3
                               END,
                               name
                           LIMIT 15");
    
    // Prioritaskan yang mulai dengan keyword
    $stmt->execute([
        '%' . $keyword . '%',
        $keyword . '%',
        '% ' . $keyword . '%'
    ]);
    
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($results);
} else {
    echo json_encode([]);
}