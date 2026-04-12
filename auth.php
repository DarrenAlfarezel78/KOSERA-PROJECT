<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function assetPath(string $path): string
{
    $segments = array_map('rawurlencode', explode('/', ltrim($path, '/')));
    return implode('/', $segments);
}

function isLoggedIn(): bool
{
    return !empty($_SESSION['user']) && !empty($_SESSION['user']['id']);
}

function currentUser(): ?array
{
    return $_SESSION['user'] ?? null;
}

function safeRedirectTarget(string $target, string $default = 'index.php'): string
{
    $target = trim($target);

    if ($target === '' || preg_match('/^[a-z][a-z0-9+.-]*:|^\/\//i', $target)) {
        return $default;
    }

    return ltrim($target, '/');
}

function requireLogin(string $loginPath = 'login.php'): void
{
    if (isLoggedIn()) {
        return;
    }

    $redirectTarget = $_SERVER['REQUEST_URI'] ?? 'index.php';
    header('Location: ' . $loginPath . '?redirect=' . urlencode($redirectTarget));
    exit();
}

function redirectIfLoggedIn(string $target = 'index.php'): void
{
    if (!isLoggedIn()) {
        return;
    }

    header('Location: ' . $target);
    exit();
}

function flashSet(string $key, string $message): void
{
    if (!isset($_SESSION['flash'])) {
        $_SESSION['flash'] = [];
    }

    $_SESSION['flash'][$key] = $message;
}

function flashGet(string $key): string
{
    if (empty($_SESSION['flash']) || !array_key_exists($key, $_SESSION['flash'])) {
        return '';
    }

    $message = (string) $_SESSION['flash'][$key];
    unset($_SESSION['flash'][$key]);

    return $message;
}
