<?php
require_once '../auth/auth_check.php';
require_once '../config/database.php';
require_once '../functions/order_functions.php';

$page_title = "Orderan Masuk - KOSERA";

// Ambil semua orderan user yang login
$orders = getOrdersByUser($_SESSION['user_id']);

include '../includes/header.php';
?>

<div class="flex">
    <?php include '../includes/navbar.php'; ?>
    
    <!-- Main Content -->
    <main class="ml-64 flex-1 p-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold">Orderan Masuk</h1>
            <a href="order_create.php" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-blue-700">
                ➕ Tambah Orderan
            </a>
        </div>
        
        <!-- Search Bar (sesuai mockup) -->
        <div class="mb-6 flex gap-4">
            <div class="flex-1 relative">
                <input type="text" id="searchInput" placeholder="Cari Pesanan" 
                       class="w-full px-4 py-3 pl-12 rounded-lg border-2 border-gray-200 focus:border-blue-500 focus:outline-none">
                <svg class="w-5 h-5 text-gray-400 absolute left-4 top-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <button class="bg-white border-2 border-gray-200 px-4 py-3 rounded-lg hover:bg-gray-50">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
            </button>
        </div>
        
        <?php if (empty($orders)): ?>
            <!-- Empty State -->
            <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
                <svg class="w-24 h-24 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="text-xl font-bold text-gray-600 mb-2">Belum Ada Orderan</h3>
                <p class="text-gray-500 mb-6">Mulai tambahkan orderan pertama Anda!</p>
                <a href="order_create.php" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg font-bold hover:bg-blue-700">
                    Tambah Orderan
                </a>
            </div>
        <?php else: ?>
            <!-- Orderan Cards (sesuai mockup KOSERA) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6" id="orderList">
                <?php foreach($orders as $order): ?>
                    <div class="bg-white rounded-2xl shadow-lg p-6 orderan-card" data-search="<?php echo strtolower($order['nama_pemesan'] . ' ' . $order['jenis_layanan'] . ' ' . $order['alamat']); ?>">
                        <!-- Status Badge -->
                        <div class="flex justify-between items-start mb-4">
                            <span class="px-4 py-1 rounded-full text-sm font-bold
                                <?php 
                                    if($order['status'] == 'Pending') echo 'bg-yellow-100 text-yellow-800';
                                    elseif($order['status'] == 'Proses') echo 'bg-blue-100 text-blue-800';
                                    elseif($order['status'] == 'Selesai') echo 'bg-green-100 text-green-800';
                                    else echo 'bg-red-100 text-red-800';
                                ?>">
                                <?php echo $order['status']; ?>
                            </span>
                            
                            <!-- Action Buttons -->
                            <div class="flex gap-2">
                                <a href="order_edit.php?id=<?php echo $order['id']; ?>" 
                                   class="text-blue-600 hover:text-blue-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <a href="order_delete.php?id=<?php echo $order['id']; ?>" 
                                   onclick="return confirm('Yakin ingin menghapus orderan ini?')"
                                   class="text-red-600 hover:text-red-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                        
                        <!-- Order Info -->
                        <div class="flex items-start gap-4">
                            <img src="/assets/images/default-avatar.png" class="w-16 h-16 rounded-full">
                            <div class="flex-1">
                                <h3 class="font-bold text-lg mb-1"><?php echo $order['nama_pemesan']; ?></h3>
                                
                                <div class="flex items-center text-gray-600 mb-2">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span class="text-sm"><?php echo $order['alamat']; ?></span>
                                </div>
                                
                                <div class="flex items-center text-gray-600 mb-2">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span class="text-sm"><?php echo $order['jenis_layanan']; ?></span>
                                </div>
                                
                                <div class="flex items-center text-blue-600 font-bold mb-2">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    <span>Rp <?php echo number_format($order['harga'], 0, ',', '.'); ?></span>
                                </div>
                                
                                <div class="flex items-center text-gray-500 text-sm">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span><?php echo date('d M Y, H:i', strtotime($order['tanggal_order'])); ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="flex gap-3 mt-4">
                            <button class="flex-1 bg-green-500 text-white py-2 rounded-lg font-bold hover:bg-green-600">
                                Terima
                            </button>
                            <button class="flex-1 bg-red-500 text-white py-2 rounded-lg font-bold hover:bg-red-600">
                                Tolak
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</div>

<!-- JavaScript Search -->
<script>
document.getElementById('searchInput').addEventListener('keyup', function() {
    const searchTerm = this.value.toLowerCase();
    const cards = document.querySelectorAll('.orderan-card');
    
    cards.forEach(card => {
        const searchData = card.getAttribute('data-search');
        if (searchData.includes(searchTerm)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
});
</script>

<?php include '../includes/footer.php'; ?>