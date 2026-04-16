<?php
$pageTitle = 'Masuk - KOSERA';
$bodyClass = 'auth-page';
require __DIR__ . '/../partials/head.php';
?>
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

        <p class="auth-footnote auth-footnote-center">Belum memiliki akun? <a href="<?php echo appUrl('auth/register?redirect=' . urlencode($redirectTarget)); ?>">Daftar Sekarang</a></p>
    </div>
</main>
<?php require __DIR__ . '/../partials/end.php'; ?>
