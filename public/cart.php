<?php
// cart.php — serves both as page and JSON API for cart actions
require_once __DIR__ . '/db.php';

// If this is an AJAX/API request, respond with JSON
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    $action = $_POST['action'];
    $token = $_POST['cart_token'] ?? '';
    $sku = $_POST['sku'] ?? '';
    $qty = max(1, (int)($_POST['qty'] ?? 1));

    try {
        if (!$token) throw new Exception('Missing cart token');

        // find or create cart
        $stmt = $pdo->prepare('SELECT id FROM carts WHERE token = ?');
        $stmt->execute([$token]);
        $cart = $stmt->fetch();
        if (!$cart) {
            $stmt = $pdo->prepare('INSERT INTO carts (token) VALUES (?)');
            $stmt->execute([$token]);
            $cart_id = $pdo->lastInsertId();
        } else {
            $cart_id = $cart['id'];
        }

        if ($action === 'add') {
            // get item
            $stmt = $pdo->prepare('SELECT id, sku, name, price, stock FROM items WHERE sku = ? FOR UPDATE');
            $pdo->beginTransaction();
            $stmt->execute([$sku]);
            $item = $stmt->fetch();
            if (!$item) throw new Exception('Item not found');
            if ($item['stock'] < $qty) {
                $pdo->rollBack();
                echo json_encode(['success' => false, 'message' => 'Insufficient stock']);
                exit;
            }
            // reserve stock (decrement immediately)
            $upd = $pdo->prepare('UPDATE items SET stock = stock - ? WHERE id = ? AND stock >= ?');
            $upd->execute([$qty, $item['id'], $qty]);
            if ($upd->rowCount() === 0) {
                $pdo->rollBack();
                echo json_encode(['success' => false, 'message' => 'Failed to reserve stock']);
                exit;
            }

            // add or update cart_items
            $stmt = $pdo->prepare('SELECT id, qty FROM cart_items WHERE cart_id = ? AND item_id = ?');
            $stmt->execute([$cart_id, $item['id']]);
            $ci = $stmt->fetch();
            if ($ci) {
                $stmt = $pdo->prepare('UPDATE cart_items SET qty = qty + ?, price = ? WHERE id = ?');
                $stmt->execute([$qty, $item['price'], $ci['id']]);
            } else {
                $stmt = $pdo->prepare('INSERT INTO cart_items (cart_id, item_id, sku, name, qty, price) VALUES (?, ?, ?, ?, ?, ?)');
                $stmt->execute([$cart_id, $item['id'], $item['sku'], $item['name'], $qty, $item['price']]);
            }

            $pdo->commit();
            // return simple cart summary
            echo json_encode(['success' => true, 'cart' => ['token' => $token], 'message' => 'Added to cart']);
            exit;
        }

        if ($action === 'view') {
            // fetch cart items
            $stmt = $pdo->prepare('SELECT id FROM carts WHERE token = ?');
            $stmt->execute([$token]);
            $c = $stmt->fetch();
            if (!$c) {
                echo json_encode(['success' => true, 'cart' => ['items' => [], 'subtotal' => 0]]);
                exit;
            }
            $cart_id = $c['id'];
            $stmt = $pdo->prepare('SELECT sku, name, qty, price FROM cart_items WHERE cart_id = ?');
            $stmt->execute([$cart_id]);
            $items = $stmt->fetchAll();
            $subtotal = 0;
            $rows = [];
            foreach ($items as $it) {
                $total = $it['qty'] * $it['price'];
                $subtotal += $total;
                $rows[] = ['sku' => $it['sku'], 'name' => $it['name'], 'qty' => (int)$it['qty'], 'price' => (float)$it['price'], 'total' => (float)$total, 'image' => '', 'category' => ''];
            }
            echo json_encode(['success' => true, 'cart' => ['items' => $rows, 'subtotal' => $subtotal]]);
            exit;
        }

    if ($action === 'update') {
      // update quantity for an item in the cart and adjust reserved stock
      $newQty = max(0, (int)($qty));
      try {
        $pdo->beginTransaction();
        // lock item row
        $stmt = $pdo->prepare('SELECT id, sku, name, price, stock FROM items WHERE sku = ? FOR UPDATE');
        $stmt->execute([$sku]);
        $item = $stmt->fetch();
        if (!$item) { $pdo->rollBack(); echo json_encode(['success' => false, 'message' => 'Item not found']); exit; }

        // find existing cart_item
        $stmt = $pdo->prepare('SELECT id, qty FROM cart_items WHERE cart_id = ? AND item_id = ?');
        $stmt->execute([$cart_id, $item['id']]);
        $ci = $stmt->fetch();
        $oldQty = $ci ? (int)$ci['qty'] : 0;
        $delta = $newQty - $oldQty;

        if ($delta > 0) {
          // need to reserve additional stock
          if ($item['stock'] < $delta) { $pdo->rollBack(); echo json_encode(['success' => false, 'message' => 'Insufficient stock to increase quantity']); exit; }
          $upd = $pdo->prepare('UPDATE items SET stock = stock - ? WHERE id = ? AND stock >= ?');
          $upd->execute([$delta, $item['id'], $delta]);
          if ($upd->rowCount() === 0) { $pdo->rollBack(); echo json_encode(['success' => false, 'message' => 'Failed to reserve stock']); exit; }
        } elseif ($delta < 0) {
          // release stock back to inventory
          $inc = $pdo->prepare('UPDATE items SET stock = stock + ? WHERE id = ?');
          $inc->execute([abs($delta), $item['id']]);
        }

        if ($newQty <= 0) {
          if ($ci) {
            $del = $pdo->prepare('DELETE FROM cart_items WHERE id = ?');
            $del->execute([$ci['id']]);
          }
        } else {
          if ($ci) {
            $u = $pdo->prepare('UPDATE cart_items SET qty = ?, price = ? WHERE id = ?');
            $u->execute([$newQty, $item['price'], $ci['id']]);
          } else {
            $ins = $pdo->prepare('INSERT INTO cart_items (cart_id, item_id, sku, name, qty, price) VALUES (?, ?, ?, ?, ?, ?)');
            $ins->execute([$cart_id, $item['id'], $item['sku'], $item['name'], $newQty, $item['price']]);
          }
        }

        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'Quantity updated']);
      } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
      }
      exit;
    }

    if ($action === 'remove') {
      // remove an item from the cart and return reserved stock
      try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare('SELECT id, sku, name FROM items WHERE sku = ? FOR UPDATE');
        $stmt->execute([$sku]);
        $item = $stmt->fetch();
        if (!$item) { $pdo->rollBack(); echo json_encode(['success' => false, 'message' => 'Item not found']); exit; }

        $stmt = $pdo->prepare('SELECT id, qty FROM cart_items WHERE cart_id = ? AND item_id = ?');
        $stmt->execute([$cart_id, $item['id']]);
        $ci = $stmt->fetch();
        if ($ci) {
          // return stock
          $ret = $pdo->prepare('UPDATE items SET stock = stock + ? WHERE id = ?');
          $ret->execute([$ci['qty'], $item['id']]);
          $del = $pdo->prepare('DELETE FROM cart_items WHERE id = ?');
          $del->execute([$ci['id']]);
        }

        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'Item removed']);
      } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
      }
      exit;
    }

        echo json_encode(['success' => false, 'message' => 'Unsupported action']);
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// Non-AJAX: render the cart page — actual items will be loaded client-side via the cart token
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Your Cart | ByteBuy</title>
  <link rel="stylesheet" href="css/styles.css" />
  <link rel="stylesheet" href="css/cart.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
  <script src="js/header.js" defer></script>
  <script src="js/cart.js" defer></script>
</head>
<body>

  <?php include 'header.php'; ?>

  <!-- HERO (keeps header scroll behavior consistent) -->
  <section class="cart-hero">
    <div class="cart-hero-overlay">
      <h1>Your Cart</h1>
      <p>Review your picks, tweak quantities, and check out securely.</p>
    </div>
  </section>

  <main class="cart-wrap">
    <div class="cart-grid">

      <!-- CART LIST (populated by js/cart.js via cart token) -->
      <section class="cart-list" id="cartList" aria-live="polite">
        <div class="loading">Loading your cart…</div>
      </section>

      <!-- SUMMARY -->
      <aside class="cart-summary" aria-labelledby="summaryTitle">
        <h2 id="summaryTitle">Order Summary</h2>

        <div class="summary-row">
          <span>Subtotal</span>
          <span id="summarySubtotal">$0.00</span>
        </div>

        <div class="summary-row hidden" id="discountRow">
          <span>Discounts</span>
          <span id="summaryDiscount">−$0.00</span>
        </div>

        <div class="summary-row">
          <span>Tax (8%)</span>
          <span id="summaryTax">$0.00</span>
        </div>

        <div class="summary-divider"></div>

        <div class="summary-row total">
          <span>Total</span>
          <span id="summaryTotal">$0.00</span>
        </div>

        <div class="summary-controls">
          <label class="toggle">
            <input type="checkbox" id="discountToggle" />
            <span>Apply Student / Business Discount (5%)</span>
          </label>

          <div class="promo">
            <input type="text" id="promoCode" placeholder="Promo code (e.g., BYTE10)" />
            <button id="applyPromo" class="apply-btn" type="button">Apply</button>
          </div>

          <div class="delivery">
            <label><input type="radio" name="delivery" value="ship" checked /> Home Delivery</label>
            <label><input type="radio" name="delivery" value="pickup" /> Store Pickup</label>
          </div>

          <a href="checkout.php" class="cta-btn dark-btn checkout-btn">Checkout</a>

          <p class="secure-note">Secure checkout • Free returns in 30 days</p>
        </div>

        <div class="helper-links">
          <a href="#" class="helper">Need a bulk quote?</a>
          <a href="#" class="helper">Get budget-friendly alternatives</a>
        </div>
      </aside>

    </div>
  </main>

  <?php include 'footer.php'; ?>

  <script>
    // Client-side: load cart using cart token and render via existing cart.js logic
    (function(){
      // dispatch a simple event so cart.js can pick up and render (cart.js should fetch cart via token)
      const token = localStorage.getItem('cart_token');
      // cart.js will do the actual fetch/render; if it listens to DOMContentLoaded it will run.
      window.dispatchEvent(new Event('cart.page.load'));
    })();
  </script>

</body>
</html>
