<?php
// public/checkout_process.php
require_once __DIR__ . '/db.php';

// This checkout processor expects a cart_token POSTed from the client (no PHP sessions)
$cart_token = $_POST['cart_token'] ?? '';
$guest_email = trim($_POST['guest_email'] ?? '');
$shipping_address = $_POST['shipping_address'] ?? '';
$billing_address = $_POST['billing_address'] ?? '';
$payment_method = $_POST['payment_method'] ?? 'cod';

if (!$cart_token) {
    die('Missing cart token');
}

try {
    $pdo->beginTransaction();

    // find cart and items
    $stmt = $pdo->prepare('SELECT id FROM carts WHERE token = ?');
    $stmt->execute([$cart_token]);
    $c = $stmt->fetch();
    if (!$c) throw new Exception('Cart not found');
    $cart_id = $c['id'];

    $stmt = $pdo->prepare('SELECT item_id, sku, name, qty, price FROM cart_items WHERE cart_id = ?');
    $stmt->execute([$cart_id]);
    $items = $stmt->fetchAll();
    if (!$items) throw new Exception('Cart is empty');

    // compute total (items already reserved when added to cart)
    $total = 0;
    foreach ($items as $it) {
        $total += $it['qty'] * $it['price'];
    }

    // generate a unique order code (BB-123456) and create order (guest checkout: user_id NULL)
    $maxAttempts = 6;
    $order_code = null;
    for ($i = 0; $i < $maxAttempts; $i++) {
        $candidate = 'BB-' . random_int(100000, 999999);
        $stmtCheck = $pdo->prepare('SELECT id FROM orders WHERE order_code = ?');
        $stmtCheck->execute([$candidate]);
        if (!$stmtCheck->fetch()) { $order_code = $candidate; break; }
    }
    if (!$order_code) {
        // fallback using timestamp
        $order_code = 'BB-' . time();
    }

    $stmt = $pdo->prepare('INSERT INTO orders (order_code, user_id, guest_email, total, shipping_address, billing_address, payment_method, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
    $status = 'pending';
    $stmt->execute([$order_code, null, $guest_email, $total, $shipping_address, $billing_address, $payment_method, $status]);
    $order_id = $pdo->lastInsertId();

    // move cart items to order_items
    $stmtInsertLine = $pdo->prepare('INSERT INTO order_items (order_id, item_id, sku, name, quantity, price) VALUES (?, ?, ?, ?, ?, ?)');
    foreach ($items as $it) {
        $stmtInsertLine->execute([$order_id, $it['item_id'], $it['sku'], $it['name'], $it['qty'], $it['price']]);
    }

    // record order event
    $stmtEvent = $pdo->prepare('INSERT INTO order_events (order_id, event_type, event_note) VALUES (?, ?, ?)');
    $stmtEvent->execute([$order_id, 'created', 'Order created by checkout_process']);

    // clear cart rows
    $stmt = $pdo->prepare('DELETE FROM cart_items WHERE cart_id = ?');
    $stmt->execute([$cart_id]);
    $stmt = $pdo->prepare('DELETE FROM carts WHERE id = ?');
    $stmt->execute([$cart_id]);

    $pdo->commit();

    // redirect to confirmation with the human-friendly code
    header('Location: order-confirmation.php?code=' . urlencode($order_code));
    exit;
} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    echo 'Checkout failed: ' . htmlspecialchars($e->getMessage());
}
