<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/../lib/auth.php';

$redirect = $_GET['redirect'] ?? $_POST['redirect'] ?? '';
$errors = [];
$fullName = trim($_POST['full_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

if (auth_user()) {
    header('Location: ' . ($redirect ?: 'index.php'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($fullName === '') {
        $errors[] = 'Please provide your full name.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    }

    if ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    }

    if (!$errors) {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'An account with that email already exists.';
        }
    }

    if (!$errors) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO users (email, password_hash, full_name) VALUES (?, ?, ?)');
        $stmt->execute([$email, $hash, $fullName]);

        $user = [
            'id' => $pdo->lastInsertId(),
            'email' => $email,
            'full_name' => $fullName,
            'is_admin' => 0,
        ];

        auth_login($user);
        header('Location: ' . ($redirect ?: 'index.php'));
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Account | ByteBuy</title>
  <link rel="stylesheet" href="css/styles.css">
  <link rel="stylesheet" href="css/auth.css">
  <link rel="icon" type="image/png" href="assets/Favicon.png">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
</head>
<body class="auth-body">
  <?php include 'header.php'; ?>
  <main class="auth-wrapper">
    <section class="auth-card">
      <h1>Join ByteBuy</h1>
      <p class="lead">Create an account to sync carts, save orders, and get early access to drops.</p>

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
          <label for="full_name">Full name</label>
          <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($fullName); ?>" required>
        </div>
        <div class="auth-field">
          <label for="reg_email">Email</label>
          <input type="email" id="reg_email" name="email" value="<?= htmlspecialchars($email); ?>" required>
        </div>
        <div class="auth-field">
          <label for="reg_password">Password</label>
          <input type="password" id="reg_password" name="password" required>
        </div>
        <div class="auth-field">
          <label for="confirm_password">Confirm password</label>
          <input type="password" id="confirm_password" name="confirm_password" required>
        </div>
        <div class="auth-actions">
          <button class="auth-submit" type="submit">Create account</button>
          <p class="auth-utility">Already have an account? <a href="login.php<?= $redirect ? '?redirect=' . urlencode($redirect) : ''; ?>">Log in</a>.</p>
        </div>
      </form>
    </section>
  </main>
  <?php include 'footer.php'; ?>
  <script src="js/header.js"></script>
</body>
</html>
