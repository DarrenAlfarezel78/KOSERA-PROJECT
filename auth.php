<?php

require_once __DIR__ . '/config/error_handler.php';
require_once __DIR__ . '/app/Support/Session.php';

appStartSession();

function assetPath(string $path): string
{
    $segments = array_map('rawurlencode', explode('/', ltrim($path, '/')));
    return implode('/', $segments);
}

function appBasePath(): string
{
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    $basePath = preg_replace('#/public$#', '', $scriptDir);

    return rtrim((string) $basePath, '/');
}

function appUrl(string $path = ''): string
{
    $basePath = appBasePath();
    $page = ltrim($path, '/');

    if ($page === '') {
        $page = 'services';
    }

    $query = '';
    $route = $page;

    $questionMark = strpos($page, '?');
    if ($questionMark !== false) {
        $route = substr($page, 0, $questionMark);
        $query = substr($page, $questionMark + 1);
    }

    $url = ($basePath === '' ? '' : $basePath) . '/index.php?page=' . $route;
    if ($query !== '') {
        $url .= '&' . $query;
    }

    return $url;
}

function appRequestTarget(): string
{
    $page = trim((string) ($_GET['page'] ?? 'services'));

    if ($page === '') {
        $page = 'services';
    }

    $query = $_GET;
    unset($query['page']);

    $queryString = http_build_query($query);

    return $queryString !== '' ? $page . '?' . $queryString : $page;
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

    $redirectTarget = appRequestTarget();
    header('Location: ' . appUrl($loginPath . '?redirect=' . urlencode($redirectTarget)));
    exit();
}

function redirectIfLoggedIn(string $target = 'services'): void
{
    if (!isLoggedIn()) {
        return;
    }

    header('Location: ' . appUrl($target));
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
