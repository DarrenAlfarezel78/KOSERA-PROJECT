<?php
include '../auth/auth_check.php';
include '../config/database.php';

$user_id = $_SESSION['user_id'];

// Mengambil total pendapatan mitra
$query_pendapatan = mysqli_query($conn, "SELECT SUM(total_harga) as total FROM orders WHERE mitra_id = '$user_id' AND status = 'Selesai'");
$data_pendapatan = mysqli_fetch_assoc($query_pendapatan);
$total_pendapatan = $data_pendapatan['total'] ?? 0;

// Mengambil jumlah pesanan aktif
$query_aktif = mysqli_query($conn, "SELECT COUNT(*) as total FROM orders WHERE mitra_id = '$user_id' AND status = 'Proses'");
$data_aktif = mysqli_fetch_assoc($query_aktif);
$total_aktif = $data_aktif['total'] ?? 0;
?>

<div class="card">
    <h3>Total Pendapatan</h3>
    <p>Rp<?php echo number_format($total_pendapatan, 0, ',', '.'); ?></p>
</div>