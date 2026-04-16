<?php

if (!function_exists('fetchAllServices')) {
    function fetchAllServices(mysqli $conn): array
    {
        $stmt = $conn->prepare('SELECT s.id, s.title, s.description, s.price, s.provider_name, s.image, s.updated_at, c.id AS category_id, c.name AS category_name, sc.name AS sub_category_name FROM services s INNER JOIN categories c ON c.id = s.category_id INNER JOIN sub_categories sc ON sc.id = s.sub_category_id ORDER BY s.created_at DESC');
        $stmt->execute();
        $result = $stmt->get_result();
        $services = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $services;
    }
}

if (!function_exists('findServiceById')) {
    function findServiceById(mysqli $conn, int $id): ?array
    {
        $stmt = $conn->prepare('SELECT s.id, s.title, s.description, s.price, s.provider_name, s.image, s.certificate, s.created_at, s.updated_at, c.name AS category_name, sc.name AS sub_category_name FROM services s INNER JOIN categories c ON c.id = s.category_id INNER JOIN sub_categories sc ON sc.id = s.sub_category_id WHERE s.id = ? LIMIT 1');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $service = $result->num_rows === 1 ? $result->fetch_assoc() : null;
        $stmt->close();

        return $service;
    }
}

if (!function_exists('fetchServiceFormDataById')) {
    function fetchServiceFormDataById(mysqli $conn, int $id): ?array
    {
        $stmt = $conn->prepare('SELECT id, title, description, price, provider_name, category_id, sub_category_id, image, certificate, updated_at FROM services WHERE id = ? LIMIT 1');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $service = $result->num_rows === 1 ? $result->fetch_assoc() : null;
        $stmt->close();

        return $service;
    }
}

if (!function_exists('fetchServiceBinaryByType')) {
    function fetchServiceBinaryByType(mysqli $conn, int $id, string $type): ?string
    {
        $column = $type === 'image' ? 'image' : 'certificate';
        $stmt = $conn->prepare("SELECT $column FROM services WHERE id = ? LIMIT 1");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows !== 1) {
            $stmt->close();
            return null;
        }

        $row = $result->fetch_assoc();
        $stmt->close();

        $data = $row[$column] ?? null;
        if (!is_string($data) || $data === '') {
            return null;
        }

        return $data;
    }
}

if (!function_exists('deleteServiceById')) {
    function deleteServiceById(mysqli $conn, int $id): bool
    {
        $stmt = $conn->prepare('DELETE FROM services WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $affected = $stmt->affected_rows > 0;
        $stmt->close();

        return $affected;
    }
}

if (!function_exists('fetchCategories')) {
    function fetchCategories(mysqli $conn): array
    {
        $categories = [];
        $result = $conn->query('SELECT id, name FROM categories ORDER BY name ASC');
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }

        return $categories;
    }
}

if (!function_exists('fetchSubCategories')) {
    function fetchSubCategories(mysqli $conn): array
    {
        $subCategories = [];
        $result = $conn->query('SELECT id, category_id, name FROM sub_categories ORDER BY name ASC');
        while ($row = $result->fetch_assoc()) {
            $subCategories[] = $row;
        }

        return $subCategories;
    }
}

if (!function_exists('categoryMatchesSubCategory')) {
    function categoryMatchesSubCategory(mysqli $conn, int $subCategoryId, int $categoryId): bool
    {
        $stmt = $conn->prepare('SELECT id FROM sub_categories WHERE id = ? AND category_id = ?');
        $stmt->bind_param('ii', $subCategoryId, $categoryId);
        $stmt->execute();
        $result = $stmt->get_result();
        $matches = $result->num_rows === 1;
        $stmt->close();

        return $matches;
    }
}

if (!function_exists('uploadErrorMessage')) {
    function uploadErrorMessage(int $errorCode): string
    {
        switch ($errorCode) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return 'Ukuran file melebihi batas upload server/form.';
            case UPLOAD_ERR_PARTIAL:
                return 'File terupload sebagian. Silakan coba lagi.';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Folder temporary upload tidak ditemukan.';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Gagal menulis file ke disk server.';
            case UPLOAD_ERR_EXTENSION:
                return 'Upload dihentikan oleh ekstensi PHP.';
            default:
                return 'Terjadi error upload file.';
        }
    }
}

if (!function_exists('detectImageMime')) {
    function detectImageMime(string $tmpPath): ?string
    {
        if (!is_readable($tmpPath)) {
            return null;
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo === false) {
            return null;
        }

        $mime = finfo_file($finfo, $tmpPath);
        finfo_close($finfo);

        if (!is_string($mime)) {
            return null;
        }

        return $mime;
    }
}
