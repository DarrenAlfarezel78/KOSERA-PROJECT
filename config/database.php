<?php
$host = "localhost";
$user = "root";
$pass = "blub";
$db   = "jasa_kos"; // Pastikan sudah buat database ini di phpMyAdmin

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}
?>