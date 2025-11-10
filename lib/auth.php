<?php
// lib/auth.php - lightweight session helpers for ByteBuy

if (session_status() === PHP_SESSION_NONE) {
    session_name('bytebuy_sid');
    session_start();
}

function auth_user(): ?array
{
    return $_SESSION['auth_user'] ?? null;
}

function auth_login(array $user): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_name('bytebuy_sid');
        session_start();
    }
    session_regenerate_id(true);
    $_SESSION['auth_user'] = [
        'id' => (int)($user['id'] ?? 0),
        'email' => (string)($user['email'] ?? ''),
        'full_name' => (string)($user['full_name'] ?? ''),
        'is_admin' => (int)($user['is_admin'] ?? 0),
    ];
}

function auth_logout(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        return;
    }
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}

function auth_require(string $redirectTo = null): void
{
    if (auth_user()) {
        return;
    }
    if ($redirectTo === null) {
        $redirectTo = $_SERVER['REQUEST_URI'] ?? 'index.php';
    }
    header('Location: login.php?redirect=' . urlencode($redirectTo));
    exit;
}
