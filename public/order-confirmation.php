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

</body>
</html>

