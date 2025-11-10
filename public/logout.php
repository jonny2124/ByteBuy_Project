<?php
require_once __DIR__ . '/../lib/auth.php';

auth_logout();

$redirect = $_GET['redirect'] ?? 'index.php';
header('Location: ' . $redirect);
exit;
