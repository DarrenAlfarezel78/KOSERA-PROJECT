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
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="auth-page">
<main class="auth-page-split">
    <section class="auth-visual auth-visual-register">
        <a class="auth-brand auth-brand-small" href="index.php">
            <img src="<?php echo assetPath('assets/logo.png'); ?>" alt="KOSERA">
        </a>

        <div class="auth-visual-copy auth-visual-copy-register">
            <span class="eyebrow">Buat Akunmu</span>
        </div>
    </section>

    <section class="auth-card auth-card-register">
        <div class="auth-card-head auth-card-head-right">
            <h2>Buat Akunmu</h2>
        </div>

        <?php if ($success !== ''): ?>
            <div class="success-box"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <?php if ($error !== ''): ?>
            <div class="error-box"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="post" class="auth-form auth-form-register">
            <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($redirectTarget); ?>">

            <div class="form-stack">
                <label for="name">Nama panjang</label>
                <input type="text" id="name" name="name" placeholder="Nama Anda" value="<?php echo htmlspecialchars((string) ($_POST['name'] ?? '')); ?>">
            </div>

            <div class="form-stack">
                <label for="phone">Nomor Telepon</label>
                <input type="text" id="phone" name="phone" placeholder="08xxxxxxxxxx" value="<?php echo htmlspecialchars((string) ($_POST['phone'] ?? '')); ?>">
            </div>

            <div class="form-stack">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="nama@email.com" value="<?php echo htmlspecialchars((string) ($_POST['email'] ?? '')); ?>">
            </div>

            <div class="form-stack">
                <label for="password">Kata Sandi</label>
                <input type="password" id="password" name="password" placeholder="Minimal 8 karakter">
            </div>

            <div class="form-stack">
                <label for="confirm_password">Konfirmasi Kata Sandi</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Ulangi kata sandi">
            </div>

            <button type="submit" class="btn btn-primary btn-block auth-submit">Buat Akun</button>
        </form>

        <p class="auth-footnote auth-footnote-center">Sudah memiliki akun? <a href="login.php<?php echo $redirectTarget !== 'index.php' ? '?redirect=' . urlencode($redirectTarget) : ''; ?>">Masuk</a></p>
    </section>
</main>
</body>
</html>
