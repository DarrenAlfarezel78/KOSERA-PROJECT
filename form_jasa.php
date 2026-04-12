<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/config/database.php';

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
    $stmt = $conn->prepare('SELECT id, title, description, price, provider_name, category_id, sub_category_id FROM services WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $formData = $result->fetch_assoc();
    } else {
        header('Location: index.php');
        exit();
    }
    $stmt->close();
}

$categories = [];
$catResult = $conn->query('SELECT id, name FROM categories ORDER BY name ASC');
while ($row = $catResult->fetch_assoc()) {
    $categories[] = $row;
}

$subCategories = [];
$subResult = $conn->query('SELECT id, category_id, name FROM sub_categories ORDER BY name ASC');
while ($row = $subResult->fetch_assoc()) {
    $subCategories[] = $row;
}

$error = isset($_GET['error']) ? trim($_GET['error']) : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $isEdit ? 'Edit Jasa' : 'Tambah Jasa'; ?> - KOSERA</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="site-header">
    <div class="container">
        <div class="topbar">
            <a class="brand" href="index.php">
                <img src="<?php echo assetPath('assets/image 4 (2) 2.png'); ?>" alt="KOSERA">
                <div class="brand-copy">
                    <strong>KOSERA</strong>
                    <span>Sahabat Bantuan Anak Kos</span>
                </div>
            </a>

            <div class="nav-actions">
                <span class="badge">Halo, <?php echo htmlspecialchars($user['name']); ?></span>
                <a class="btn btn-secondary" href="index.php">Kembali</a>
                <a class="btn btn-secondary" href="logout.php">Keluar</a>
            </div>
        </div>

        <div class="hero" style="margin-top: 24px; grid-template-columns: 1.2fr 0.8fr;">
            <div class="hero-copy">
                <span class="eyebrow"><?php echo $isEdit ? 'Edit Jasa Mitra' : 'Tambah Jasa Mitra'; ?></span>
                <h1>Lengkapi data jasa dengan rapi dan konsisten.</h1>
                <p>Form ini tetap memakai warna brand KOSERA dan hanya bisa diakses setelah login.</p>
            </div>
            <div class="hero-panel">
                <img src="<?php echo assetPath('assets/image 4 (2) 2.png'); ?>" alt="Logo KOSERA">
                <div class="stat-card">
                    <strong><?php echo $isEdit ? 'Edit Mode' : 'Buat Baru'; ?></strong>
                    <span>Mode formulir</span>
                </div>
            </div>
        </div>
    </div>
</header>

<main class="container">
    <?php if ($error !== ''): ?>
        <div class="error-box"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="panel">
        <form method="post" action="save_service.php" id="serviceForm" enctype="multipart/form-data" onsubmit="return validateServiceForm(this)">
            <input type="hidden" name="id" value="<?php echo (int) $formData['id']; ?>">

            <div class="form-grid">
                <div class="full">
                    <label for="title">Judul Jasa</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars((string) $formData['title']); ?>">
                    <div class="field-error" id="err-title"></div>
                </div>

                <div>
                    <label for="category_id">Kategori</label>
                    <select id="category_id" name="category_id">
                        <option value="">Pilih Kategori</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo (int) $category['id']; ?>" <?php echo ((int) $formData['category_id'] === (int) $category['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="field-error" id="err-category_id"></div>
                </div>

                <div>
                    <label for="sub_category_id">Sub-Kategori</label>
                    <select id="sub_category_id" name="sub_category_id">
                        <option value="">Pilih Sub-Kategori</option>
                        <?php foreach ($subCategories as $subCategory): ?>
                            <option value="<?php echo (int) $subCategory['id']; ?>" data-category="<?php echo (int) $subCategory['category_id']; ?>" <?php echo ((int) $formData['sub_category_id'] === (int) $subCategory['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($subCategory['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="field-error" id="err-sub_category_id"></div>
                </div>

                <div>
                    <label for="price">Harga (angka)</label>
                    <input type="text" id="price" name="price" value="<?php echo htmlspecialchars((string) $formData['price']); ?>" placeholder="contoh: 75000">
                    <div class="field-error" id="err-price"></div>
                </div>

                <div>
                    <label for="provider_name">Nama Mitra</label>
                    <input type="text" id="provider_name" name="provider_name" value="<?php echo htmlspecialchars((string) $formData['provider_name']); ?>">
                    <div class="field-error" id="err-provider_name"></div>
                </div>

                <div class="full">
                    <label for="description">Deskripsi Jasa</label>
                    <textarea id="description" name="description" rows="5"><?php echo htmlspecialchars((string) $formData['description']); ?></textarea>
                    <div class="field-error" id="err-description"></div>
                </div>

                <div>
                    <label for="image">Logo/Cover Jasa (JPG/PNG, max 2MB)</label>
                    <input type="file" id="image" name="image" accept="image/jpeg,image/png">
                    <small>Upload gambar untuk ditampilkan di homepage.</small>
                    <div class="field-error" id="err-image"></div>
                </div>

                <div>
                    <label for="certificate">Foto Sertifikat (JPG/PNG, max 2MB)</label>
                    <input type="file" id="certificate" name="certificate" accept="image/jpeg,image/png">
                    <small>Upload sertifikat untuk ditampilkan di halaman detail.</small>
                    <div class="field-error" id="err-certificate"></div>
                </div>
            </div>

            <div class="actions">
                <button type="submit" class="btn btn-primary"><?php echo $isEdit ? 'Simpan Perubahan' : 'Simpan Jasa'; ?></button>
                <a href="index.php" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
</main>

<script src="assets/js/script.js"></script>
<script>
    function syncSubCategoryByCategory() {
        const category = document.getElementById('category_id');
        const subCategory = document.getElementById('sub_category_id');
        const selectedCategory = category.value;

        Array.from(subCategory.options).forEach((option, idx) => {
            if (idx === 0) {
                option.hidden = false;
                return;
            }
            const belongsTo = option.getAttribute('data-category');
            option.hidden = selectedCategory && belongsTo !== selectedCategory;
        });

        const current = subCategory.options[subCategory.selectedIndex];
        if (current && current.hidden) {
            subCategory.value = '';
        }
    }

    document.getElementById('category_id').addEventListener('change', syncSubCategoryByCategory);
    syncSubCategoryByCategory();
</script>
</body>
</html>
<?php
$conn->close();
