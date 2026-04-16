<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/config/database.php';

requireLogin();

$conn = getConnection();
$user = currentUser();

$flashSuccess = flashGet('success');
$success = isset($_GET['success']) ? trim($_GET['success']) : '';

$sql = "SELECT s.id, s.title, s.description, s.price, s.provider_name,
           s.image,
           s.updated_at,
               c.id AS category_id, c.name AS category_name,
               sc.name AS sub_category_name
        FROM services s
        INNER JOIN categories c ON c.id = s.category_id
        INNER JOIN sub_categories sc ON sc.id = s.sub_category_id";

$sql .= ' ORDER BY s.created_at DESC';

$stmt = $conn->prepare($sql);
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
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#ecfeff',
                            100: '#cffafe',
                            600: '#0891b2',
                            700: '#0e7490',
                            800: '#155e75'
                        }
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
            <a href="index.php" class="inline-flex items-center">
                <img src="<?php echo assetPath('assets/logo.png'); ?>" alt="KOSERA" class="h-9 w-auto sm:h-10">
            </a>

            <div class="flex items-center gap-2">
                <span class="rounded-full bg-cyan-100 px-3 py-1 text-xs font-semibold text-cyan-800 sm:text-sm">Halo, <?php echo htmlspecialchars($user['name']); ?></span>
                <a href="logout.php" class="inline-flex items-center rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100">Keluar</a>
            </div>
        </div>
    </div>
</header>

<main class="mx-auto w-full max-w-6xl px-4 py-6 sm:px-6">
    <?php if ($successMessage !== ''): ?>
        <div class="mb-4 rounded-xl border border-emerald-300 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800"><?php echo htmlspecialchars($successMessage); ?></div>
    <?php endif; ?>

    <div class="mb-4 flex flex-col gap-3 rounded-2xl border border-cyan-100 bg-white/90 p-4 shadow-sm sm:flex-row sm:items-end sm:justify-between">
        <div class="w-full sm:max-w-xl">
            <label for="searchFilter" class="mb-2 block text-sm font-semibold text-slate-700">Cari Jasa:</label>
            <input
                type="search"
                id="searchFilter"
                placeholder="Cari judul, deskripsi, kategori, atau mitra..."
                oninput="applySearchFilter()"
                class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm outline-none ring-cyan-300 transition focus:ring"
            >
        </div>
        <div>
            <a href="form_jasa.php" class="inline-flex items-center justify-center rounded-xl bg-brand-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-800">+ Tambah Jasa</a>
        </div>
    </div>

    <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm">
        <?php if (empty($services)): ?>
            <div class="px-5 py-8 text-center text-sm text-slate-600">Belum ada data jasa.</div>
        <?php else: ?>
            <table class="min-w-full text-sm" id="serviceContainer">
                <thead class="bg-slate-50 text-slate-700">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold">Gambar</th>
                        <th class="px-4 py-3 text-left font-semibold">Jasa</th>
                        <th class="px-4 py-3 text-left font-semibold">Kategori</th>
                        <th class="px-4 py-3 text-left font-semibold">Mitra</th>
                        <th class="px-4 py-3 text-left font-semibold">Harga</th>
                        <th class="px-4 py-3 text-left font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php foreach ($services as $service): ?>
                        <tr class="service-row hover:bg-cyan-50/50">
                            <td data-label="Gambar" class="px-4 py-3 align-top">
                                <div class="flex h-16 w-20 items-center justify-center overflow-hidden rounded-lg bg-slate-100 text-[11px] text-slate-500">
                                    <?php if (!empty($service['image'])): ?>
                                        <img src="image.php?id=<?php echo (int) $service['id']; ?>&type=image&v=<?php echo urlencode((string) $service['updated_at']); ?>" alt="<?php echo htmlspecialchars($service['title']); ?>" class="h-full w-full object-cover">
                                    <?php else: ?>
                                        <span>Tidak ada gambar</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td data-label="Jasa" class="px-4 py-3 align-top">
                                <a href="detail.php?id=<?php echo (int) $service['id']; ?>" class="flex flex-col gap-1 text-slate-900">
                                    <strong class="font-semibold"><?php echo htmlspecialchars($service['title']); ?></strong>
                                    <span class="line-clamp-2 text-xs text-slate-600"><?php echo htmlspecialchars($service['description']); ?></span>
                                </a>
                            </td>
                            <td data-label="Kategori" class="px-4 py-3 align-top"><?php echo htmlspecialchars($service['category_name']); ?><br><small class="text-slate-500"><?php echo htmlspecialchars($service['sub_category_name']); ?></small></td>
                            <td data-label="Mitra" class="px-4 py-3 align-top"><?php echo htmlspecialchars($service['provider_name']); ?></td>
                            <td data-label="Harga" class="px-4 py-3 align-top font-semibold text-brand-700">Rp <?php echo number_format((float) $service['price'], 0, ',', '.'); ?></td>
                            <td data-label="Aksi" class="px-4 py-3 align-top">
                                <div class="flex flex-wrap gap-2">
                                    <a href="detail.php?id=<?php echo (int) $service['id']; ?>" class="inline-flex rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-100">Detail</a>
                                    <a href="form_jasa.php?id=<?php echo (int) $service['id']; ?>" class="inline-flex rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-100">Edit</a>
                                    <a
                                       href="delete.php?id=<?php echo (int) $service['id']; ?>"
                                       class="inline-flex rounded-lg border border-rose-300 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700 transition hover:bg-rose-100"
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
</body>
</html>
<?php
$stmt->close();
$conn->close();
