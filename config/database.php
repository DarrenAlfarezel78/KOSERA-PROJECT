<?php
// Konfigurasi Database (KISS: semua config di 1 file)
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'blub');
define('DB_NAME', 'kosera_db');
define('DB_PORT', '3306');

// Fungsi koneksi (simple function)
function getConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

    if ($conn->connect_error) {
        die("Koneksi gagal: " . $conn->connect_error);
    }
    
    return $conn;
}

// Auto close connection saat script selesai
function closeConnection($conn) {
    $conn->close();
}
?>