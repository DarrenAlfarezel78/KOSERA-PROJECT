<?php

if (!function_exists('appRenderView')) {
    function appRenderView(string $viewPath, array $data = []): void
    {
        $viewFile = __DIR__ . '/../Views/' . ltrim($viewPath, '/');
        if (!is_file($viewFile)) {
            throw new RuntimeException('View not found: ' . $viewPath);
        }

        extract($data, EXTR_SKIP);
        require $viewFile;
    }
}
