<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/../lib/auth.php';

$redirect = $_GET['redirect'] ?? $_POST['redirect'] ?? '';
$errors = [];
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (auth_user()) {
    header('Location: ' . ($redirect ?: 'index.php'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    if ($password === '') {
        $errors[] = 'Password is required.';
    }

    if (!$errors) {
        $stmt = $pdo->prepare('SELECT id, email, password_hash, full_name, is_admin FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            auth_login($user);
            header('Location: ' . ($redirect ?: 'index.php'));
            exit;
        }

        $errors[] = 'Invalid email or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Log in | ByteBuy</title>
  <link rel="stylesheet" href="css/styles.css">
  <link rel="stylesheet" href="css/auth.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
</head>
<body class="auth-body">
  <?php include 'header.php'; ?>
  <main class="auth-wrapper">
    <section class="auth-card">
      <h1>Welcome back</h1>
      <p class="lead">Sign in to sync your cart, track orders, and unlock ByteBuy perks.</p>

      <?php if ($errors): ?>
        <div class="auth-errors">
          <ul>
            <?php foreach ($errors as $error): ?>
              <li><?= htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form method="post" class="auth-form" novalidate>
        <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect); ?>">
        <div class="auth-field">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" value="<?= htmlspecialchars($email); ?>" required>
        </div>
        <div class="auth-field">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" required>
        </div>
        <div class="auth-actions">
          <button class="auth-submit" type="submit">Log in</button>
          <p class="auth-utility">Need an account? <a href="register.php<?= $redirect ? '?redirect=' . urlencode($redirect) : ''; ?>">Create one</a>.</p>
        </div>
      </form>
    </section>
  </main>
  <?php include 'footer.php'; ?>
  <script src="js/header.js"></script>
</body>
</html>
