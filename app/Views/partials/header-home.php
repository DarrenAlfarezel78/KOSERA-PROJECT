<header class="header simple-home-header">
    <div class="container">
        <div class="simple-topbar">
            <a class="brand" href="<?php echo appUrl('services'); ?>">
                <img src="<?php echo assetPath('assets/logo.png'); ?>" alt="KOSERA">
            </a>

            <div class="nav-actions">
                <span class="badge">Halo, <?php echo htmlspecialchars($user['name']); ?></span>
                <a class="btn btn-secondary" href="<?php echo appUrl('auth/logout'); ?>">Keluar</a>
            </div>
        </div>
    </div>
</header>
