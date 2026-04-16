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
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $redirectTarget = safeRedirectTarget($_POST['redirect'] ?? 'index.php');

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Emazil harus valid.';
    } elseif ($password === '') {
        $error = 'Kata sandi wajib diisi.';
    } else {
        if ($usesLegacySchema) {
            $stmt = $conn->prepare('SELECT id, nama_panjang AS name, email, nomor_telepon AS phone, password AS password_hash FROM users WHERE email = ? LIMIT 1');
        } else {
            $stmt = $conn->prepare('SELECT id, name, email, phone, password_hash FROM users WHERE email = ? LIMIT 1');
        }

        if (!$stmt) {
            error_log('Login prepare failed: ' . $conn->error);
            $error = 'Terjadi masalah sistem saat login. Silakan coba lagi.';
        } else {
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();

            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['user'] = [
                    'id' => (int) $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'phone' => $user['phone']
                ];

                flashSet('success', 'Selamat datang kembali, ' . $user['name'] . '.');
                header('Location: ' . $redirectTarget);
                exit();
            }

            if ($error === '') {
                $error = 'Email atau kata sandi tidak cocok.';
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
    <title>Masuk - KOSERA</title>
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
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">

<main class="w-full max-w-md">
    <div class="bg-white p-8 md:p-10 rounded-3xl shadow-xl shadow-blue-900/10 border border-gray-100">
        
        <div class="flex justify-center mb-6">
            <img src="<?php echo assetPath('assets/logo.png'); ?>" alt="KOSERA" class="h-16">
        </div>

        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-slate-800">Masuk ke Akunmu</h2>
        </div>

        <?php if ($success !== ''): ?>
            <div class="bg-emerald-50 text-emerald-700 p-3 rounded-xl mb-4 border border-emerald-100 text-sm text-center">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <?php if ($error !== ''): ?>
            <div class="bg-red-50 text-red-700 p-3 rounded-xl mb-4 border border-red-100 text-sm text-center">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="post" class="space-y-6">
            <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirectTarget); ?>">

            <div class="space-y-2">
                <label class="text-sm font-semibold text-slate-600 ml-1">Email</label>
                <input type="email" name="email" class="w-full px-5 py-4 rounded-2xl bg-gray-100 border-transparent focus:bg-white focus:ring-2 focus:ring-brand focus:border-transparent outline-none transition-all" placeholder="nama@email.com" value="<?php echo htmlspecialchars((string) ($_POST['email'] ?? '')); ?>">
            </div>

            <div class="space-y-2">
                <label class="text-sm font-semibold text-slate-600 ml-1">Kata Sandi</label>
                <input type="password" name="password" class="w-full px-5 py-4 rounded-2xl bg-gray-100 border-transparent focus:bg-white focus:ring-2 focus:ring-brand focus:border-transparent outline-none transition-all" placeholder="Masukkan kata sandi">
            </div>

            <button type="submit" class="w-full bg-brand hover:bg-brandDark text-white font-bold py-4 rounded-2xl shadow-lg shadow-brand/20 transition-all active:scale-95 mt-4">
                Masuk
            </button>
        </form>

        <p class="text-center mt-8 text-sm text-slate-500">
            Belum memiliki akun? 
            <a href="register.php<?php echo $redirectTarget !== 'index.php' ? '?redirect=' . urlencode($redirectTarget) : ''; ?>" class="text-brand font-bold hover:underline">Daftar Sekarang</a>
        </p>
    </div>
</main>
</body>
</html>
