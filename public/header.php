<?php
$shopPages = ['shop.php', 'product-details.php'];
$shopIsActive = in_array(basename($_SERVER['PHP_SELF']), $shopPages, true);
?>
<!-- HEADER -->
<header class="header">
  <div class="top-bar">
    <div class="logo-section">
      <a href="index.php">
        <img src="assets/Logo1.png" alt="ByteBuy Logo">
      </a>
    </div>
    <nav class="main-nav">
      <div class="dropdown" id="shopDropdown">
        <button class="menu-btn <?= $shopIsActive ? 'active' : '' ?>" id="shopMenuToggle" aria-haspopup="true" aria-expanded="false" aria-controls="shopMenu">Shop</button>
        <div class="dropdown-menu" id="shopMenu" role="menu">
          <a href="shop.php?filter=all" role="menuitem">All</a>
          <a href="shop.php?filter=laptops" role="menuitem">Laptops</a>
          <a href="shop.php?filter=smartphones" role="menuitem">Smartphones</a>
          <a href="shop.php?filter=audio" role="menuitem">Audio</a>
          <a href="shop.php?filter=storage" role="menuitem">Storage</a>
          <a href="shop.php?filter=accessories" role="menuitem">Accessories</a>
        </div>
      </div>

      <a href="cart.php" class="<?= basename($_SERVER['PHP_SELF']) == 'cart.php' ? 'active' : '' ?>">Cart</a>
      <a href="order-status.php" class="<?= basename($_SERVER['PHP_SELF']) == 'order-status.php' ? 'active' : '' ?>">Track Order</a>
    </nav>
  </div>
</header>
