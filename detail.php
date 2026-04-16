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
        }
    </script>
</head>
<body class="bg-slate-100 min-h-screen text-slate-900">
<header class="border-b border-cyan-200 bg-white/85 backdrop-blur">
    <div class="mx-auto w-full max-w-6xl px-4 py-3 sm:px-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <a class="flex items-center gap-3" href="index.php">
                <img src="<?php echo assetPath('assets/logo.png'); ?>" alt="KOSERA" class="h-10 w-auto">
                <div class="hidden sm:flex sm:flex-col">
                    <strong class="text-sm">KOSERA</strong>
                    <span class="text-xs text-slate-500">Sahabat Bantuan Anak Kos</span>
                </div>
            </a>

            <div class="flex items-center gap-2">
                <?php if ($user !== null): ?>
                    <span class="rounded-full bg-cyan-100 px-3 py-1 text-xs font-semibold text-cyan-800 sm:text-sm">Halo, <?php echo htmlspecialchars($user['name']); ?></span>
                    <a href="logout.php" class="inline-flex rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100">Keluar</a>
                <?php else: ?>
                    <a href="login.php" class="inline-flex rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100">Masuk</a>
                    <a href="register.php" class="inline-flex rounded-lg bg-cyan-700 px-3 py-2 text-sm font-semibold text-white transition hover:bg-cyan-800">Daftar</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>

<main class="mx-auto w-full max-w-6xl px-4 py-6 sm:px-6">
    <div class="mb-4 flex flex-wrap items-end justify-between gap-3">
        <div>
            <span class="inline-flex rounded-full bg-cyan-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-cyan-800"><?php echo htmlspecialchars($service['category_name']); ?></span>
            <h1 class="mt-2 text-2xl font-bold text-slate-900 sm:text-3xl"><?php echo htmlspecialchars($service['title']); ?></h1>
        </div>
        <div class="rounded-xl bg-cyan-700 px-4 py-2 text-white">
            <strong>Rp <?php echo number_format((float) $service['price'], 0, ',', '.'); ?></strong>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <?php if (!empty($service['image'])): ?>
                <img src="image.php?id=<?php echo (int) $service['id']; ?>&type=image&v=<?php echo urlencode((string) $service['updated_at']); ?>" alt="<?php echo htmlspecialchars($service['title']); ?>" class="h-full min-h-[320px] w-full object-cover">
            <?php else: ?>
                <div class="flex min-h-[320px] items-center justify-center text-sm text-slate-500">
                    <span>Belum ada cover gambar.</span>
                </div>
            <?php endif; ?>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <p class="text-sm leading-7 text-slate-700"><?php echo nl2br(htmlspecialchars($service['description'])); ?></p>

            <div class="mt-4 grid grid-cols-1 gap-3 text-sm text-slate-700 sm:grid-cols-2">
                <div>
                    <p><strong>Mitra Penyedia:</strong><br><?php echo htmlspecialchars($service['provider_name']); ?></p>
                    <p><strong>Kategori:</strong><br><?php echo htmlspecialchars($service['category_name']); ?> - <?php echo htmlspecialchars($service['sub_category_name']); ?></p>
                </div>
                <div>
                    <p><strong>Tanggal Ditambahkan:</strong><br><?php echo date('d/m/Y H:i', strtotime($service['created_at'])); ?></p>
                </div>
            </div>

            <div class="mt-6">
                <h3 class="mb-2 text-lg font-semibold text-slate-900">Sertifikat</h3>
                <div class="h-[280px] overflow-hidden rounded-xl border border-slate-200 bg-slate-100">
                    <?php if (!empty($service['certificate'])): ?>
                        <img src="image.php?id=<?php echo (int) $service['id']; ?>&type=certificate&v=<?php echo urlencode((string) $service['updated_at']); ?>" alt="Sertifikat" class="h-full w-full object-cover">
                    <?php else: ?>
                        <div class="flex h-full items-center justify-center text-sm text-slate-500">
                            <span>Belum ada sertifikat yang diunggah.</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mt-6 flex flex-wrap gap-2">
                <?php if ($user !== null): ?>
                    <a href="form_jasa.php?id=<?php echo (int) $service['id']; ?>" class="inline-flex rounded-xl bg-cyan-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-cyan-800">Edit</a>
                    <a href="delete.php?id=<?php echo (int) $service['id']; ?>" class="inline-flex rounded-xl border border-rose-300 bg-rose-50 px-4 py-2.5 text-sm font-semibold text-rose-700 transition hover:bg-rose-100" onclick="return confirmDelete(event, '<?php echo htmlspecialchars(addslashes($service['title'])); ?>')">Hapus</a>
                <?php endif; ?>
                <a href="index.php" class="inline-flex rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">Kembali ke Daftar</a>
            </div>
        </div>
    </div>
</main>

<script src="assets/js/script.js"></script>
</body>
</html>
