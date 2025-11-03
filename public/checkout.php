<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Checkout | ByteBuy</title>
  <link rel="stylesheet" href="css/styles.css">
  <link rel="stylesheet" href="css/checkout.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
  <script src="js/header.js" defer></script>
  <script src="js/checkout.js" defer></script>
</head>
<body>

  <?php include 'header.php'; ?>

  <!-- HERO to keep header behavior consistent -->
  <section class="checkout-hero hero">
    <div class="checkout-hero-overlay">
      <h1>Checkout</h1>
      <p>Secure payment, fast delivery, and helpful support.</p>
    </div>
  </section>

  <main class="checkout-wrap">
    <div class="checkout-grid">

      <!-- LEFT: FORMS -->
      <section class="checkout-forms">
        <!-- Billing Details -->
        <div class="card" id="billing">
          <h2>Billing Details</h2>
          <div class="grid-2">
            <div class="field">
              <label for="firstName">First Name</label>
              <input id="firstName" name="firstName" type="text" placeholder="Jane" required>
            </div>
            <div class="field">
              <label for="lastName">Last Name</label>
              <input id="lastName" name="lastName" type="text" placeholder="Doe" required>
            </div>
          </div>
          <div class="grid-2">
            <div class="field">
              <label for="email">Email</label>
              <input id="email" name="email" type="email" placeholder="you@example.com" required>
            </div>
            <div class="field">
              <label for="phone">Phone</label>
              <input id="phone" name="phone" type="tel" placeholder="(555) 123-4567" required>
            </div>
          </div>
          <div class="field">
            <label for="address1">Address Line 1</label>
            <input id="address1" name="address1" type="text" placeholder="123 Byte Lane" required>
          </div>
          <div class="field">
            <label for="address2">Address Line 2 <span class="muted">(optional)</span></label>
            <input id="address2" name="address2" type="text" placeholder="Suite, unit, etc.">
          </div>
          <div class="grid-3">
            <div class="field">
              <label for="city">City</label>
              <input id="city" name="city" type="text" placeholder="San Francisco" required>
            </div>
            <div class="field">
              <label for="state">State/Province</label>
              <input id="state" name="state" type="text" placeholder="CA" required>
            </div>
            <div class="field">
              <label for="postal">Postal Code</label>
              <input id="postal" name="postal" type="text" placeholder="94107" required>
            </div>
          </div>
          <div class="field">
            <label for="country">Country</label>
            <select id="country" name="country" required>
              <option value="" disabled selected>Select country</option>
              <option value="US">United States</option>
              <option value="SG">Singapore</option>
              <option value="CA">Canada</option>
              <option value="GB">United Kingdom</option>
              <option value="AU">Australia</option>
            </select>
          </div>
        </div>

        <!-- Shipping Options -->
        <div class="card" id="shipping">
          <h2>Shipping</h2>
          <div class="options">
            <label class="option">
              <input type="radio" name="shipping" value="standard" checked>
              <div>
                <strong>Standard</strong>
                <div class="muted">3–5 business days • Free</div>
              </div>
            </label>
            <label class="option">
              <input type="radio" name="shipping" value="express">
              <div>
                <strong>Express</strong>
                <div class="muted">1–2 business days • +$15.00</div>
              </div>
            </label>
          </div>
        </div>

        <!-- Payment Method -->
        <div class="card" id="payment">
          <h2>Payment</h2>
          <div class="options">
            <label class="option">
              <input type="radio" name="payment" value="card" checked>
              <div>
                <strong>Credit / Debit Card</strong>
                <div class="muted">Visa, MasterCard, AmEx</div>
              </div>
            </label>
            <label class="option">
              <input type="radio" name="payment" value="paynow">
              <div>
                <strong>PayNow</strong>
                <div class="muted">Instant bank transfer</div>
              </div>
            </label>
          </div>

          <!-- Card Fields -->
          <div class="pay-section" id="payCard">
            <div class="field">
              <label for="cardNumber">Card Number</label>
              <input id="cardNumber" type="text" inputmode="numeric" placeholder="1234 5678 9012 3456">
            </div>
            <div class="grid-2">
              <div class="field">
                <label for="cardExpiry">Expiry</label>
                <input id="cardExpiry" type="text" placeholder="MM/YY">
              </div>
              <div class="field">
                <label for="cardCvc">CVC</label>
                <input id="cardCvc" type="text" inputmode="numeric" placeholder="123">
              </div>
            </div>
          </div>

          <!-- PayNow Fields -->
          <div class="pay-section hidden" id="payNow">
            <div class="paynow-box">
              <div class="qr">QR</div>
              <div>
                <strong>Scan to Pay</strong>
                <p class="muted">Open your banking app and scan the QR. Your order will be confirmed once payment is received.</p>
              </div>
            </div>
          </div>
        </div>

        <button class="cta-btn dark-btn place-order" id="placeOrder" type="button">Place Order</button>
        <p class="secure-note">By placing your order, you agree to our terms and return policy.</p>

      </section>

      <!-- RIGHT: SUMMARY -->
      <aside class="summary card" id="summary">
        <h2>Order Summary</h2>

        <div id="summaryItems" class="items"></div>

        <div class="row"><span>Subtotal</span><span id="sumSubtotal">$0.00</span></div>
        <div class="row hidden" id="sumDiscountRow"><span>Discounts</span><span id="sumDiscount">−$0.00</span></div>
        <div class="row"><span>Tax (8%)</span><span id="sumTax">$0.00</span></div>
        <div class="row"><span>Shipping</span><span id="sumShipping">$0.00</span></div>
        <div class="divider"></div>
        <div class="row total"><span>Total</span><span id="sumTotal">$0.00</span></div>

        <div class="helper">
          <a href="cart.php">← Back to cart</a>
        </div>
      </aside>

    </div>
  </main>

  <?php include 'footer.php'; ?>

</body>
</html>

