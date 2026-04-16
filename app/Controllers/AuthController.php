<?php
require_once __DIR__ . '/../../auth.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Models/UserModel.php';
require_once __DIR__ . '/../Support/View.php';

function appLoginController(): void
{
    redirectIfLoggedIn();

    $conn = getConnection();
    $error = '';
    $success = flashGet('success');
    $redirectTarget = safeRedirectTarget($_GET['redirect'] ?? 'services');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $redirectTarget = safeRedirectTarget($_POST['redirect'] ?? 'services');

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Email harus valid.';
        } elseif ($password === '') {
            $error = 'Kata sandi wajib diisi.';
        } else {
            $user = findUserByEmail($conn, $email);

            if ($user && password_verify($password, $user['password_hash'])) {
                appLoginUser($user);
                flashSet('success', 'Selamat datang kembali, ' . $user['name'] . '.');
                $conn->close();
                header('Location: ' . appUrl($redirectTarget));
                exit();
            }

            $error = 'Email atau kata sandi tidak cocok.';
        }
    }

    $conn->close();

    appRenderView('auth/login.php', compact('error', 'success', 'redirectTarget'));
}

function appRegisterController(): void
{
    redirectIfLoggedIn();

    $conn = getConnection();
    $error = '';
    $success = flashGet('success');
    $redirectTarget = safeRedirectTarget($_GET['redirect'] ?? 'services');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $redirectTarget = safeRedirectTarget($_POST['redirect'] ?? 'services');

        if ($name === '') {
            $error = 'Nama lengkap wajib diisi.';
        } elseif ($phone === '') {
            $error = 'Nomor telepon wajib diisi.';
        } elseif ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Email harus valid.';
        } elseif (strlen($password) < 8) {
            $error = 'Kata sandi minimal 8 karakter.';
        } elseif ($password !== $confirmPassword) {
            $error = 'Konfirmasi kata sandi tidak cocok.';
        } elseif (userEmailExists($conn, $email)) {
            $error = 'Email sudah terdaftar.';
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            if (createUser($conn, $name, $phone, $email, $passwordHash)) {
                flashSet('success', 'Akun berhasil dibuat. Silakan masuk menggunakan email dan kata sandi Anda.');
                $conn->close();
                header('Location: ' . appUrl('auth/login?redirect=' . urlencode($redirectTarget)));
                exit();
            }

            $error = 'Terjadi masalah sistem saat registrasi. Silakan coba lagi.';
        }
    }

    $conn->close();

    appRenderView('auth/register.php', compact('error', 'success', 'redirectTarget'));
}

function appLogoutController(): void
{
    appLogoutUser();
    flashSet('success', 'Anda telah keluar dari akun.');
    header('Location: ' . appUrl('services'));
    exit();
}
