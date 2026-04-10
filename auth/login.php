<?php
session_start();
require_once '../config/database.php';

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: /pages/dashboard.php");
    exit();
}

$page_title = "Login - KOSERA";
include '../includes/header.php';

// Proses login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn = getConnection();
    
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            // Login berhasil
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama'] = $user['nama_panjang'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['foto_profil'] = $user['foto_profil'];
            
            header("Location: /pages/dashboard.php");
            exit();
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Email tidak ditemukan!";
    }
    
    closeConnection($conn);
}
?>

<!-- UI Login (sesuai mockup) -->
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-blue-100">
    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md">
        <div class="text-center mb-8">
            <img src="/assets/images/logo.png" alt="KOSERA" class="h-16 mx-auto mb-4">
        </div>
        
        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Email</label>
                <input type="email" name="email" required
                       class="w-full px-4 py-3 rounded-lg bg-gray-100 border-transparent focus:border-blue-500 focus:bg-white focus:ring-0">
            </div>
            
            <div class="mb-6">
                <label class="block text-gray-700 mb-2">Kata Sandi</label>
                <input type="password" name="password" required
                       class="w-full px-4 py-3 rounded-lg bg-gray-100 border-transparent focus:border-blue-500 focus:bg-white focus:ring-0">
            </div>
            
            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-bold hover:bg-blue-700 transition">
                Masuk
            </button>
        </form>
        
        <p class="text-center mt-6 text-gray-600">
            Belum punya akun? <a href="register.php" class="text-blue-600 hover:underline">Daftar</a>
        </p>
    </div>
</div>

<?php include '../includes/footer.php'; ?>