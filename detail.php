<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/config/database.php';

if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$id = (int) $_GET['id'];
$conn = getConnection();
$user = currentUser();

$stmt = $conn->prepare('
    SELECT s.id, s.title, s.description, s.price, s.provider_name, s.image, s.certificate, s.created_at, s.updated_at,
           c.name AS category_name, sc.name AS sub_category_name
    FROM services s
    INNER JOIN categories c ON c.id = s.category_id
    INNER JOIN sub_categories sc ON sc.id = s.sub_category_id
    WHERE s.id = ?
');
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    header('Location: index.php');
    exit();
}

$service = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($service['title']); ?> - KOSERA</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="site-header">
    <div class="container">
        <div class="topbar">
            <a class="brand" href="index.php">
                <img src="<?php echo assetPath('assets/logo.png'); ?>" alt="KOSERA">
                <div class="brand-copy">
                    <strong>KOSERA</strong>
                    <span>Sahabat Bantuan Anak Kos</span>
                </div>
            </a>

            <div class="nav-actions">
                <?php if ($user !== null): ?>
                    <span class="badge">Halo, <?php echo htmlspecialchars($user['name']); ?></span>
                    <a class="btn btn-secondary" href="logout.php">Keluar</a>
                <?php else: ?>
                    <a class="btn btn-secondary" href="login.php">Masuk</a>
                    <a class="btn btn-primary" href="register.php">Daftar</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>

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
                <img src="image.php?id=<?php echo (int) $service['id']; ?>&type=image&v=<?php echo urlencode((string) $service['updated_at']); ?>" alt="<?php echo htmlspecialchars($service['title']); ?>" style="min-height: 320px;">
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
                        <img src="image.php?id=<?php echo (int) $service['id']; ?>&type=certificate&v=<?php echo urlencode((string) $service['updated_at']); ?>" alt="Sertifikat">
                    <?php else: ?>
                        <div class="panel" style="height: 100%; display: flex; align-items: center; justify-content: center; margin: 0; box-shadow: none; border: 0;">
                            <span style="color: #64748b;">Belum ada sertifikat yang diunggah.</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="actions" style="margin-top: 24px;">
                <?php if ($user !== null): ?>
                    <a class="btn btn-primary" href="form_jasa.php?id=<?php echo (int) $service['id']; ?>">Edit</a>
                    <a class="btn btn-danger" href="delete.php?id=<?php echo (int) $service['id']; ?>" onclick="return confirmDelete(event, '<?php echo htmlspecialchars(addslashes($service['title'])); ?>')">Hapus</a>
                <?php endif; ?>
                <a class="btn btn-secondary" href="index.php">Kembali ke Daftar</a>
            </div>
        </div>
    </div>
</main>

<script src="assets/js/script.js"></script>
</body>
</html>
