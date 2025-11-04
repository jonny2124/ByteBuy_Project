<?php
// public/db.php
// Database connection using PDO. For production, move credentials outside webroot.

// We are not using PHP session cookies in this build - carts are token-based (localStorage)
// If you later want server sessions, uncomment and configure the cookie params and call session_start().

$DB_HOST = '127.0.0.1';
$DB_NAME = 'bytebuy';
$DB_USER = 'root';
$DB_PASS = ''; // XAMPP default: empty for root

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

$dsn = "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4";
try {
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
} catch (PDOException $e) {
    // In production, log error instead of echoing
    die('Database connection failed: ' . $e->getMessage());
}

?>
