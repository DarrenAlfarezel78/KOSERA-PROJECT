<?php
// Fungsi-fungsi untuk operasi orderan (KISS: semua fungsi orderan di 1 file)

// CREATE - Tambah orderan baru
function createOrder($user_id, $nama_pemesan, $alamat, $jenis_layanan, $harga) {
    $conn = getConnection();
    
    $nama_pemesan = mysqli_real_escape_string($conn, $nama_pemesan);
    $alamat = mysqli_real_escape_string($conn, $alamat);
    $jenis_layanan = mysqli_real_escape_string($conn, $jenis_layanan);
    $harga = floatval($harga);
    
    $sql = "INSERT INTO orderan (user_id, nama_pemesan, alamat, jenis_layanan, harga, status) 
            VALUES ($user_id, '$nama_pemesan', '$alamat', '$jenis_layanan', $harga, 'Pending')";
    
    $result = $conn->query($sql);
    closeConnection($conn);
    
    return $result;
}

// READ - Ambil semua orderan user tertentu
function getOrdersByUser($user_id) {
    $conn = getConnection();
    
    $sql = "SELECT * FROM orderan WHERE user_id = $user_id ORDER BY tanggal_order DESC";
    $result = $conn->query($sql);
    
    $orders = [];
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
    }
    
    closeConnection($conn);
    return $orders;
}

// READ - Ambil 1 orderan berdasarkan ID
function getOrderById($order_id, $user_id) {
    $conn = getConnection();
    
    // Validasi: orderan harus milik user yang login
    $sql = "SELECT * FROM orderan WHERE id = $order_id AND user_id = $user_id";
    $result = $conn->query($sql);
    
    $order = null;
    if ($result->num_rows == 1) {
        $order = $result->fetch_assoc();
    }
    
    closeConnection($conn);
    return $order;
}

// UPDATE - Edit orderan
function updateOrder($order_id, $user_id, $nama_pemesan, $alamat, $jenis_layanan, $harga, $status) {
    $conn = getConnection();
    
    $nama_pemesan = mysqli_real_escape_string($conn, $nama_pemesan);
    $alamat = mysqli_real_escape_string($conn, $alamat);
    $jenis_layanan = mysqli_real_escape_string($conn, $jenis_layanan);
    $harga = floatval($harga);
    $status = mysqli_real_escape_string($conn, $status);
    
    // Validasi: hanya bisa update orderan milik sendiri
    $sql = "UPDATE orderan 
            SET nama_pemesan = '$nama_pemesan',
                alamat = '$alamat',
                jenis_layanan = '$jenis_layanan',
                harga = $harga,
                status = '$status'
            WHERE id = $order_id AND user_id = $user_id";
    
    $result = $conn->query($sql);
    closeConnection($conn);
    
    return $result;
}

// DELETE - Hapus orderan
function deleteOrder($order_id, $user_id) {
    $conn = getConnection();
    
    // Validasi: hanya bisa hapus orderan milik sendiri
    $sql = "DELETE FROM orderan WHERE id = $order_id AND user_id = $user_id";
    
    $result = $conn->query($sql);
    closeConnection($conn);
    
    return $result;
}

// STATS - Hitung statistik untuk dashboard
function getOrderStats($user_id) {
    $conn = getConnection();
    
    $stats = [];
    
    // Total orderan bulan ini
    $sql = "SELECT COUNT(*) as total FROM orderan 
            WHERE user_id = $user_id 
            AND MONTH(tanggal_order) = MONTH(CURRENT_DATE())
            AND YEAR(tanggal_order) = YEAR(CURRENT_DATE())";
    $result = $conn->query($sql);
    $stats['orderan_bulan_ini'] = $result->fetch_assoc()['total'];
    
    // Total pendapatan
    $sql = "SELECT SUM(harga) as total FROM orderan WHERE user_id = $user_id AND status = 'Selesai'";
    $result = $conn->query($sql);
    $stats['total_pendapatan'] = $result->fetch_assoc()['total'] ?? 0;
    
    // Layanan aktif
    $sql = "SELECT COUNT(*) as total FROM orderan WHERE user_id = $user_id AND status = 'Proses'";
    $result = $conn->query($sql);
    $stats['layanan_aktif'] = $result->fetch_assoc()['total'];
    
    closeConnection($conn);
    return $stats;
}
?>