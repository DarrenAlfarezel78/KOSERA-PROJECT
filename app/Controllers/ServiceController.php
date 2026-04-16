<?php
require_once __DIR__ . '/../../auth.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Models/ServiceModel.php';
require_once __DIR__ . '/../Support/View.php';

function appIndexController(): void
{
    requireLogin();

    $conn = getConnection();
    $user = currentUser();
    $flashSuccess = flashGet('success');
    $success = isset($_GET['success']) ? trim($_GET['success']) : '';
    $services = fetchAllServices($conn);

    $successMessage = '';
    if ($flashSuccess !== '') {
        $successMessage = $flashSuccess;
    } elseif ($success === 'created') {
        $successMessage = 'Data jasa berhasil ditambahkan.';
    } elseif ($success === 'updated') {
        $successMessage = 'Data jasa berhasil diperbarui.';
    } elseif ($success === 'deleted') {
        $successMessage = 'Data jasa berhasil dihapus.';
    }

    $conn->close();

    appRenderView('services/index.php', compact('user', 'services', 'successMessage'));
}

function appDetailController(): void
{
    if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
        header('Location: ' . appUrl('services'));
        exit();
    }

    $id = (int) $_GET['id'];
    $conn = getConnection();
    $user = currentUser();
    $service = findServiceById($conn, $id);

    if ($service === null) {
        $conn->close();
        header('Location: ' . appUrl('services'));
        exit();
    }

    $conn->close();

    appRenderView('services/detail.php', compact('user', 'service'));
}

function appFormController(): void
{
    requireLogin();

    $conn = getConnection();
    $user = currentUser();
    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    $isEdit = $id > 0;

    $formData = [
        'id' => 0,
        'title' => '',
        'description' => '',
        'price' => '',
        'provider_name' => '',
        'category_id' => '',
        'sub_category_id' => ''
    ];

    if ($isEdit) {
        $service = fetchServiceFormDataById($conn, $id);
        if ($service !== null) {
            $formData = $service;
        } else {
            $conn->close();
            header('Location: ' . appUrl('services'));
            exit();
        }
    }

    $categories = fetchCategories($conn);
    $subCategories = fetchSubCategories($conn);
    $error = isset($_GET['error']) ? trim($_GET['error']) : '';

    $conn->close();

    appRenderView('services/form.php', compact('user', 'formData', 'categories', 'subCategories', 'error', 'isEdit'));
}

function appSaveServiceController(): void
{
    requireLogin();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ' . appUrl('services'));
        exit();
    }

    $conn = getConnection();

    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $providerName = trim($_POST['provider_name'] ?? '');
    $categoryId = isset($_POST['category_id']) ? (int) $_POST['category_id'] : 0;
    $subCategoryId = isset($_POST['sub_category_id']) ? (int) $_POST['sub_category_id'] : 0;
    $priceRaw = trim($_POST['price'] ?? '');

    $errors = [];

    if ($title === '') {
        $errors[] = 'Judul jasa wajib diisi.';
    }
    if ($description === '') {
        $errors[] = 'Deskripsi wajib diisi.';
    }
    if ($providerName === '') {
        $errors[] = 'Nama mitra wajib diisi.';
    }
    if ($categoryId <= 0) {
        $errors[] = 'Kategori wajib dipilih.';
    }
    if ($subCategoryId <= 0) {
        $errors[] = 'Sub-kategori wajib dipilih.';
    }
    if ($priceRaw === '' || !preg_match('/^\d+(\.\d{1,2})?$/', $priceRaw) || (float) $priceRaw <= 0) {
        $errors[] = 'Harga tidak valid.';
    }

    if (strlen($title) > 160) {
        $errors[] = 'Judul maksimal 160 karakter.';
    }
    if (strlen($providerName) > 120) {
        $errors[] = 'Nama mitra maksimal 120 karakter.';
    }

    $imageData = null;
    $certificateData = null;
    $maxFileSize = 2 * 1024 * 1024;

    if (isset($_FILES['image'])) {
        $imageError = (int) ($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE);

        if ($imageError !== UPLOAD_ERR_NO_FILE && $imageError !== UPLOAD_ERR_OK) {
            $errors[] = 'Gagal upload gambar: ' . uploadErrorMessage($imageError);
        } elseif ($imageError === UPLOAD_ERR_OK && (int) $_FILES['image']['size'] > 0) {
            if ((int) $_FILES['image']['size'] > $maxFileSize) {
                $errors[] = 'Gambar logo/cover terlalu besar (max 2MB).';
            } else {
                $imageMime = detectImageMime((string) $_FILES['image']['tmp_name']);
                if (!in_array($imageMime, ['image/jpeg', 'image/png'], true)) {
                    $errors[] = 'Format gambar harus JPG atau PNG.';
                } else {
                    $imageData = file_get_contents($_FILES['image']['tmp_name']);
                    if ($imageData === false || $imageData === '') {
                        $errors[] = 'Gagal membaca file gambar.';
                    }
                }
            }
        }
    }

    if (isset($_FILES['certificate'])) {
        $certificateError = (int) ($_FILES['certificate']['error'] ?? UPLOAD_ERR_NO_FILE);

        if ($certificateError !== UPLOAD_ERR_NO_FILE && $certificateError !== UPLOAD_ERR_OK) {
            $errors[] = 'Gagal upload sertifikat: ' . uploadErrorMessage($certificateError);
        } elseif ($certificateError === UPLOAD_ERR_OK && (int) $_FILES['certificate']['size'] > 0) {
            if ((int) $_FILES['certificate']['size'] > $maxFileSize) {
                $errors[] = 'Gambar sertifikat terlalu besar (max 2MB).';
            } else {
                $certificateMime = detectImageMime((string) $_FILES['certificate']['tmp_name']);
                if (!in_array($certificateMime, ['image/jpeg', 'image/png'], true)) {
                    $errors[] = 'Format sertifikat harus JPG atau PNG.';
                } else {
                    $certificateData = file_get_contents($_FILES['certificate']['tmp_name']);
                    if ($certificateData === false || $certificateData === '') {
                        $errors[] = 'Gagal membaca file sertifikat.';
                    }
                }
            }
        }
    }

    $title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars($description, ENT_QUOTES, 'UTF-8');
    $providerName = htmlspecialchars($providerName, ENT_QUOTES, 'UTF-8');
    $price = (float) $priceRaw;

    if (!categoryMatchesSubCategory($conn, $subCategoryId, $categoryId)) {
        $errors[] = 'Sub-kategori tidak sesuai dengan kategori.';
    }

    if (!empty($errors)) {
        $message = urlencode(implode(' ', $errors));
        $conn->close();
        if ($id > 0) {
            header('Location: ' . appUrl('services/edit/' . $id . '?error=' . $message));
        } else {
            header('Location: ' . appUrl('services/create?error=' . $message));
        }
        exit();
    }

    if ($id > 0) {
        if ($imageData !== null && $certificateData !== null) {
            $stmt = $conn->prepare('UPDATE services SET category_id = ?, sub_category_id = ?, title = ?, description = ?, price = ?, provider_name = ?, image = ?, certificate = ? WHERE id = ?');
            $stmt->bind_param('iissdsssi', $categoryId, $subCategoryId, $title, $description, $price, $providerName, $imageData, $certificateData, $id);
        } elseif ($imageData !== null) {
            $stmt = $conn->prepare('UPDATE services SET category_id = ?, sub_category_id = ?, title = ?, description = ?, price = ?, provider_name = ?, image = ? WHERE id = ?');
            $stmt->bind_param('iissdssi', $categoryId, $subCategoryId, $title, $description, $price, $providerName, $imageData, $id);
        } elseif ($certificateData !== null) {
            $stmt = $conn->prepare('UPDATE services SET category_id = ?, sub_category_id = ?, title = ?, description = ?, price = ?, provider_name = ?, certificate = ? WHERE id = ?');
            $stmt->bind_param('iissdssi', $categoryId, $subCategoryId, $title, $description, $price, $providerName, $certificateData, $id);
        } else {
            $stmt = $conn->prepare('UPDATE services SET category_id = ?, sub_category_id = ?, title = ?, description = ?, price = ?, provider_name = ? WHERE id = ?');
            $stmt->bind_param('iissdsi', $categoryId, $subCategoryId, $title, $description, $price, $providerName, $id);
        }

        $stmt->execute();
        $stmt->close();
        $conn->close();
        header('Location: ' . appUrl('services?success=updated'));
        exit();
    }

    if ($imageData !== null && $certificateData !== null) {
        $stmt = $conn->prepare('INSERT INTO services (category_id, sub_category_id, title, description, price, provider_name, image, certificate) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('iissdsss', $categoryId, $subCategoryId, $title, $description, $price, $providerName, $imageData, $certificateData);
    } elseif ($imageData !== null) {
        $stmt = $conn->prepare('INSERT INTO services (category_id, sub_category_id, title, description, price, provider_name, image) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('iissdss', $categoryId, $subCategoryId, $title, $description, $price, $providerName, $imageData);
    } elseif ($certificateData !== null) {
        $stmt = $conn->prepare('INSERT INTO services (category_id, sub_category_id, title, description, price, provider_name, certificate) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('iissdss', $categoryId, $subCategoryId, $title, $description, $price, $providerName, $certificateData);
    } else {
        $stmt = $conn->prepare('INSERT INTO services (category_id, sub_category_id, title, description, price, provider_name) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('iissds', $categoryId, $subCategoryId, $title, $description, $price, $providerName);
    }

    $stmt->execute();
    $stmt->close();
    $conn->close();
    header('Location: ' . appUrl('services?success=created'));
    exit();
}

function appDeleteServiceController(): void
{
    requireLogin();

    if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
        header('Location: ' . appUrl('services'));
        exit();
    }

    $id = (int) $_GET['id'];
    $conn = getConnection();
    deleteServiceById($conn, $id);
    $conn->close();

    header('Location: ' . appUrl('services?success=deleted'));
    exit();
}

function appImageController(): void
{
    if (!isset($_GET['id']) || !ctype_digit($_GET['id']) || !isset($_GET['type'])) {
        http_response_code(404);
        exit('Not Found');
    }

    $id = (int) $_GET['id'];
    $type = $_GET['type'];

    if (!in_array($type, ['image', 'certificate'], true)) {
        http_response_code(400);
        exit('Invalid Type');
    }

    $conn = getConnection();
    $imageData = fetchServiceBinaryByType($conn, $id, $type);
    $conn->close();

    if ($imageData === null || $imageData === '') {
        http_response_code(404);
        exit('Not Found');
    }

    $mimeTypes = [
        'ffd8ff' => 'image/jpeg',
        '89504e47' => 'image/png'
    ];

    $hex = bin2hex(substr($imageData, 0, 4));
    $contentType = 'image/jpeg';

    foreach ($mimeTypes as $magic => $mime) {
        if (strpos($hex, $magic) === 0) {
            $contentType = $mime;
            break;
        }
    }

    header('Content-Type: ' . $contentType);
    header('Content-Length: ' . strlen($imageData));
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Expires: 0');

    echo $imageData;
    exit();
}
