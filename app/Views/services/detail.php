<?php
$pageTitle = $service['title'] . ' - KOSERA';
require __DIR__ . '/../partials/head.php';
require __DIR__ . '/../partials/header-service.php';
?>

<main class="container">
    <div class="detail-header">
        <div>
            <span class="eyebrow-detail"><?php echo htmlspecialchars($service['category_name']); ?></span>
            <h1><?php echo htmlspecialchars($service['title']); ?></h1>
        </div>
        <div class="detail-price">
            <strong>Rp <?php echo number_format((float) $service['price'], 0, ',', '.'); ?></strong>
        </div>
    </div>

    <div class="detail-layout">
        <div class="detail-media">
            <?php if (!empty($service['image'])): ?>
                <img src="<?php echo appUrl('services/image/' . (int) $service['id'] . '/image?v=' . urlencode((string) $service['updated_at'])); ?>" alt="<?php echo htmlspecialchars($service['title']); ?>" style="min-height: 320px;">
            <?php else: ?>
                <div class="panel" style="min-height: 320px; display: flex; align-items: center; justify-content: center; margin: 0; box-shadow: none; border: 0;">
                    <span style="color: #64748b;">Belum ada cover gambar.</span>
                </div>
            <?php endif; ?>
        </div>

        <div class="panel detail-meta">
            <p><?php echo nl2br(htmlspecialchars($service['description'])); ?></p>

            <div class="detail-grid">
                <div>
                    <p><strong>Mitra Penyedia:</strong><br><?php echo htmlspecialchars($service['provider_name']); ?></p>
                    <p><strong>Kategori:</strong><br><?php echo htmlspecialchars($service['category_name']); ?> - <?php echo htmlspecialchars($service['sub_category_name']); ?></p>
                </div>
                <div>
                    <p><strong>Tanggal Ditambahkan:</strong><br><?php echo date('d/m/Y H:i', strtotime($service['created_at'])); ?></p>
                </div>
            </div>

            <div style="margin-top: 24px;">
                <h3>Sertifikat</h3>
                <div class="detail-spotlight" style="height: 280px;">
                    <?php if (!empty($service['certificate'])): ?>
                        <img src="<?php echo appUrl('services/image/' . (int) $service['id'] . '/certificate?v=' . urlencode((string) $service['updated_at'])); ?>" alt="Sertifikat">
                    <?php else: ?>
                        <div class="panel" style="height: 100%; display: flex; align-items: center; justify-content: center; margin: 0; box-shadow: none; border: 0;">
                            <span style="color: #64748b;">Belum ada sertifikat yang diunggah.</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="actions" style="margin-top: 24px;">
                <?php if ($user !== null): ?>
                    <a class="btn btn-primary" href="<?php echo appUrl('services/edit/' . (int) $service['id']); ?>">Edit</a>
                    <a class="btn btn-danger" href="<?php echo appUrl('services/delete/' . (int) $service['id']); ?>" onclick="return confirmDelete(event, '<?php echo htmlspecialchars(addslashes($service['title'])); ?>')">Hapus</a>
                <?php endif; ?>
                <a class="btn btn-secondary" href="<?php echo appUrl('services'); ?>">Kembali ke Daftar</a>
            </div>
        </div>
    </div>
</main>

<script src="assets/js/script.js"></script>
<?php require __DIR__ . '/../partials/end.php'; ?>
