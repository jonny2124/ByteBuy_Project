<?php
require_once __DIR__ . '/db.php';

function map_category_from_sku(string $sku): string
{
    $prefix = substr(strtolower($sku), 0, 2);
    return match ($prefix) {
        'la' => 'Laptops',
        'ph' => 'Smartphones',
        'au' => 'Audio',
        'st' => 'Storage',
        'ac' => 'Accessories',
        default => 'Other',
    };
}

function category_like_pattern(string $sku): ?string
{
    $prefix = substr(strtolower($sku), 0, 2);
    if ($prefix === '') {
        return null;
    }
    return $prefix . '%';
}

function normalize_image_path(?string $image): string
{
    $image = trim((string)($image ?? ''));
    if ($image === '') {
        return 'assets/products/placeholder.png';
    }

    $isAbsolute = preg_match('#^(https?:)?//#i', $image) === 1;
    if ($isAbsolute) {
        return $image;
    }

    $image = ltrim($image, '/');
    if (strpos($image, 'assets/') === 0) {
        return $image;
    }

    return 'assets/products/' . $image;
}

function derive_rating(string $sku): float
{
    $hash = crc32(strtolower($sku));
    $fraction = ($hash % 40) / 100; // 0.00 - 0.39
    return round(4.2 + $fraction, 1);
}

$productId = trim($_GET['id'] ?? '');
$product = null;
$relatedProducts = [];
$error = '';

if ($productId === '') {
    $error = 'Missing product reference.';
} else {
    $stmt = $pdo->prepare('SELECT sku, name, description, price, stock, image FROM items WHERE sku = ? LIMIT 1');
    $stmt->execute([$productId]);
    $row = $stmt->fetch();

    if ($row) {
        $product = [
            'sku' => $row['sku'],
            'name' => $row['name'],
            'description' => $row['description'] ?: 'Experience the next generation of tech performance with premium craftsmanship.',
            'price' => (float)$row['price'],
            'stock' => (int)$row['stock'],
            'image' => normalize_image_path($row['image']),
        ];

        $product['category'] = map_category_from_sku($product['sku']);
        $product['rating'] = derive_rating($product['sku']);

        $pattern = category_like_pattern($product['sku']);
        if ($pattern) {
            $relStmt = $pdo->prepare('SELECT sku, name, price, image FROM items WHERE sku LIKE ? AND sku <> ? ORDER BY RAND() LIMIT 4');
            $relStmt->execute([$pattern, $product['sku']]);
            $relatedProducts = $relStmt->fetchAll() ?: [];
        }

        foreach ($relatedProducts as &$item) {
            $item['image'] = normalize_image_path($item['image'] ?? '');
            $item['price'] = (float)$item['price'];
            $item['category'] = map_category_from_sku($item['sku']);
        }
        unset($item);
    } else {
        $error = 'We could not find that product.';
    }
}

$pageTitle = $product ? "{$product['name']} | ByteBuy" : 'Product Details | ByteBuy';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle); ?></title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/product-detail.css">
    <link rel="icon" type="image/png" href="assets/Favicon.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
</head>
<body>
<?php include 'header.php'; ?>

<main class="product-page-wrapper">
    <?php if ($error): ?>
        <section class="product-empty">
            <h1>Product Not Found</h1>
            <p><?= htmlspecialchars($error); ?></p>
            <div class="product-empty-actions">
                <a href="shop.php" class="cta-btn">Back to Shop</a>
                <a href="index.php" class="cta-btn dark-btn">Go Home</a>
            </div>
        </section>
    <?php elseif ($product): ?>
        <nav class="breadcrumb" aria-label="Breadcrumb">
            <a href="index.php">Home</a>
            <span class="divider">></span>
            <a href="shop.php">Shop</a>
            <span class="divider">></span>
            <span aria-current="page"><?= htmlspecialchars($product['name']); ?></span>
        </nav>

        <section class="product-hero" id="productDetail"
                 data-sku="<?= htmlspecialchars($product['sku']); ?>"
                 data-stock="<?= (int)$product['stock']; ?>"
                 data-name="<?= htmlspecialchars($product['name']); ?>">
            <div class="product-gallery">
                <img src="<?= htmlspecialchars($product['image']); ?>" alt="<?= htmlspecialchars($product['name']); ?>">
            </div>
            <div class="product-summary">
                <p class="product-category-tag"><?= htmlspecialchars($product['category']); ?></p>
                <h1><?= htmlspecialchars($product['name']); ?></h1>
                <div class="rating-row">
                    <div class="rating-stars" aria-label="Rated <?= number_format($product['rating'], 1); ?> out of 5">
                        <?php
                        $score = $product['rating'];
                        $fullStars = floor($score);
                        $hasHalf = ($score - $fullStars) >= 0.5;
                        for ($i = 1; $i <= 5; $i++):
                            $class = '';
                            if ($i <= $fullStars) {
                                $class = 'filled';
                            } elseif ($hasHalf && $i === $fullStars + 1) {
                                $class = 'half';
                            }
                            ?>
                            <span class="star <?= $class; ?>">&#9733;</span>
                        <?php endfor; ?>
                    </div>
                    <span class="rating-score"><?= number_format($product['rating'], 1); ?> / 5</span>
                </div>
                <p class="product-description"><?= nl2br(htmlspecialchars($product['description'])); ?></p>

                <div class="price-stock-row">
                    <p class="product-price">$<?= number_format($product['price'], 2); ?></p>
                    <div class="stock-line">
                        <?php $inStock = $product['stock'] > 0; ?>
                        <span id="stockStatus" class="stock-flag <?= $inStock ? 'in-stock' : 'out-of-stock'; ?>">
                            <?= $inStock ? 'In stock' : 'Out of stock'; ?>
                        </span>
                        <span class="stock-count">
                            <span id="stockValue"><?= (int)$product['stock']; ?></span> units available
                        </span>
                    </div>
                </div>

                <div class="purchase-panel">
                    <div class="quantity-picker" aria-label="Select quantity">
                        <button type="button" id="qtyMinus" aria-label="Decrease quantity">-</button>
                        <input type="number"
                               id="quantityInput"
                               min="1"
                               max="<?= max(1, (int)$product['stock']); ?>"
                               value="1"
                               <?= $product['stock'] < 1 ? 'disabled' : ''; ?>>
                        <button type="button" id="qtyPlus" aria-label="Increase quantity">+</button>
                    </div>
                    <button class="cta-btn dark-btn add-cart-btn"
                            id="addToCartBtn" <?= $product['stock'] < 1 ? 'disabled' : ''; ?>>
                        <?= $product['stock'] < 1 ? 'Out of Stock' : 'Add to Cart'; ?>
                    </button>
                </div>
                <div id="productToast" class="product-toast" role="status" aria-live="polite"></div>

                <ul class="product-facts">
                    <li><span>Category</span><strong><?= htmlspecialchars($product['category']); ?></strong></li>
                    <li><span>SKU</span><strong><?= htmlspecialchars(strtoupper($product['sku'])); ?></strong></li>
                    <li><span>Returns</span><strong>30-day easy returns</strong></li>
                    <li><span>Warranty</span><strong>1-year ByteBuy coverage</strong></li>
                </ul>
            </div>
        </section>

        <section class="related-products">
            <div class="section-heading">
                <h2>Suggested For You</h2>
                <p>More from <?= htmlspecialchars($product['category']); ?></p>
            </div>
            <?php if ($relatedProducts): ?>
                <div class="related-grid">
                    <?php foreach ($relatedProducts as $item): ?>
                        <article class="related-card">
                            <a href="product-details.php?id=<?= urlencode($item['sku']); ?>">
                                <figure>
                                    <img src="<?= htmlspecialchars($item['image']); ?>" alt="<?= htmlspecialchars($item['name']); ?>">
                                </figure>
                                <div class="related-info">
                                    <p class="related-category"><?= htmlspecialchars($item['category']); ?></p>
                                    <h3><?= htmlspecialchars($item['name']); ?></h3>
                                    <p class="related-price">$<?= number_format($item['price'], 2); ?></p>
                                </div>
                            </a>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="related-empty">We’re curating more picks for you. Check back soon.</p>
            <?php endif; ?>
        </section>
    <?php endif; ?>
</main>

<?php include 'footer.php'; ?>

<script src="js/header.js"></script>
<script src="js/product.js"></script>
</body>
</html>
