<?php

if (!defined('APP_ERROR_HANDLER_REGISTERED')) {
    define('APP_ERROR_HANDLER_REGISTERED', true);
}

if (!function_exists('appRenderHttp500')) {
    function appRenderHttp500(string $errorId = '', string $message = '', string $file = '', int $line = 0): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }

        $_SESSION['app_last_error'] = [
            'id' => $errorId,
            'message' => $message,
            'file' => $file,
            'line' => $line,
            'time' => date('Y-m-d H:i:s')
        ];

        http_response_code(500);

        $errorPage = __DIR__ . '/../500.php';
        if (is_file($errorPage)) {
            include $errorPage;
            exit();
        }

        echo 'Internal Server Error';
        exit();
    }
}

if (!function_exists('appBuildErrorId')) {
    function appBuildErrorId(): string
    {
        return 'ERR-' . date('YmdHis') . '-' . substr(md5(uniqid('', true)), 0, 6);
    }
}

set_error_handler(function (int $severity, string $message, string $file, int $line): bool {
    if (!(error_reporting() & $severity)) {
        return false;
    }

    throw new ErrorException($message, 0, $severity, $file, $line);
});

set_exception_handler(function (Throwable $exception): void {
    $errorId = appBuildErrorId();

    error_log(sprintf(
        '[%s] %s | %s in %s:%d | Trace: %s',
        $errorId,
        get_class($exception),
        $exception->getMessage(),
        $exception->getFile(),
        $exception->getLine(),
        $exception->getTraceAsString()
    ));

    appRenderHttp500(
        $errorId,
        $exception->getMessage(),
        $exception->getFile(),
        $exception->getLine()
    );
});

register_shutdown_function(function (): void {
    $error = error_get_last();
    if ($error === null) {
        return;
    }

    $fatalTypes = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR];
    if (!in_array($error['type'], $fatalTypes, true)) {
        return;
    }

    $errorId = appBuildErrorId();

    error_log(sprintf(
        '[%s] FATAL | %s in %s:%d',
        $errorId,
        $error['message'],
        $error['file'],
        $error['line']
    ));

    appRenderHttp500(
        $errorId,
        (string) $error['message'],
        (string) $error['file'],
        (int) $error['line']
    );
});
