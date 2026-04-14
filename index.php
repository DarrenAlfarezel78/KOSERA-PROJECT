<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/config/database.php';

requireLogin();

$conn = getConnection();
$user = currentUser();

$categoryFilter = isset($_GET['category_id']) ? (int) $_GET['category_id'] : 0;
$flashSuccess = flashGet('success');
$success = isset($_GET['success']) ? trim($_GET['success']) : '';

$categories = [];
$catResult = $conn->query('SELECT id, name FROM categories ORDER BY name ASC');
while ($row = $catResult->fetch_assoc()) {
    $categories[] = $row;
}

$sql = "SELECT s.id, s.title, s.description, s.price, s.provider_name,
           s.image,
           s.updated_at,
               c.id AS category_id, c.name AS category_name,
               sc.name AS sub_category_name
        FROM services s
        INNER JOIN categories c ON c.id = s.category_id
        INNER JOIN sub_categories sc ON sc.id = s.sub_category_id";
$params = [];
$types = '';

if ($categoryFilter > 0) {
    $sql .= ' WHERE s.category_id = ?';
    $types = 'i';
    $params[] = $categoryFilter;
}

$sql .= ' ORDER BY s.created_at DESC';

$stmt = $conn->prepare($sql);
if ($types !== '') {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$services = $result->fetch_all(MYSQLI_ASSOC);

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
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KOSERA - Daftar Jasa</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="header simple-home-header">
    <div class="container">
        <div class="simple-topbar">
            <a class="brand" href="index.php">
                <img src="<?php echo assetPath('assets/logo.png'); ?>" alt="KOSERA">
            </a>

            <div class="nav-actions">
                <span class="badge">Halo, <?php echo htmlspecialchars($user['name']); ?></span>
                <a class="btn btn-secondary" href="logout.php">Keluar</a>
            </div>
        </div>
    </div>
</header>

<main class="container">
    <?php if ($successMessage !== ''): ?>
        <div class="success-box"><?php echo htmlspecialchars($successMessage); ?></div>
    <?php endif; ?>

    <div class="toolbar">
        <div class="left">
            <label for="categoryFilter">Filter Kategori:</label>
            <select id="categoryFilter" name="category_id" onchange="applyCategoryFilter()">
                <option value="all">Semua Kategori</option>
                <?php foreach ($categories as $category): ?>
                    <option
                        value="<?php echo (int) $category['id']; ?>"
                        <?php echo ($categoryFilter === (int) $category['id']) ? 'selected' : ''; ?>
                    >
                        <?php echo htmlspecialchars($category['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="right">
            <a class="btn btn-primary" href="form_jasa.php">+ Tambah Jasa</a>
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
                        <tr class="service-row" data-category-id="<?php echo (int) $service['category_id']; ?>">
                            <td>
                                <div class="table-image-wrap">
                                    <?php if (!empty($service['image'])): ?>
                                        <img src="image.php?id=<?php echo (int) $service['id']; ?>&type=image&v=<?php echo urlencode((string) $service['updated_at']); ?>" alt="<?php echo htmlspecialchars($service['title']); ?>">
                                    <?php else: ?>
                                        <span>Tidak ada gambar</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <a href="detail.php?id=<?php echo (int) $service['id']; ?>" class="service-link">
                                    <strong><?php echo htmlspecialchars($service['title']); ?></strong>
                                    <span><?php echo htmlspecialchars($service['description']); ?></span>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($service['category_name']); ?><br><small><?php echo htmlspecialchars($service['sub_category_name']); ?></small></td>
                            <td><?php echo htmlspecialchars($service['provider_name']); ?></td>
                            <td class="price">Rp <?php echo number_format((float) $service['price'], 0, ',', '.'); ?></td>
                            <td>
                                <div class="table-actions">
                                    <a class="btn btn-secondary" href="detail.php?id=<?php echo (int) $service['id']; ?>">Detail</a>
                                    <a class="btn btn-secondary" href="form_jasa.php?id=<?php echo (int) $service['id']; ?>">Edit</a>
                                    <a class="btn btn-danger"
                                       href="delete.php?id=<?php echo (int) $service['id']; ?>"
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
    applyCategoryFilter();
</script>
</body>
</html>
<?php
$stmt->close();
$conn->close();
