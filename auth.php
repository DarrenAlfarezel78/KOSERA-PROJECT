<?php

require_once __DIR__ . '/config/error_handler.php';

if (!function_exists('appStartSession')) {
    function appStartSession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443);

        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => $isHttps,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);

        session_start();
    }
}

if (!function_exists('appLoginUser')) {
    function appLoginUser(array $user): void
    {
        appStartSession();
        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id' => (int) ($user['id'] ?? 0),
            'name' => (string) ($user['name'] ?? ''),
            'email' => (string) ($user['email'] ?? ''),
            'phone' => (string) ($user['phone'] ?? '')
        ];
    }
}

if (!function_exists('appLogoutUser')) {
    function appLogoutUser(): void
    {
        appStartSession();
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], (bool) $params['secure'], (bool) $params['httponly']);
        }

        session_destroy();
        session_start();
        session_regenerate_id(true);
    }
}

appStartSession();

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

function safeRedirectTarget(string $target, string $default = 'services'): string
{
    $target = trim($target);

    if ($target === '' || preg_match('/^[a-z][a-z0-9+.-]*:|^\/\//i', $target)) {
        return $default;
    }

    return ltrim($target, '/');
}

function requireLogin(string $loginPath = 'auth/login'): void
{
    if (isLoggedIn()) {
        return;
    }

    header('Location: login.php');
    exit();
}

function redirectIfLoggedIn(string $target = 'index.php'): void
{
    if (!isLoggedIn()) {
        return;
    }

    header('Location: ' . ltrim($target, '/'));
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
