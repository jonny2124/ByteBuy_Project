<?php
require_once 'db.php';

header('Content-Type: application/json');

if (!isset($_GET['sku'])) {
    http_response_code(400);
    echo json_encode(['error' => 'SKU parameter is required']);
    exit;
}

$sku = $_GET['sku'];

try {
    $stmt = $pdo->prepare('SELECT stock FROM items WHERE sku = ?');
    $stmt->execute([$sku]);
    $result = $stmt->fetch();
    
    if ($result === false) {
        http_response_code(404);
        echo json_encode(['error' => 'Product not found']);
        exit;
    }
    
    echo json_encode(['stock' => (int)$result['stock']]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}
?>