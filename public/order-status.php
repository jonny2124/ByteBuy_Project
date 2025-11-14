<?php
function bytebuy_order_status_fetch(PDO $pdo, string $query, array $params): ?array
{
  $sql = 'SELECT o.*, u.email AS user_email, u.full_name AS user_name
          FROM orders o
          LEFT JOIN users u ON o.user_id = u.id
          WHERE ' . $query . ' LIMIT 1';
  $stmt = $pdo->prepare($sql);
  $stmt->execute($params);
  $row = $stmt->fetch();
  return $row ?: null;
}

function bytebuy_order_status_lookup(PDO $pdo, string $orderRef, string $email = '', bool $allowEmailLookup = false): array
{
  $order = null;
  if ($orderRef !== '') {
    $order = bytebuy_order_status_fetch($pdo, 'o.order_code = ?', [$orderRef]);
    if (!$order && preg_match('/(\d+)/', $orderRef, $m)) {
      $order = bytebuy_order_status_fetch($pdo, 'o.id = ?', [$m[1]]);
    }
  } elseif ($allowEmailLookup && $email !== '') {
    $stmt = $pdo->prepare('SELECT o.*, u.email AS user_email, u.full_name AS user_name
                           FROM orders o
                           LEFT JOIN users u ON o.user_id = u.id
                           WHERE o.guest_email = ?
                           ORDER BY o.created_at DESC LIMIT 1');
    $stmt->execute([$email]);
    $order = $stmt->fetch();
    if (!$order) {
      $stmt = $pdo->prepare('SELECT o.*, u.email AS user_email, u.full_name AS user_name
                             FROM orders o
                             LEFT JOIN users u ON o.user_id = u.id
                             WHERE u.email = ?
                             ORDER BY o.created_at DESC LIMIT 1');
      $stmt->execute([$email]);
      $order = $stmt->fetch();
    }
  }

  if (!$order) {
    throw new Exception('Order not found');
  }

  return $order;
}

function bytebuy_order_status_lookup_by_id(PDO $pdo, int $orderId): array
{
  $order = bytebuy_order_status_fetch($pdo, 'o.id = ?', [$orderId]);
  if (!$order) {
    throw new Exception('Order not found');
  }
  return $order;
}

function bytebuy_order_status_payload(PDO $pdo, array $order): array
{
  $stmt = $pdo->prepare('SELECT oi.item_id, oi.sku, oi.name, oi.quantity, oi.price, IFNULL(i.image, "") AS image
                         FROM order_items oi
                         LEFT JOIN items i ON oi.item_id = i.id
                         WHERE oi.order_id = ?');
  $stmt->execute([$order['id']]);
  $items = $stmt->fetchAll();

  $subtotal = 0.0;
  foreach ($items as &$it) {
    $it['quantity'] = (int)$it['quantity'];
    $it['price'] = (float)$it['price'];
    $it['total'] = $it['quantity'] * $it['price'];
    if (!$it['image']) {
      $it['image'] = 'assets/home/placeholder.png';
    }
    $subtotal += $it['total'];
  }
  unset($it);

  $discount = isset($order['discount_total']) ? (float)$order['discount_total'] : 0.0;
  $total = isset($order['total']) ? (float)$order['total'] : max(0.0, $subtotal - $discount);

  return [
    'id' => (int)$order['id'],
    'code' => $order['order_code'] ?? null,
    'created_at' => $order['created_at'] ?? null,
    'status' => $order['status'] ?? null,
    'guest_email' => $order['guest_email'] ?? null,
    'shipping_address' => $order['shipping_address'] ?? '',
    'billing_address' => $order['billing_address'] ?? '',
    'payment_method' => $order['payment_method'] ?? '',
    'items' => $items,
    'subtotal' => $subtotal,
    'discount' => $discount,
    'total' => $total,
    'coupon_code' => $order['coupon_code'] ?? null,
  ];
}

function bytebuy_order_status_verify_email(array $order, string $email): void
{
  $expected = $order['guest_email'] ?: ($order['user_email'] ?? '');
  if ($expected && $email === '') {
    throw new Exception('Enter the email associated with this order to continue.');
  }
  if ($expected && strcasecmp($expected, $email) !== 0) {
    throw new Exception('Email does not match this order.');
  }
}

function bytebuy_order_status_log(PDO $pdo, int $orderId, string $type, string $note = ''): void
{
  try {
    $stmt = $pdo->prepare('INSERT INTO order_events (order_id, event_type, event_note) VALUES (?, ?, ?)');
    $stmt->execute([$orderId, $type, $note]);
  } catch (Throwable $e) {
    error_log('Order event log failed: ' . $e->getMessage());
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
  require_once __DIR__ . '/db.php';
  header('Content-Type: application/json');

  $action = $_POST['action'];
  try {
    switch ($action) {
      case 'find':
        $orderIdRaw = trim($_POST['orderId'] ?? $_POST['order'] ?? '');
        $email = trim($_POST['email'] ?? '');
        if ($orderIdRaw === '' && $email === '') {
          throw new Exception('Enter your Order ID or email.');
        }
        $order = bytebuy_order_status_lookup($pdo, $orderIdRaw, $email, true);
        if ($email !== '') {
          bytebuy_order_status_verify_email($order, $email);
        }
        $payload = bytebuy_order_status_payload($pdo, $order);
        echo json_encode(['success' => true, 'order' => $payload]);
        break;

      case 'cancel':
        $orderRef = trim($_POST['orderCode'] ?? $_POST['orderId'] ?? '');
        $email = trim($_POST['email'] ?? '');
        if ($orderRef === '') {
          throw new Exception('Order reference is required.');
        }
        $order = bytebuy_order_status_lookup($pdo, $orderRef);
        bytebuy_order_status_verify_email($order, $email);
        $createdAt = $order['created_at'] ? new DateTime($order['created_at']) : null;
        $ageHours = $createdAt ? (time() - $createdAt->getTimestamp()) / 3600 : PHP_INT_MAX;
        $status = strtolower($order['status'] ?? '');
        if ($ageHours > 24) {
          throw new Exception('Cancellations or refund requests are available within 24 hours of purchase.');
        }
        if (in_array($status, ['shipped', 'delivered', 'cancelled', 'canceled', 'refunded'], true)) {
          throw new Exception('This order can no longer be cancelled online.');
        }
        $pdo->prepare('UPDATE orders SET status = ? WHERE id = ?')->execute(['cancelled', $order['id']]);
        bytebuy_order_status_log($pdo, (int)$order['id'], 'cancel_requested', 'Customer requested cancellation/refund via order-status page.');
        $freshOrder = bytebuy_order_status_lookup_by_id($pdo, (int)$order['id']);
        echo json_encode([
          'success' => true,
          'message' => 'Cancellation request received. Our team will review and confirm via email.',
          'order' => bytebuy_order_status_payload($pdo, $freshOrder),
        ]);
        break;

      case 'update_address':
        $orderRef = trim($_POST['orderCode'] ?? $_POST['orderId'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $address = trim($_POST['address'] ?? '');
        if ($orderRef === '') {
          throw new Exception('Order reference is required.');
        }
        if (strlen($address) < 10) {
          throw new Exception('Please provide a full delivery address.');
        }
        $order = bytebuy_order_status_lookup($pdo, $orderRef);
        bytebuy_order_status_verify_email($order, $email);
        $createdAt = $order['created_at'] ? new DateTime($order['created_at']) : null;
        $ageDays = $createdAt ? (time() - $createdAt->getTimestamp()) / 86400 : PHP_INT_MAX;
        $status = strtolower($order['status'] ?? '');
        if ($ageDays > 3) {
          throw new Exception('Delivery addresses can be changed within 3 days of purchase.');
        }
        if (in_array($status, ['shipped', 'delivered', 'cancelled', 'canceled'], true)) {
          throw new Exception('Orders that have shipped or closed cannot be updated.');
        }
        $cleanAddress = preg_replace("/[\r\f\v]+/", "\n", $address);
        $cleanAddress = trim(preg_replace("/\n{3,}/", "\n\n", $cleanAddress));
        $pdo->prepare('UPDATE orders SET shipping_address = ?, updated_at = NOW() WHERE id = ?')->execute([$cleanAddress, $order['id']]);
        bytebuy_order_status_log($pdo, (int)$order['id'], 'shipping_updated', 'Customer updated delivery address via order-status page.');

        $freshOrder = bytebuy_order_status_lookup_by_id($pdo, (int)$order['id']);
        echo json_encode([
          'success' => true,
          'message' => 'Delivery address updated successfully.',
          'order' => bytebuy_order_status_payload($pdo, $freshOrder),
        ]);
        break;

      case 'change_payment':
        $orderRef = trim($_POST['orderCode'] ?? $_POST['orderId'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $newMethod = strtolower(trim($_POST['payment_method'] ?? ''));
        if ($orderRef === '') {
          throw new Exception('Order reference is required.');
        }
        $allowedMethods = ['card', 'paynow'];
        if (!in_array($newMethod, $allowedMethods, true)) {
          throw new Exception('Choose a valid payment method.');
        }
        $order = bytebuy_order_status_lookup($pdo, $orderRef);
        bytebuy_order_status_verify_email($order, $email);
        $status = strtolower($order['status'] ?? '');
        if (in_array($status, ['shipped', 'delivered', 'cancelled', 'canceled'], true)) {
          throw new Exception('Payment method can no longer be changed for this order.');
        }
        if ($order['payment_method'] === $newMethod) {
          throw new Exception('This payment method is already on file.');
        }
        $pdo->prepare('UPDATE orders SET payment_method = ?, status = ? WHERE id = ?')->execute([$newMethod, 'pending_verification', $order['id']]);
        bytebuy_order_status_log(
          $pdo,
          (int)$order['id'],
          'payment_updated',
          sprintf('Customer changed payment method to %s. Status set to pending verification via order-status page.', strtoupper($newMethod))
        );
        $freshOrder = bytebuy_order_status_lookup_by_id($pdo, (int)$order['id']);
        echo json_encode([
          'success' => true,
          'message' => 'Payment method updated. Status changed to Pending Verification.',
          'order' => bytebuy_order_status_payload($pdo, $freshOrder),
        ]);
        break;

      default:
        throw new Exception('Unsupported action');
    }
  } catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
  }
  exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Order Status | ByteBuy</title>
  <link rel="stylesheet" href="css/styles.css" />
  <link rel="stylesheet" href="css/status.css" />
  <link rel="icon" type="image/png" href="assets/Favicon.png">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
  <script src="js/header.js" defer></script>
  <script src="js/status.js" defer></script>
</head>
<body>

  <?php include 'header.php'; ?>

  <!-- HERO -->
  <section class="status-hero hero">
    <div class="status-hero-overlay">
      <h1>Track Your Order</h1>
      <p>Real-time updates from checkout to your doorstep.</p>
    </div>
  </section>

  <main class="status-wrap">
    <div class="status-grid">

      <!-- LEFT: SEARCH + PROGRESS -->
      <section class="card finder">
        <h2>Find Your Order</h2>
        <form id="statusForm" class="finder-form" novalidate>
          <div class="grid-2">
            <div class="field">
              <label for="orderId">Order ID</label>
              <input id="orderId" name="orderId" type="text" placeholder="BB-123456" />
            </div>
            <div class="field">
              <label for="email">Email</label>
              <input id="email" name="email" type="email" placeholder="you@example.com" />
            </div>
          </div>
          <div class="finder-actions">
            <button type="submit" class="cta-btn dark-btn">Check Status</button>
          <p class="muted hint">Enter your Order ID or email; either works.</p>
          </div>
        </form>
      </section>

      <section class="card progress-card">
        <h2>Order Progress</h2>
        <div class="meta-row">
          <div><span class="label">Order</span> <strong id="metaOrder">-</strong></div>
          <div><span class="label">Placed</span> <strong id="metaDate">-</strong></div>
          <div><span class="label">ETA</span> <strong id="metaEta">-</strong></div>
          <div><span class="label">Status</span> <strong id="metaStatus">-</strong></div>
        </div>

        <ol class="tracker" id="tracker">
          <li class="step" data-step="ordered">
            <span class="dot"></span>
            <div class="step-info">
              <strong>Ordered</strong>
              <span class="when" id="whenOrdered">-</span>
            </div>
          </li>
          <li class="step" data-step="packed">
            <span class="dot"></span>
            <div class="step-info">
              <strong>Packed</strong>
              <span class="when" id="whenPacked">-</span>
            </div>
          </li>
          <li class="step" data-step="shipped">
            <span class="dot"></span>
            <div class="step-info">
              <strong>Shipped</strong>
              <span class="when" id="whenShipped">-</span>
            </div>
          </li>
          <li class="step" data-step="delivered">
            <span class="dot"></span>
            <div class="step-info">
              <strong>Delivered</strong>
              <span class="when" id="whenDelivered">-</span>
            </div>
          </li>
        </ol>
      </section>

      <!-- RIGHT: ITEMS SUMMARY -->
      <aside class="card items-card">
        <h2>Items</h2>
        <div id="statusItems" class="items"></div>
      </aside>

      <section class="card post-sales" id="postSales">
        <div class="post-sales-head">
          <h2>Post-Sales Care</h2>
          <p class="muted">Manage cancellations, delivery updates, and payment changes after checkout.</p>
        </div>
        <div class="verify-field">
          <label for="actionEmail">Email on order <span>(required to make changes)</span></label>
          <input type="email" id="actionEmail" placeholder="you@example.com" autocomplete="email">
        </div>
        <div id="actionFeedback" class="action-feedback" aria-live="polite"></div>
        <div class="action-grid">
          <div class="action-block" id="cancelBlock">
            <div class="action-info">
              <h3>Cancel / Refund Order</h3>
              <p>Request an instant cancellation or refund review. Available within 24 hours unless shipped.</p>
            </div>
            <ul class="action-details">
              <li>Full refunds issued automatically for unshipped orders.</li>
              <li>Shipped orders move to manual support review.</li>
            </ul>
            <div class="action-controls">
              <button type="button" class="cta-btn action-btn" id="cancelOrderBtn" disabled>Request Cancellation</button>
              <p class="action-hint" id="cancelHint">Lookup your order to enable this action.</p>
            </div>
          </div>

          <div class="action-block" id="addressBlock">
            <div class="action-info">
              <h3>Change Delivery Address</h3>
              <p>Edit your delivery address within 3 days of purchase.</p>
            </div>
            <div class="current-value" id="currentAddress">No address on file.</div>
            <form id="addressForm" class="action-form">
              <label for="newAddress">New delivery address</label>
              <textarea id="newAddress" rows="3" placeholder="Street, unit, city, postal code" required></textarea>
              <button type="submit" class="cta-btn secondary-btn" disabled>Update Address</button>
            </form>
            <p class="action-hint" id="addressHint">Lookup your order to edit the delivery address.</p>
          </div>

          <div class="action-block" id="paymentBlock">
            <div class="action-info">
              <h3>Change Payment Method</h3>
              <p>Switch payment before shipment. Updates set status to Pending Verification.</p>
            </div>
            <div class="current-value" id="currentPayment">-</div>
            <div class="action-controls">
              <form id="paymentForm" class="action-form">
                <label for="paymentMethod">New payment method</label>
                <select id="paymentMethod" required>
                  <option value="card">Credit / Debit Card</option>
                  <option value="paynow">PayNow</option>
                </select>
                <button type="submit" class="cta-btn secondary-btn" disabled>Update Payment</button>
              </form>
              <p class="action-hint" id="paymentHint">Lookup your order to switch payment method.</p>
            </div>
          </div>
        </div>
      </section>

    </div>
  </main>

  <?php include 'footer.php'; ?>

</body>
</html>

