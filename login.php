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
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="auth-page">
<main class="auth-page-center">
    <div class="auth-login-card">
        <div class="auth-login-brand">
            <img src="<?php echo assetPath('assets/logo.png'); ?>" alt="KOSERA">
        </div>

        <div class="auth-card-head auth-card-head-center">
            <h2>Masuk ke Akunmu</h2>
        </div>

        <?php if ($success !== ''): ?>
            <div class="success-box"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <?php if ($error !== ''): ?>
            <div class="error-box"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="post" class="auth-form auth-form-compact">
            <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirectTarget); ?>">

            <div class="form-stack">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="nama@email.com" value="<?php echo htmlspecialchars((string) ($_POST['email'] ?? '')); ?>">
            </div>

            <div class="form-stack">
                <label for="password">Kata Sandi</label>
                <input type="password" id="password" name="password" placeholder="Masukkan kata sandi">
            </div>

            <button type="submit" class="btn btn-primary btn-block auth-submit">Masuk</button>
        </form>

        <p class="auth-footnote auth-footnote-center">Belum memiliki akun? <a href="register.php<?php echo $redirectTarget !== 'index.php' ? '?redirect=' . urlencode($redirectTarget) : ''; ?>">Daftar Sekarang</a></p>
    </div>
</main>
</body>
</html>
