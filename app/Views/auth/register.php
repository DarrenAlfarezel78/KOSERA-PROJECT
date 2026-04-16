<?php
$pageTitle = 'Daftar - KOSERA';
$bodyClass = 'auth-page';
require __DIR__ . '/../partials/head.php';
?>
<main class="auth-page-split">
    <section class="auth-visual auth-visual-register">
        <a class="auth-brand auth-brand-small" href="<?php echo appUrl('services'); ?>">
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

        <p class="auth-footnote auth-footnote-center">Sudah memiliki akun? <a href="<?php echo appUrl('auth/login?redirect=' . urlencode($redirectTarget)); ?>">Masuk</a></p>
    </section>
</main>
<?php require __DIR__ . '/../partials/end.php'; ?>
