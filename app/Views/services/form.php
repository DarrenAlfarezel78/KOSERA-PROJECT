<?php
$pageTitle = ($isEdit ? 'Edit Jasa' : 'Tambah Jasa') . ' - KOSERA';
require __DIR__ . '/../partials/head.php';
require __DIR__ . '/../partials/header-service.php';
?>

<main class="container">
    <div class="form-header">
        <h1><?php echo $isEdit ? 'Edit Jasa Mitra' : 'Tambah Jasa Mitra'; ?></h1>
        <p>Lengkapi data jasa dengan rapi dan konsisten.</p>
    </div>

    <?php if ($error !== ''): ?>
        <div class="error-box"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="panel">
        <form method="post" action="<?php echo appUrl('services/store'); ?>" id="serviceForm" enctype="multipart/form-data" onsubmit="return validateServiceForm(this)">
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
                    <div class="image-preview-container" id="imagePreviewContainer" style="display: none; margin-top: 10px;">
                        <img id="imagePreview" src="" alt="Preview" class="image-preview">
                    </div>
                    <div class="field-error" id="err-image"></div>
                </div>

                <div>
                    <label for="certificate">Foto Sertifikat (JPG/PNG, max 2MB)</label>
                    <input type="file" id="certificate" name="certificate" accept="image/jpeg,image/png">
                    <small>Upload sertifikat untuk ditampilkan di halaman detail.</small>
                    <div class="image-preview-container" id="certificatePreviewContainer" style="display: none; margin-top: 10px;">
                        <img id="certificatePreview" src="" alt="Preview" class="image-preview">
                    </div>
                    <div class="field-error" id="err-certificate"></div>
                </div>
            </div>

            <div class="actions">
                <button type="submit" class="btn btn-primary"><?php echo $isEdit ? 'Simpan Perubahan' : 'Simpan Jasa'; ?></button>
                <a href="<?php echo appUrl('services'); ?>" class="btn btn-secondary">Kembali</a>
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
<?php require __DIR__ . '/../partials/end.php'; ?>
