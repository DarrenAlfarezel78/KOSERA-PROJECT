<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/config/database.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
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

if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
    if ($_FILES['image']['size'] > $maxFileSize) {
        $errors[] = 'Gambar logo/cover terlalu besar (max 2MB).';
    } elseif (!in_array($_FILES['image']['type'], ['image/jpeg', 'image/png'], true)) {
        $errors[] = 'Format gambar harus JPG atau PNG.';
    } else {
        $imageData = file_get_contents($_FILES['image']['tmp_name']);
        if ($imageData === false) {
            $errors[] = 'Gagal membaca file gambar.';
        }
    }
}

if (isset($_FILES['certificate']) && $_FILES['certificate']['size'] > 0) {
    if ($_FILES['certificate']['size'] > $maxFileSize) {
        $errors[] = 'Gambar sertifikat terlalu besar (max 2MB).';
    } elseif (!in_array($_FILES['certificate']['type'], ['image/jpeg', 'image/png'], true)) {
        $errors[] = 'Format sertifikat harus JPG atau PNG.';
    } else {
        $certificateData = file_get_contents($_FILES['certificate']['tmp_name']);
        if ($certificateData === false) {
            $errors[] = 'Gagal membaca file sertifikat.';
        }
    }
}

$title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
$description = htmlspecialchars($description, ENT_QUOTES, 'UTF-8');
$providerName = htmlspecialchars($providerName, ENT_QUOTES, 'UTF-8');
$price = (float) $priceRaw;

$checkRel = $conn->prepare('SELECT id FROM sub_categories WHERE id = ? AND category_id = ?');
$checkRel->bind_param('ii', $subCategoryId, $categoryId);
$checkRel->execute();
$checkRelResult = $checkRel->get_result();
if ($checkRelResult->num_rows !== 1) {
    $errors[] = 'Sub-kategori tidak sesuai dengan kategori.';
}
$checkRel->close();

if (!empty($errors)) {
    $message = urlencode(implode(' ', $errors));
    if ($id > 0) {
        header('Location: form_jasa.php?id=' . $id . '&error=' . $message);
    } else {
        header('Location: form_jasa.php?error=' . $message);
    }
    exit();
}

if ($id > 0) {
    if ($imageData !== null && $certificateData !== null) {
        $stmt = $conn->prepare('UPDATE services SET category_id = ?, sub_category_id = ?, title = ?, description = ?, price = ?, provider_name = ?, image = ?, certificate = ? WHERE id = ?');
        $stmt->bind_param('iissdssssi', $categoryId, $subCategoryId, $title, $description, $price, $providerName, $imageData, $certificateData, $id);
        $stmt->execute();
        $stmt->close();
    } elseif ($imageData !== null) {
        $stmt = $conn->prepare('UPDATE services SET category_id = ?, sub_category_id = ?, title = ?, description = ?, price = ?, provider_name = ?, image = ? WHERE id = ?');
        $stmt->bind_param('iissdsssi', $categoryId, $subCategoryId, $title, $description, $price, $providerName, $imageData, $id);
        $stmt->execute();
        $stmt->close();
    } elseif ($certificateData !== null) {
        $stmt = $conn->prepare('UPDATE services SET category_id = ?, sub_category_id = ?, title = ?, description = ?, price = ?, provider_name = ?, certificate = ? WHERE id = ?');
        $stmt->bind_param('iissdssssi', $categoryId, $subCategoryId, $title, $description, $price, $providerName, $certificateData, $id);
        $stmt->execute();
        $stmt->close();
    } else {
        $stmt = $conn->prepare('UPDATE services SET category_id = ?, sub_category_id = ?, title = ?, description = ?, price = ?, provider_name = ? WHERE id = ?');
        $stmt->bind_param('iissdsi', $categoryId, $subCategoryId, $title, $description, $price, $providerName, $id);
        $stmt->execute();
        $stmt->close();
    }
    $conn->close();

    header('Location: index.php?success=updated');
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

header('Location: index.php?success=created');
exit();
