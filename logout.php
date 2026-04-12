<?php
require_once __DIR__ . '/auth.php';

unset($_SESSION['user']);
flashSet('success', 'Anda telah keluar dari akun.');

header('Location: index.php');
exit();
