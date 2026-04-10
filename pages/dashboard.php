<?php
require_once '../auth/auth_check.php';
require_once '../config/database.php';
require_once '../functions/order_functions.php';

$page_title = "Dashboard - KOSERA";

// Ambil statistik
$stats = getOrderStats($_SESSION['user_id']);

include '../includes/header.php';
?>

<div class="flex">
    <?php include '../includes/navbar.php'; ?>
    
    <!-- Main Content -->
    <main class="ml-64 flex-1 p-8">
        <h1 class="text-3xl font-bold mb-8">Selamat Datang Kak <?php echo $_SESSION['nama']; ?>!</h1>
        
        <!-- Stats Cards (sesuai mockup KOSERA) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Card 1: Pesanan Bulan Ini -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-4 mx-auto">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <h3 class="text-center text-gray-600 mb-2">Pesanan Bulan ini</h3>
                <p class="text-center text-4xl font-bold text-blue-600"><?php echo $stats['orderan_bulan_ini']; ?></p>
            </div>
            
            <!-- Card 2: Total Pendapatan -->
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-center w-16 h-16 bg-white/20 rounded-full mb-4 mx-auto">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <h3 class="text-center mb-2">Total Pendapatan</h3>
                <p class="text-center text-3xl font-bold">Rp <?php echo number_format($stats['total_pendapatan'], 0, ',', '.'); ?></p>
            </div>
            
            <!-- Card 3: Layanan Aktif -->
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-center w-16 h-16 bg-white/20 rounded-full mb-4 mx-auto">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h3 class="text-center mb-2">Layanan Yang Aktif</h3>
                <p class="text-center text-4xl font-bold"><?php echo $stats['layanan_aktif']; ?></p>
            </div>
            
            <!-- Card 4: Poin -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-4 mx-auto">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-center text-gray-600 mb-2">Poin</h3>
                <p class="text-center text-4xl font-bold text-blue-600">5</p>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="mt-8">
            <a href="order_create.php" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-blue-700 transition">
                ➕ Tambah Orderan Baru
            </a>
            <a href="orders.php" class="inline-block bg-white border-2 border-blue-600 text-blue-600 px-6 py-3 rounded-lg font-bold hover:bg-blue-50 transition ml-4">
                📋 Lihat Semua Orderan
            </a>
        </div>
    </main>
</div>

<?php include '../includes/footer.php'; ?>