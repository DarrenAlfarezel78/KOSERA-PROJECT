<!-- Sidebar (sesuai desain KOSERA) -->
<aside class="w-64 bg-white h-screen fixed left-0 top-0 shadow-lg">
    <!-- Logo -->
    <div class="p-6 border-b">
        <img src="/assets/images/logo.png" alt="KOSERA" class="h-12">
    </div>
    
    <!-- Profile Section -->
    <div class="p-6 border-b text-center">
        <img src="<?php echo $_SESSION['foto_profil'] ?? '/assets/images/default-avatar.png'; ?>" 
             class="w-24 h-24 rounded-full mx-auto mb-3">
        <h3 class="font-bold text-blue-600">Profil</h3>
    </div>
    
    <!-- Menu -->
    <nav class="p-4">
        <a href="/pages/dashboard.php" class="block px-4 py-3 rounded-lg hover:bg-blue-600 hover:text-white mb-2">
            📊 Dashboard
        </a>
        <a href="/pages/orders.php" class="block px-4 py-3 rounded-lg hover:bg-blue-600 hover:text-white mb-2">
            📦 Orderan Masuk
        </a>
        <a href="/pages/profile.php" class="block px-4 py-3 rounded-lg hover:bg-blue-600 hover:text-white mb-2">
            👤 Detail/Riwayat
        </a>
        <hr class="my-4">
        <a href="/laporan/laporan_orderan.php" class="block px-4 py-3 rounded-lg hover:bg-blue-600 hover:text-white mb-2">
            📄 Laporan
        </a>
        <a href="/auth/logout.php" class="block px-4 py-3 rounded-lg text-red-600 hover:bg-red-50">
            🚪 Logout
        </a>
    </nav>
</aside>