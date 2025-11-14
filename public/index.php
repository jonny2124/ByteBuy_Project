<!DOCTYPE html>
<html lang="en">
	<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>ByteBuy | Home</title>
	<link rel="stylesheet" href="css/styles.css">
	<link rel="icon" type="image/png" href="assets/Favicon.png">
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
	</head>
	<body>

	<?php include 'header.php'; ?>

	<!-- HERO -->
	<section class="hero home-hero">
		<div class="hero-overlay">
		<h1>Affordable Tech at Your Fingertips</h1>
		<p>Shop the latest gadgets and electronics at unbeatable prices.</p>
		<a href="shop.php" class="cta-btn">Shop Now</a>
		</div>
	</section>

	<!-- PROMOTIONS -->
	<section class="promo-container">
		<div class="promo-highlight">
			<a href="product-details.php?id=lap2" class="promo-link">
			<div class="promo-content">
				<p class="promo-tag">Flash Deal</p>
				<h2>MacBook Air M4</h2>
				<p class="promo-desc">Lightweight performance with up to 18 hours of battery life.</p>
				<div class="promo-image">
					<img src="assets/home/macbook.jpeg" alt="MacBook Air M4">
				</div>	
				<p class="promo-price">Starting at $1,299</p>
				<span class="cta-btn dark-btn">View Details</span>
			</div>
			</a>
		</div>

		<div class="promo-highlight">
			<a href="product-details.php?id=ph1" class="promo-link">
			<div class="promo-content">
				<p class="promo-tag">Flash Deal</p>
				<h2>Samsung Galaxy S25</h2>
				<p class="promo-desc">Flagship cameras, 120Hz AMOLED, and Snapdragon Elite silicon.</p>
				<div class="promo-image">
					<img src="assets/products/SamsungGalaxyS25.jpg" alt="Samsung Galaxy S25">
				</div>
				<p class="promo-price">Starting at $899</p>
				<span class="cta-btn dark-btn">View Details</span>
			</div>
			</a>
		</div>
	</section>

	<!-- CATEGORIES -->
	<section class="categories">
		<h2>Popular Categories</h2>
		<div class="category-grid">
		<div class="category-card"><img src="assets/home/phone.webp" alt="Phones" class="category-image"><p>Phones</p><button class="cta-btn">Shop</button></div>
		<div class="category-card"><img src="assets/home/laptop.jpg" alt="Laptops" class="category-image"><p>Laptops</p><button class="cta-btn">Shop</button></div>
		<div class="category-card"><img src="assets/home/tablet.jpeg" alt="Tablets" class="category-image"><p>Tablets</p><button class="cta-btn">Shop</button></div>
		<div class="category-card"><img src="assets/home/smartwatch.jpg" alt="Smartwatches" class="category-image"><p>Smartwatches</p><button class="cta-btn">Shop</button></div>
		<div class="category-card"><img src="assets/home/headphone.webp" alt="Headphones" class="category-image"><p>Headphones</p><button class="cta-btn">Shop</button></div>
		</div>
	</section>

	<!-- DEAL OF THE DAY -->
	<section class="deals">
		<h2>Deal of the Day</h2>
		<div class="deal-grid">
		<a class="deal-card" href="product-details.php?id=ph1">
			<div class="deal-sale">SALE!</div>
			<img src="assets/home/deal1.webp" alt="Samsung Galaxy S25" class="deal-image">
			<h3>Samsung Galaxy S25</h3>
			<p class="old-price">$1,049.00</p>
			<p class="new-price">$899.00</p>
			<div class="timer"></div>
		</a>

		<a class="deal-card" href="product-details.php?id=lap1">
			<div class="deal-sale">SALE!</div>
			<img src="assets/home/deal2.jpeg" alt="MacBook Air M2" class="deal-image">
			<h3>MacBook Air M2</h3>
			<p class="old-price">$1,399.00</p>
			<p class="new-price">$1,099.00</p>
			<div class="timer"></div>
		</a>

		<a class="deal-card" href="product-details.php?id=au1">
			<div class="deal-sale">SALE!</div>
			<img src="assets/home/deal3.jpg" alt="Sony WH-1000XM5" class="deal-image">
			<h3>Sony WH-1000XM5</h3>
			<p class="old-price">$499.00</p>
			<p class="new-price">$399.00</p>
			<div class="timer"></div>
		</a>
		</div>
	</section>

	<!-- BEST SELLER SPOTLIGHT -->
	<section class="best-sellers">
		<a class="best-highlight best-highlight-link" href="product-details.php?id=ac9">
		<div class="best-content">
			<p class="best-tag">Best Seller</p>
			<h2>Apple Watch</h2>
			<p class="best-price">Starting at <span>$399</span></p>
			<span class="cta-btn dark-btn">View Details</span>
		</div>
		<div class="best-image">
			<img src="assets/home/best.png" alt="Apple Watch">
		</div>
		</a>
	</section>

	<?php include 'footer.php'; ?>

	<script src="js/timer.js"></script>
	<script src="js/header.js"></script>
	</body>
</html>
