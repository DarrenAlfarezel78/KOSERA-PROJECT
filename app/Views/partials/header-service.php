<header class="site-header">
    <div class="container">
        <div class="topbar">
            <a class="brand" href="<?php echo appUrl('services'); ?>">
                <img src="<?php echo assetPath('assets/logo.png'); ?>" alt="KOSERA">
                <div class="brand-copy">
                    <strong>KOSERA</strong>
                    <span>Sahabat Bantuan Anak Kos</span>
                </div>
            </a>

            <div class="nav-actions">
                <?php if ($user !== null): ?>
                    <span class="badge">Halo, <?php echo htmlspecialchars($user['name']); ?></span>
                    <a class="btn btn-secondary" href="<?php echo appUrl('auth/logout'); ?>">Keluar</a>
                <?php else: ?>
                    <a class="btn btn-secondary" href="<?php echo appUrl('auth/login'); ?>">Masuk</a>
                    <a class="btn btn-primary" href="<?php echo appUrl('auth/register'); ?>">Daftar</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>
