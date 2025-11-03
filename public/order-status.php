<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Order Status | ByteBuy</title>
  <link rel="stylesheet" href="css/styles.css" />
  <link rel="stylesheet" href="css/status.css" />
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
          <button type="submit" class="cta-btn dark-btn">Check Status</button>
          <p class="muted hint">Enter your Order ID or email — either works.</p>
        </form>
      </section>

      <section class="card progress-card">
        <h2>Order Progress</h2>
        <div class="meta-row">
          <div><span class="label">Order</span> <strong id="metaOrder">—</strong></div>
          <div><span class="label">Placed</span> <strong id="metaDate">—</strong></div>
          <div><span class="label">ETA</span> <strong id="metaEta">—</strong></div>
        </div>

        <ol class="tracker" id="tracker">
          <li class="step" data-step="ordered">
            <span class="dot"></span>
            <div class="step-info">
              <strong>Ordered</strong>
              <span class="when" id="whenOrdered">—</span>
            </div>
          </li>
          <li class="step" data-step="packed">
            <span class="dot"></span>
            <div class="step-info">
              <strong>Packed</strong>
              <span class="when" id="whenPacked">—</span>
            </div>
          </li>
          <li class="step" data-step="shipped">
            <span class="dot"></span>
            <div class="step-info">
              <strong>Shipped</strong>
              <span class="when" id="whenShipped">—</span>
            </div>
          </li>
          <li class="step" data-step="delivered">
            <span class="dot"></span>
            <div class="step-info">
              <strong>Delivered</strong>
              <span class="when" id="whenDelivered">—</span>
            </div>
          </li>
        </ol>
      </section>

      <!-- RIGHT: ITEMS SUMMARY -->
      <aside class="card items-card">
        <h2>Items</h2>
        <div id="statusItems" class="items"></div>
      </aside>

    </div>
  </main>

  <?php include 'footer.php'; ?>

</body>
</html>

