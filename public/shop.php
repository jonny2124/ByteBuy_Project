<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>ByteBuy | Shop</title>
  <link rel="stylesheet" href="css/styles.css"/>
  <link rel="stylesheet" href="css/shop.css"/>
  <link rel="icon" type="image/png" href="assets/Favicon.png">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
</head>
<body>

  <?php include 'header.php'; ?>

  <!-- SHOP CONTROLS (sticky) -->
  <section class="shop-controls" id="shopControls">
    <div class="filters">
      <button class="filter-btn active" data-filter="All">All</button>
      <button class="filter-btn" data-filter="Laptops">Laptops</button>
      <button class="filter-btn" data-filter="Smartphones">Smartphones</button>
      <button class="filter-btn" data-filter="Audio">Audio</button>
      <button class="filter-btn" data-filter="Storage">Storage</button>
      <button class="filter-btn" data-filter="Accessories">Accessories</button>
    </div>

    <div class="utilities">
      <div class="search-wrap">
        <input id="searchInput" type="text" placeholder="Search products…"/>
        <span class="search-icon" aria-hidden="true">🔍</span>
      </div>

      <div class="sort-wrap">
        <label for="sortSelect" class="visually-hidden">Sort</label>
        <select id="sortSelect">
          <option value="default">Sort: Featured</option>
          <option value="price_asc">Price: Low to High</option>
          <option value="price_desc">Price: High to Low</option>
          <option value="rating_desc">Rating: High to Low</option>
          <option value="name_asc">Name: A to Z</option>
        </select>
      </div>
    </div>
  </section>

  <!-- PRODUCT GRID -->
  <main class="shop-grid" id="productGrid" aria-live="polite">
    <!-- Cards are injected by shop.js -->
  </main>

  <!-- Notification Container -->
  <div id="notificationOverlay" class="notification-overlay">
    <div id="notification" class="notification" role="alert" aria-live="polite"></div>
  </div>

  <?php include 'footer.php'; ?>

  <script src="js/header.js"></script>
  <script src="js/shop.js"></script>
</body>
</html>
