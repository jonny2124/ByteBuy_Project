<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Order Confirmation | ByteBuy</title>
  <link rel="stylesheet" href="css/styles.css" />
  <link rel="stylesheet" href="css/confirm.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
  <script src="js/header.js" defer></script>
  <script src="js/confirm.js" defer></script>
</head>
<body>

  <?php include 'header.php'; ?>

  <!-- HERO -->
  <section class="confirm-hero hero">
    <div class="confirm-hero-overlay">
      <div class="success-badge">✓</div>
      <h1>Your Order Has Been Placed!</h1>
      <p>Thank you for choosing ByteBuy — we’re packing your tech.</p>
    </div>
  </section>

  <main class="confirm-wrap">
    <div class="confirm-grid">

      <!-- LEFT: DETAILS + ITEMS -->
      <section class="card order-details">
        <h2>Order Details</h2>
        <div class="detail-grid">
          <div class="detail">
            <span class="label">Order ID</span>
            <span class="value" id="orderId">BB-000000</span>
          </div>
          <div class="detail">
            <span class="label">Date</span>
            <span class="value" id="orderDate">—</span>
          </div>
          <div class="detail">
            <span class="label">Estimated Delivery</span>
            <span class="value" id="deliveryEstimate">—</span>
          </div>
        </div>

        <h3 class="items-title">Items</h3>
        <div id="orderItems" class="items"></div>
      </section>

      <!-- RIGHT: SUMMARY + CTAS -->
      <aside class="card order-summary">
        <h2>Summary</h2>
        <div class="row"><span>Subtotal</span><span id="confSubtotal">$0.00</span></div>
        <div class="row hidden" id="confDiscountRow"><span>Discounts</span><span id="confDiscount">−$0.00</span></div>
        <div class="row"><span>Tax (8%)</span><span id="confTax">$0.00</span></div>
        <div class="row"><span>Shipping</span><span id="confShipping">$0.00</span></div>
        <div class="divider"></div>
        <div class="row total"><span>Total</span><span id="confTotal">$0.00</span></div>

        <div class="cta-group">
          <a href="shop.php" class="cta-btn dark-btn">Continue Shopping</a>
          <a href="#" class="cta-btn track-btn">Track Order</a>
        </div>

        <div class="thankyou">
          <h3>Thank you!</h3>
          <p>We’re a small, dedicated team making reliable tech accessible for everyone — from students and young creators to busy parents and practical professionals. If you need help, reply to your confirmation email or reach out anytime.</p>
        </div>
      </aside>

    </div>
  </main>

  <?php include 'footer.php'; ?>
<?php
// If order code is present, fetch order details and expose them to the client via a JS variable.
if (!empty($_GET['code'])) {
  require_once __DIR__ . '/db.php';
  $code = $_GET['code'];
  // Try to fetch the order by order_code
  $stmt = $pdo->prepare('SELECT id, order_code, created_at, total, discount_total, coupon_code FROM orders WHERE order_code = ? LIMIT 1');
  $stmt->execute([$code]);
  $order = $stmt->fetch();
  if ($order) {
    // fetch items
    $stmt2 = $pdo->prepare('SELECT oi.sku, oi.name, oi.quantity AS qty, oi.price, IFNULL(i.image, "") AS image FROM order_items oi LEFT JOIN items i ON oi.item_id = i.id WHERE oi.order_id = ?');
    $stmt2->execute([$order['id']]);
    $items = $stmt2->fetchAll();
    // prepare a safe JSON payload
    $subtotal = 0.0;
    $payload = [
      'code' => $order['order_code'],
      'created_at' => $order['created_at'],
      'subtotal' => 0.0,
      'discount' => isset($order['discount_total']) ? (float)$order['discount_total'] : 0.0,
      'total' => (float)$order['total'],
      'coupon_code' => $order['coupon_code'] ?? null,
      'items' => []
    ];
    foreach ($items as $it) {
      $lineTotal = (int)$it['qty'] * (float)$it['price'];
      $subtotal += $lineTotal;
      $payload['items'][] = [
        'name' => $it['name'],
        'qty' => (int)$it['qty'],
        'price' => (float)$it['price'],
        'total' => $lineTotal,
        'image' => $it['image'] ?: 'assets/home/placeholder.png',
        'category' => ''
      ];
    }
    $payload['subtotal'] = $subtotal;
    $json = json_encode($payload, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
    // store last order snapshot and expose data
    echo "<script>try{ localStorage.setItem('bytebuy_last_order', JSON.stringify({id:'" . addslashes($order['order_code']) . "', date:'" . date('c') . "'})); }catch(e){}; window.__ORDER_DATA = {$json};</script>";
  }
}
?>

</body>
</html>

