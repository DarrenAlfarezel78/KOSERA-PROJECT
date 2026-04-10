<?php
// File ini dipanggil di setiap halaman yang butuh login
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /auth/login.php");
    exit();
}

// Refresh session data dari database (optional, untuk data terbaru)
require_once __DIR__ . '/../config/database.php';
$conn = getConnection();
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = $user_id";
$result = $conn->query($sql);

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
    $_SESSION['nama'] = $user['nama_panjang'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['foto_profil'] = $user['foto_profil'];
} else {
    // User tidak ditemukan, logout paksa
    session_destroy();
    header("Location: /auth/login.php");
    exit();
}

closeConnection($conn);
?>