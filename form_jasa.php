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
        header('Location: index.php?page=services');
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
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: '#0a7ea2',
                        brandDark: '#06546f',
                    }
                }
            }
        };
    </script>
</head>
<body class="min-h-screen bg-gradient-to-br from-cyan-50 via-slate-50 to-sky-100 text-slate-900">
<header class="border-b border-cyan-200 bg-white/85 backdrop-blur">
    <div class="mx-auto w-full max-w-6xl px-4 py-3 sm:px-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <a href="index.php?page=services" class="flex items-center gap-3">
                <img src="<?php echo assetPath('assets/logo.png'); ?>" alt="KOSERA" class="h-9 w-auto sm:h-10">
                <div class="hidden sm:flex sm:flex-col">
                    <strong class="text-sm">KOSERA</strong>
                    <span class="text-xs text-slate-500">Sahabat Bantuan Anak Kos</span>
                </div>
            </a>

            <div class="flex items-center gap-2">
                <span class="rounded-full bg-cyan-100 px-3 py-1 text-xs font-semibold text-cyan-800 sm:text-sm">Halo, <?php echo htmlspecialchars($user['name']); ?></span>
                <a href="index.php?page=services" class="inline-flex rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100">Kembali</a>
                <a href="logout.php" class="inline-flex rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100">Keluar</a>
            </div>
        </div>
    </div>
</header>

<main class="mx-auto w-full max-w-6xl px-4 py-6 sm:px-6">
    <div class="mb-4">
        <h1 class="text-2xl font-bold text-slate-900"><?php echo $isEdit ? 'Edit Jasa Mitra' : 'Tambah Jasa Mitra'; ?></h1>
        <p class="mt-1 text-sm text-slate-600">Lengkapi data jasa dengan rapi dan konsisten.</p>
    </div>

    <?php if ($error !== ''): ?>
        <div class="mb-4 rounded-xl border border-rose-300 bg-rose-50 px-4 py-3 text-sm text-rose-800"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <form method="post" action="save_service.php" id="serviceForm" enctype="multipart/form-data" onsubmit="return validateServiceForm(this)">
            <input type="hidden" name="id" value="<?php echo (int) $formData['id']; ?>">

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label for="title" class="mb-1 block text-sm font-semibold text-slate-700">Judul Jasa</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars((string) $formData['title']); ?>" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none ring-cyan-300 focus:ring">
                    <div class="field-error mt-1 text-xs text-rose-600" id="err-title"></div>
                </div>

                <div>
                    <label for="category_id" class="mb-1 block text-sm font-semibold text-slate-700">Kategori</label>
                    <select id="category_id" name="category_id" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none ring-cyan-300 focus:ring">
                        <option value="">Pilih Kategori</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo (int) $category['id']; ?>" <?php echo ((int) $formData['category_id'] === (int) $category['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="field-error mt-1 text-xs text-rose-600" id="err-category_id"></div>
                </div>

                <div>
                    <label for="sub_category_id" class="mb-1 block text-sm font-semibold text-slate-700">Sub-Kategori</label>
                    <select id="sub_category_id" name="sub_category_id" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none ring-cyan-300 focus:ring">
                        <option value="">Pilih Sub-Kategori</option>
                        <?php foreach ($subCategories as $subCategory): ?>
                            <option value="<?php echo (int) $subCategory['id']; ?>" data-category="<?php echo (int) $subCategory['category_id']; ?>" <?php echo ((int) $formData['sub_category_id'] === (int) $subCategory['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($subCategory['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="field-error mt-1 text-xs text-rose-600" id="err-sub_category_id"></div>
                </div>

                <div>
                    <label for="price" class="mb-1 block text-sm font-semibold text-slate-700">Harga (angka)</label>
                    <input type="text" id="price" name="price" value="<?php echo htmlspecialchars((string) $formData['price']); ?>" placeholder="contoh: 75000" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none ring-cyan-300 focus:ring">
                    <div class="field-error mt-1 text-xs text-rose-600" id="err-price"></div>
                </div>

                <div>
                    <label for="provider_name" class="mb-1 block text-sm font-semibold text-slate-700">Nama Mitra</label>
                    <input type="text" id="provider_name" name="provider_name" value="<?php echo htmlspecialchars((string) $formData['provider_name']); ?>" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none ring-cyan-300 focus:ring">
                    <div class="field-error mt-1 text-xs text-rose-600" id="err-provider_name"></div>
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="mb-1 block text-sm font-semibold text-slate-700">Deskripsi Jasa</label>
                    <textarea id="description" name="description" rows="5" class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none ring-cyan-300 focus:ring"><?php echo htmlspecialchars((string) $formData['description']); ?></textarea>
                    <div class="field-error mt-1 text-xs text-rose-600" id="err-description"></div>
                </div>

                <div>
                    <label for="image" class="mb-1 block text-sm font-semibold text-slate-700">Logo/Cover Jasa (JPG/PNG, max 2MB)</label>
                    <input type="file" id="image" name="image" accept="image/jpeg,image/png" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm">
                    <small class="mt-1 block text-xs text-slate-500">Upload gambar untuk ditampilkan di homepage.</small>
                    <div class="mt-3 hidden w-full max-w-xs overflow-hidden rounded-xl border border-cyan-200 bg-slate-100" id="imagePreviewContainer">
                        <img id="imagePreview" src="" alt="Preview" class="h-44 w-full object-cover">
                    </div>
                    <div class="field-error mt-1 text-xs text-rose-600" id="err-image"></div>
                </div>

                <div>
                    <label for="certificate" class="mb-1 block text-sm font-semibold text-slate-700">Foto Sertifikat (JPG/PNG, max 2MB)</label>
                    <input type="file" id="certificate" name="certificate" accept="image/jpeg,image/png" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm">
                    <small class="mt-1 block text-xs text-slate-500">Upload sertifikat untuk ditampilkan di halaman detail.</small>
                    <div class="mt-3 hidden w-full max-w-xs overflow-hidden rounded-xl border border-cyan-200 bg-slate-100" id="certificatePreviewContainer">
                        <img id="certificatePreview" src="" alt="Preview" class="h-44 w-full object-cover">
                    </div>
                    <div class="field-error mt-1 text-xs text-rose-600" id="err-certificate"></div>
                </div>
            </div>

            <div class="mt-5 flex flex-wrap gap-2">
                <button type="submit" class="inline-flex rounded-xl bg-cyan-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-cyan-800"><?php echo $isEdit ? 'Simpan Perubahan' : 'Simpan Jasa'; ?></button>
                <a href="index.php?page=services" class="inline-flex rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">Kembali</a>
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

    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('imagePreview');
    const imagePreviewContainer = document.getElementById('imagePreviewContainer');

    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                imagePreview.src = event.target.result;
                imagePreviewContainer.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            imagePreviewContainer.style.display = 'none';
        }
    });

    const certificateInput = document.getElementById('certificate');
    const certificatePreview = document.getElementById('certificatePreview');
    const certificatePreviewContainer = document.getElementById('certificatePreviewContainer');

    certificateInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                certificatePreview.src = event.target.result;
                certificatePreviewContainer.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            certificatePreviewContainer.style.display = 'none';
        }
    });

    <?php if ($isEdit && !empty($formData['image'])): ?>
        document.getElementById('imagePreviewContainer').style.display = 'block';
        document.getElementById('imagePreview').src = '<?php echo appUrl('services/image/' . (int) $formData['id'] . '/image?v=' . rawurlencode((string) $formData['updated_at'])); ?>';
    <?php endif; ?>

    <?php if ($isEdit && !empty($formData['certificate'])): ?>
        document.getElementById('certificatePreviewContainer').style.display = 'block';
        document.getElementById('certificatePreview').src = '<?php echo appUrl('services/image/' . (int) $formData['id'] . '/certificate?v=' . rawurlencode((string) $formData['updated_at'])); ?>';
    <?php endif; ?>
</script>
</body>
</html>
<?php
$conn->close();
