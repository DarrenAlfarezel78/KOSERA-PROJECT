<?php
$pageTitle = 'KOSERA - Daftar Jasa';
require __DIR__ . '/../partials/head.php';
require __DIR__ . '/../partials/header-home.php';
?>

<main class="container">
    <?php if ($successMessage !== ''): ?>
        <div class="success-box"><?php echo htmlspecialchars($successMessage); ?></div>
    <?php endif; ?>

    <div class="toolbar">
        <div class="left">
            <label for="searchFilter">Cari Jasa:</label>
            <input
                type="search"
                id="searchFilter"
                placeholder="Cari judul, deskripsi, kategori, atau mitra..."
                oninput="applySearchFilter()"
            >
        </div>
        <div class="right">
            <a class="btn btn-primary" href="<?php echo appUrl('services/create'); ?>">+ Tambah Jasa</a>
        </div>
    </div>

    <div class="table-responsive">
        <?php if (empty($services)): ?>
            <div class="panel">Belum ada data jasa.</div>
        <?php else: ?>
            <table class="service-table" id="serviceContainer">
                <thead>
                    <tr>
                        <th>Gambar</th>
                        <th>Jasa</th>
                        <th>Kategori</th>
                        <th>Mitra</th>
                        <th>Harga</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($services as $service): ?>
                        <tr class="service-row">
                            <td data-label="Gambar">
                                <div class="table-image-wrap">
                                    <?php if (!empty($service['image'])): ?>
                                        <img src="<?php echo appUrl('services/image/' . (int) $service['id'] . '/image?v=' . urlencode((string) $service['updated_at'])); ?>" alt="<?php echo htmlspecialchars($service['title']); ?>">
                                    <?php else: ?>
                                        <span>Tidak ada gambar</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td data-label="Jasa">
                                <a href="<?php echo appUrl('services/show/' . (int) $service['id']); ?>" class="service-link">
                                    <strong><?php echo htmlspecialchars($service['title']); ?></strong>
                                    <span><?php echo htmlspecialchars($service['description']); ?></span>
                                </a>
                            </td>
                            <td data-label="Kategori"><?php echo htmlspecialchars($service['category_name']); ?><br><small><?php echo htmlspecialchars($service['sub_category_name']); ?></small></td>
                            <td data-label="Mitra"><?php echo htmlspecialchars($service['provider_name']); ?></td>
                            <td data-label="Harga" class="price">Rp <?php echo number_format((float) $service['price'], 0, ',', '.'); ?></td>
                            <td data-label="Aksi">
                                <div class="table-actions">
                                                <a class="btn btn-secondary" href="<?php echo appUrl('services/show/' . (int) $service['id']); ?>">Detail</a>
                                                <a class="btn btn-secondary" href="<?php echo appUrl('services/edit/' . (int) $service['id']); ?>">Edit</a>
                                    <a class="btn btn-danger"
                                                    href="<?php echo appUrl('services/delete/' . (int) $service['id']); ?>"
                                       onclick="return confirmDelete(event, '<?php echo htmlspecialchars(addslashes($service['title'])); ?>')">Hapus</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</main>

<script src="assets/js/script.js"></script>
<script>
    applySearchFilter();
</script>
<?php require __DIR__ . '/../partials/end.php'; ?>
