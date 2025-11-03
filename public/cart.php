<!DOCTYPE html>
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

      <!-- CART LIST -->
      <section class="cart-list" id="cartList" aria-live="polite">

        <!-- Item 1 -->
        <article class="cart-item" data-price="899.00">
          <div class="item-media">
            <img src="assets/home/deal2.jpeg" alt="MacBook Air M2"/>
          </div>
          <div class="item-info">
            <div class="item-head">
              <h3 class="item-name">MacBook Air M2</h3>
              <span class="item-category">Laptops</span>
            </div>
            <p class="item-price">$<span class="unit-price">899.00</span></p>
            <label class="addon">
              <input type="checkbox" class="warranty" data-warranty="89.00" />
              <span>Add 2‑Year Protection (+$89)</span>
            </label>
            <div class="item-actions">
              <div class="qty">
                <button class="btn-qty minus" aria-label="Decrease quantity">−</button>
                <input type="number" class="qty-input" value="1" min="1"/>
                <button class="btn-qty plus" aria-label="Increase quantity">+</button>
              </div>
              <div class="item-total-wrap">
                <span class="item-total-label">Total</span>
                <span class="item-total">$899.00</span>
              </div>
            </div>
            <div class="item-links">
              <button class="link save-later" type="button">Save for later</button>
              <button class="link remove" type="button">Remove</button>
            </div>
          </div>
        </article>

        <!-- Item 2 -->
        <article class="cart-item" data-price="399.00">
          <div class="item-media">
            <img src="assets/home/deal3.jpg" alt="Sony WH-1000XM5"/>
          </div>
          <div class="item-info">
            <div class="item-head">
              <h3 class="item-name">Sony WH‑1000XM5</h3>
              <span class="item-category">Audio</span>
            </div>
            <p class="item-price">$<span class="unit-price">399.00</span></p>
            <label class="addon">
              <input type="checkbox" class="warranty" data-warranty="29.00" />
              <span>Add 2‑Year Protection (+$29)</span>
            </label>
            <div class="item-actions">
              <div class="qty">
                <button class="btn-qty minus" aria-label="Decrease quantity">−</button>
                <input type="number" class="qty-input" value="1" min="1"/>
                <button class="btn-qty plus" aria-label="Increase quantity">+</button>
              </div>
              <div class="item-total-wrap">
                <span class="item-total-label">Total</span>
                <span class="item-total">$399.00</span>
              </div>
            </div>
            <div class="item-links">
              <button class="link save-later" type="button">Save for later</button>
              <button class="link remove" type="button">Remove</button>
            </div>
          </div>
        </article>

        <!-- Persona helper strip -->
        <div class="persona-strip">
          <div class="persona-chip" title="Small Business Buyer / Freelancer">Bulk quote available</div>
          <div class="persona-chip" title="Budget-Conscious Parent">Budget alternatives</div>
          <div class="persona-chip" title="Melissa Ong – Practical Professional">Care plan options</div>
          <div class="persona-chip" title="Ryan Lee – Young Tech Enthusiast">Upgrade suggestions</div>
        </div>

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

</body>
</html>
