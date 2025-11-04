<?php
// public/checkout_process.php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/../lib/mailer.php';

// This checkout processor expects a cart_token POSTed from the client (no PHP sessions)
$cart_token = $_POST['cart_token'] ?? '';
$guest_email = trim($_POST['guest_email'] ?? '');
$shipping_address = $_POST['shipping_address'] ?? '';
$billing_address = $_POST['billing_address'] ?? '';
$payment_method = $_POST['payment_method'] ?? 'cod';
$coupon_code = strtoupper(trim($_POST['coupon_code'] ?? ''));

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

    // compute subtotal (items already reserved when added to cart)
    $subtotal = 0;
    foreach ($items as $it) {
        $subtotal += $it['qty'] * $it['price'];
    }

    $discount_total = 0.00;
    $applied_coupon = null;
    if ($coupon_code !== '') {
        $stmtCoupon = $pdo->prepare('SELECT code, type, value, active, min_subtotal, max_uses, uses, expires_at FROM coupons WHERE code = ? FOR UPDATE');
        $stmtCoupon->execute([$coupon_code]);
        $coupon = $stmtCoupon->fetch();

        if (!$coupon) {
            throw new Exception('Invalid coupon code');
        }
        if ((int)$coupon['active'] !== 1) {
            throw new Exception('Coupon is inactive');
        }
        if ($coupon['expires_at'] !== null && $coupon['expires_at'] !== '' && strtotime($coupon['expires_at']) < time()) {
            throw new Exception('Coupon has expired');
        }
        if ($coupon['min_subtotal'] !== null && $coupon['min_subtotal'] !== '' && $subtotal < (float)$coupon['min_subtotal']) {
            throw new Exception('Order total does not meet coupon minimum');
        }
        if ($coupon['max_uses'] !== null && $coupon['max_uses'] !== '' && (int)$coupon['max_uses'] > 0 && (int)$coupon['uses'] >= (int)$coupon['max_uses']) {
            throw new Exception('Coupon usage limit reached');
        }

        switch ($coupon['type']) {
            case 'percent':
                $percent = max(0, min(100, (float)$coupon['value']));
                $discount_total = round($subtotal * ($percent / 100), 2);
                break;
            case 'fixed':
                $discount_total = round(min((float)$coupon['value'], $subtotal), 2);
                break;
            default:
                throw new Exception('Unsupported coupon type');
        }

        if ($discount_total <= 0) {
            throw new Exception('Coupon does not provide a discount');
        }

        $applied_coupon = $coupon['code'];

        $stmtUpdateCoupon = $pdo->prepare('UPDATE coupons SET uses = uses + 1 WHERE code = ?');
        $stmtUpdateCoupon->execute([$coupon['code']]);
    }

    $total = max(0, $subtotal - $discount_total);
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

    $status = 'pending';
    $stmt = $pdo->prepare('INSERT INTO orders (order_code, user_id, guest_email, total, discount_total, coupon_code, shipping_address, billing_address, payment_method, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([
        $order_code,
        null,
        $guest_email,
        $total,
        $discount_total,
        $applied_coupon,
        $shipping_address,
        $billing_address,
        $payment_method,
        $status
    ]);
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
    if ($guest_email) {
        try {
            bytebuy_mailer_send_order_confirmation($pdo, (int)$order_id, $guest_email);
        } catch (Throwable $mailError) {
            error_log('Order email error: ' . $mailError->getMessage());
        }
    }

    // redirect to confirmation with the human-friendly code
    header('Location: order-confirmation.php?code=' . urlencode($order_code));
    exit;
} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    error_log('Checkout error: ' . $e->getMessage());
    
    // Show a user-friendly error page
    ?><!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Checkout Error | ByteBuy</title>
        <link rel="stylesheet" href="css/styles.css">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    </head>
    <body>
        <?php include 'header.php'; ?>
        
        <main style="max-width: 800px; margin: 2rem auto; padding: 2rem;">
            <h1>Checkout Error</h1>
            <p>We couldn't complete your order. The error has been logged.</p>
            <p class="error-details" style="color: #dc2626; margin: 1rem 0;">
                <?= htmlspecialchars($e->getMessage()) ?>
            </p>
            <div style="margin-top: 2rem;">
                <a href="cart.php" class="cta-btn">Return to Cart</a>
                <a href="checkout.php" class="cta-btn dark-btn">Try Again</a>
            </div>
        </main>

        <?php include 'footer.php'; ?>
    </body>
    </html>
    <?php
    exit;
}
