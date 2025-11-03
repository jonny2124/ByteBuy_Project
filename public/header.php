<!-- HEADER -->
<header class="header">
  <div class="top-bar">
    <div class="logo-section">
      <a href="index.php">
        <img src="assets/Logo.png" alt="ByteBuy Logo">
      </a>
    </div>
    <nav>
      <a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">Home</a>
      <a href="shop.php" class="<?= basename($_SERVER['PHP_SELF']) == 'shop.php' ? 'active' : '' ?>">Shop</a>
      <a href="cart.php" class="<?= basename($_SERVER['PHP_SELF']) == 'cart.php' ? 'active' : '' ?>">Cart</a>
      <a href="order-status.php" class="<?= basename($_SERVER['PHP_SELF']) == 'order-status.php' ? 'active' : '' ?>">Track Order</a>
    </nav>
  </div>
</header>
