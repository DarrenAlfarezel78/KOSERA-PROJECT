<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/config/database.php';

redirectIfLoggedIn();

$conn = getConnection();
$error = '';
$success = flashGet('success');
$redirectTarget = safeRedirectTarget($_GET['redirect'] ?? 'index.php');

$userColumns = [];
$columnResult = $conn->query('SHOW COLUMNS FROM users');
if ($columnResult) {
    while ($column = $columnResult->fetch_assoc()) {
        $userColumns[] = $column['Field'];
    }
}

$usesLegacySchema = in_array('nama_panjang', $userColumns, true)
    && in_array('nomor_telepon', $userColumns, true)
    && in_array('password', $userColumns, true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $redirectTarget = safeRedirectTarget($_POST['redirect'] ?? 'index.php');

    if ($name === '') {
        $error = 'Nama lengkap wajib diisi.';
    } elseif ($phone === '') {
        $error = 'Nomor telepon wajib diisi.';
    } elseif ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Email harus valid.';
    } elseif (strlen($password) < 8) {
        $error = 'Kata sandi minimal 8 karakter.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Konfirmasi kata sandi tidak cocok.';
    } else {
        $stmt = $conn->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        if (!$stmt) {
            error_log('Register check prepare failed: ' . $conn->error);
            $error = 'Terjadi masalah sistem saat registrasi. Silakan coba lagi.';
            $exists = true;
        } else {
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $exists = $result->num_rows > 0;
            $stmt->close();
        }

        if ($error !== '') {
            // Error message already prepared above.
        } elseif ($exists) {
            $error = 'Email sudah terdaftar.';
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            if ($usesLegacySchema) {
                $stmt = $conn->prepare('INSERT INTO users (nama_panjang, nomor_telepon, email, password) VALUES (?, ?, ?, ?)');
            } else {
                $stmt = $conn->prepare('INSERT INTO users (name, phone, email, password_hash) VALUES (?, ?, ?, ?)');
            }

            if (!$stmt) {
                error_log('Register insert prepare failed: ' . $conn->error);
                $error = 'Terjadi masalah sistem saat registrasi. Silakan coba lagi.';
            } else {
                $stmt->bind_param('ssss', $name, $phone, $email, $passwordHash);
                $stmt->execute();
                $stmt->close();

                flashSet('success', 'Akun berhasil dibuat. Silakan masuk menggunakan email dan kata sandi Anda.');
                header('Location: login.php?redirect=' . urlencode($redirectTarget));
                exit();
            }
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - KOSERA</title>
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
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4 md:p-8">

<main class="w-full max-w-5xl bg-white rounded-[2.5rem] shadow-2xl overflow-hidden flex flex-col lg:flex-row">
    
    <section class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-[#6fb2ff] to-brand p-12 flex-col justify-between text-white relative overflow-hidden">
        <div class="absolute -top-20 -left-20 w-64 h-64 bg-white/10 rounded-full"></div>
        <div class="absolute -bottom-20 -right-20 w-48 h-48 bg-white/20 rounded-full"></div>
        
        <div class="relative z-10">
            <img src="<?php echo assetPath('assets/logo.png'); ?>" alt="KOSERA" class="h-12 brightness-0 invert">
            <div class="mt-20">
                <span class="bg-white/20 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-widest">Buat Akunmu</span>
                <h1 class="text-4xl font-extrabold mt-4 leading-tight">Gabung Jadi Mitra <br>KOSERA Sekarang.</h1>
            </div>
        </div>

        <div class="relative z-10 flex justify-center">
             <div class="h-64 w-64 rounded-full bg-white/10"></div>
        </div>
    </section>

    <section class="w-full lg:w-1/2 p-8 md:p-12">
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-slate-800">Buat Akunmu</h2>
            <p class="text-slate-500 text-sm mt-1">Lengkapi data diri untuk mulai menawarkan jasa.</p>
        </div>

        <?php if ($success !== ''): ?>
            <div class="bg-emerald-50 text-emerald-700 p-4 rounded-xl mb-6 border border-emerald-100 text-sm italic">
                ✅ <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <?php if ($error !== ''): ?>
            <div class="bg-red-50 text-red-700 p-4 rounded-xl mb-6 border border-red-100 text-sm italic">
                ⚠️ <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="post" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirectTarget); ?>">

            <div class="md:col-span-2 space-y-1.5">
                <label class="text-xs font-bold text-slate-500 uppercase ml-1">Nama Lengkap</label>
                <input type="text" name="name" class="w-full px-5 py-3.5 rounded-2xl bg-slate-100 border-transparent focus:bg-white focus:ring-2 focus:ring-brand focus:border-transparent outline-none transition-all" placeholder="Masukkan nama Anda" value="<?php echo htmlspecialchars((string) ($_POST['name'] ?? '')); ?>">
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-500 uppercase ml-1">Nomor Telepon</label>
                <input type="text" name="phone" class="w-full px-5 py-3.5 rounded-2xl bg-slate-100 border-transparent focus:bg-white focus:ring-2 focus:ring-brand focus:border-transparent outline-none transition-all" placeholder="08xxxxxxxxxx" value="<?php echo htmlspecialchars((string) ($_POST['phone'] ?? '')); ?>">
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-500 uppercase ml-1">Email</label>
                <input type="email" name="email" class="w-full px-5 py-3.5 rounded-2xl bg-slate-100 border-transparent focus:bg-white focus:ring-2 focus:ring-brand focus:border-transparent outline-none transition-all" placeholder="nama@email.com" value="<?php echo htmlspecialchars((string) ($_POST['email'] ?? '')); ?>">
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-500 uppercase ml-1">Kata Sandi</label>
                <input type="password" name="password" class="w-full px-5 py-3.5 rounded-2xl bg-slate-100 border-transparent focus:bg-white focus:ring-2 focus:ring-brand focus:border-transparent outline-none transition-all" placeholder="Minimal 8 karakter">
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-500 uppercase ml-1">Konfirmasi Sandi</label>
                <input type="password" name="confirm_password" class="w-full px-5 py-3.5 rounded-2xl bg-slate-100 border-transparent focus:bg-white focus:ring-2 focus:ring-brand focus:border-transparent outline-none transition-all" placeholder="Ulangi kata sandi">
            </div>

            <div class="md:col-span-2 mt-4">
                <button type="submit" class="w-full bg-brand hover:bg-brandDark text-white font-bold py-4 rounded-2xl shadow-lg shadow-brand/20 transition-all active:scale-95">
                    Buat Akun Sekarang
                </button>
                <p class="text-center mt-6 text-sm text-slate-500">
                    Sudah memiliki akun? 
                    <a href="login.php<?php echo $redirectTarget !== 'index.php' ? '?redirect=' . urlencode($redirectTarget) : ''; ?>" class="text-brand font-bold hover:underline">Masuk</a>
                </p>
            </div>
        </form>
    </section>
</main>
</body>
</html>
