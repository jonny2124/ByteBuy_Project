<?php
// public/products.php - JSON API for product catalog sourced from DB
// Supports optional filters via GET: q, category, sort (price_asc|price_desc|name_asc|newest)

require_once __DIR__ . '/db.php';
header('Content-Type: application/json');

function map_category_from_sku($sku){
    $p = substr($sku, 0, 2);
    switch ($p) {
        case 'la': return 'Laptops';
        case 'ph': return 'Smartphones';
        case 'au': return 'Audio';
        case 'st': return 'Storage';
        case 'ac': return 'Accessories';
        default: return 'Other';
    }
}

try {
    $q = trim($_GET['q'] ?? '');
    $cat = trim($_GET['category'] ?? '');
    $sort = trim($_GET['sort'] ?? '');

    $where = ['stock > 0'];
    $params = [];

    if ($q !== '') {
        $where[] = '(name LIKE ? OR description LIKE ?)';
        $like = '%' . $q . '%';
        $params[] = $like; $params[] = $like;
    }

    if ($cat !== '') {
        $map = [
            'Laptops' => 'lap%',
            'Smartphones' => 'ph%',
            'Audio' => 'au%',
            'Storage' => 'st%',
            'Accessories' => 'ac%'
        ];
        if (isset($map[$cat])) {
            $where[] = 'sku LIKE ?';
            $params[] = $map[$cat];
        }
    }

    $order = 'created_at DESC';
    if ($sort === 'price_asc') $order = 'price ASC';
    elseif ($sort === 'price_desc') $order = 'price DESC';
    elseif ($sort === 'name_asc') $order = 'name ASC';

    $sql = 'SELECT sku, name, price, image FROM items';
    if ($where) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }
    $sql .= ' ORDER BY ' . $order . ' LIMIT 500';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();

    $items = [];
    foreach ($rows as $r) {
        $items[] = [
            'id' => $r['sku'],
            'name' => $r['name'],
            'category' => map_category_from_sku($r['sku']),
            'price' => (float)$r['price'],
            'rating' => 4.5,
            'img' => $r['image'] ?: 'assets/home/placeholder.png',
        ];
    }

    echo json_encode(['success' => true, 'items' => $items]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

