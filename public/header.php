<?php
require_once __DIR__ . '/../lib/auth.php';

$currentPage = basename($_SERVER['PHP_SELF']);
$currentUser = auth_user();

if (!function_exists('bytebuy_normalize_path')) {
    function bytebuy_normalize_path(string $href): string
    {
        $parts = parse_url($href);
        if (!isset($parts['path'])) {
            return '';
        }
        return basename($parts['path']);
    }
}

$navGroups = [
    [
        'label' => 'Shop',
        'tagline' => 'Browse every category and current hero deals.',
        'cta' => ['label' => 'Shop All Products', 'href' => 'shop.php'],
        'links' => [
            ['label' => 'All Products', 'href' => 'shop.php', 'description' => 'Full ByteBuy assortment'],
            ['label' => 'Laptops', 'href' => 'shop.php?filter=laptops', 'description' => 'Ultrabooks & creators'],
            ['label' => 'Smartphones', 'href' => 'shop.php?filter=smartphones', 'description' => 'Flagship & budget picks'],
            ['label' => 'Audio', 'href' => 'shop.php?filter=audio', 'description' => 'Headphones & earbuds'],
            ['label' => 'Storage', 'href' => 'shop.php?filter=storage', 'description' => 'SSD, HDD, cards'],
            ['label' => 'Accessories', 'href' => 'shop.php?filter=accessories', 'description' => 'Everyday add-ons'],
        ],
    ],
    [
        'label' => 'Experience',
        'tagline' => 'Connect with ByteBuy’s story and showrooms.',
        'cta' => ['label' => 'Visit Homepage', 'href' => 'index.php'],
        'links' => [
            ['label' => 'Home', 'href' => 'index.php', 'description' => 'Latest launches'],
            ['label' => 'About Us', 'href' => 'about.php', 'description' => 'Our values & mission'],
            ['label' => 'Contact', 'href' => 'contact.php', 'description' => 'Talk to our team'],
            ['label' => 'Shop Grid', 'href' => 'shop.php?sort=rating_desc', 'description' => 'Top-rated gear'],
        ],
    ],
    [
        'label' => 'Orders',
        'tagline' => 'Keep carts, orders, and deliveries on track.',
        'cta' => ['label' => 'Order Tracking', 'href' => 'order-status.php'],
        'links' => [
            ['label' => 'Cart', 'href' => 'cart.php', 'description' => 'View saved items'],
            ['label' => 'Checkout', 'href' => 'checkout.php', 'description' => 'Secure payment flow'],
            ['label' => 'Order Status', 'href' => 'order-status.php', 'description' => 'Track fulfillment'],
            ['label' => 'Order History', 'href' => 'order-confirmation.php', 'description' => 'Latest confirmation'],
        ],
    ],
    [
        'label' => 'Account',
        'tagline' => 'Manage sign-in, loyalty, and security.',
        'cta' => ['label' => $currentUser ? 'Account Hub' : 'Login Portal', 'href' => $currentUser ? 'order-status.php' : 'login.php'],
        'links' => $currentUser ? [
            ['label' => 'Order Status', 'href' => 'order-status.php', 'description' => 'Track fulfillment'],
            ['label' => 'Order History', 'href' => 'order-confirmation.php', 'description' => 'Latest confirmation'],
            ['label' => 'Support Inbox', 'href' => 'contact.php', 'description' => 'Submit a request'],
            ['label' => 'Logout', 'href' => 'logout.php', 'description' => 'Sign out securely'],
        ] : [
            ['label' => 'Login', 'href' => 'login.php', 'description' => 'Access your account'],
            ['label' => 'Register', 'href' => 'register.php', 'description' => 'Create membership'],
            ['label' => 'Order Status', 'href' => 'order-status.php', 'description' => 'Guest tracking'],
            ['label' => 'Support Inbox', 'href' => 'contact.php', 'description' => 'Need help?'],
        ],
    ],
];

$loginButton = $currentUser
    ? ['label' => 'Log out', 'href' => 'logout.php']
    : ['label' => 'Log in', 'href' => 'login.php'];
?>
<!-- HEADER -->
<header class="header mega-header">
  <div class="top-bar">
    <div class="logo-section">
      <a href="index.php">
        <img src="assets/Logo1.png" alt="ByteBuy Logo">
      </a>
    </div>
    <nav class="mega-nav" aria-label="Primary">
      <ul class="nav-links">
        <?php $totalGroups = count($navGroups); ?>
        <?php foreach ($navGroups as $index => $group): ?>
          <?php
          $menuId = 'megaMenu' . $index;
          $alignClass = ($index >= $totalGroups - 2) ? 'align-right' : '';
          ?>
          <li class="nav-item <?= $alignClass; ?>">
            <button class="nav-trigger" type="button" aria-expanded="false" aria-controls="<?= $menuId; ?>">
              <?= htmlspecialchars($group['label']); ?>
              <span class="chevron" aria-hidden="true"></span>
            </button>
            <div class="mega-menu" id="<?= $menuId; ?>" role="menu">
              <div class="mega-menu-grid">
                <div class="mega-intro">
                  <p class="mega-label"><?= htmlspecialchars($group['label']); ?></p>
                  <h4><?= htmlspecialchars($group['tagline']); ?></h4>
                  <?php if (!empty($group['cta'])): ?>
                    <a class="mega-cta" href="<?= htmlspecialchars($group['cta']['href']); ?>">
                      <?= htmlspecialchars($group['cta']['label']); ?>  
                    </a>
                  <?php endif; ?>
                </div>
                <div class="mega-columns">
                  <?php
                  $columns = array_chunk($group['links'], ceil(count($group['links']) / 2));
                  foreach ($columns as $column):
                  ?>
                    <div class="mega-column">
                      <?php foreach ($column as $link): ?>
                        <?php $isCurrent = bytebuy_normalize_path($link['href']) === $currentPage; ?>
                        <a href="<?= htmlspecialchars($link['href']); ?>" class="<?= $isCurrent ? 'is-active' : ''; ?>">
                          <span><?= htmlspecialchars($link['label']); ?></span>
                          <?php if (!empty($link['description'])): ?>
                            <small><?= htmlspecialchars($link['description']); ?></small>
                          <?php endif; ?>
                        </a>
                      <?php endforeach; ?>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
      <div class="nav-actions">
        <?php $loginActive = bytebuy_normalize_path($loginButton['href']) === $currentPage; ?>
        <a href="<?= htmlspecialchars($loginButton['href']); ?>" class="pill-link login-pill <?= $loginActive ? 'is-active' : ''; ?>">
          <?= htmlspecialchars($loginButton['label']); ?>
        </a>
      </div>
    </nav>
  </div>
</header>
