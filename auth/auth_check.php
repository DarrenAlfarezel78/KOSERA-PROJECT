<?php
session_start();

// Jika tidak ada session user_id, tendang ke halaman login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>