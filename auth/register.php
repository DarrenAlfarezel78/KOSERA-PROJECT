<?php
session_start();
require_once '../config/database.php';

$page_title = "Register - KOSERA";
include '../includes/header.php';

// Proses form register
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn = getConnection();
    
    $nama = mysqli_real_escape_string($conn, $_POST['nama_panjang']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $nomor_telepon = mysqli_real_escape_string($conn, $_POST['nomor_telepon']);
    
    // Cek email sudah ada atau belum
    $check_sql = "SELECT id FROM users WHERE email = '$email'";
    $check_result = $conn->query($check_sql);
    
    if ($check_result->num_rows > 0) {
        $error = "Email sudah terdaftar!";
    } else {
        // Insert ke database
        $sql = "INSERT INTO users (nama_panjang, email, password, nomor_telepon) 
                VALUES ('$nama', '$email', '$password', '$nomor_telepon')";
        
        if ($conn->query($sql)) {
            $_SESSION['success'] = "Registrasi berhasil! Silakan login.";
            header("Location: login.php");
            exit();
        } else {
            $error = "Registrasi gagal: " . $conn->error;
        }
    }
    
    closeConnection($conn);
}
?>

<!-- UI Register (sesuai mockup) -->
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-blue-100">
    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md">
        <div class="text-center mb-8">
            <img src="/assets/images/logo.png" alt="KOSERA" class="h-16 mx-auto mb-4">
            <h2 class="text-2xl font-bold">Buat Akunmu</h2>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Nama Panjang</label>
                <input type="text" name="nama_panjang" required
                       class="w-full px-4 py-3 rounded-lg bg-gray-100 border-transparent focus:border-blue-500 focus:bg-white focus:ring-0">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Nomor Telepon</label>
                <input type="tel" name="nomor_telepon" required
                       class="w-full px-4 py-3 rounded-lg bg-gray-100 border-transparent focus:border-blue-500 focus:bg-white focus:ring-0">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Email</label>
                <input type="email" name="email" required
                       class="w-full px-4 py-3 rounded-lg bg-gray-100 border-transparent focus:border-blue-500 focus:bg-white focus:ring-0">
            </div>
            
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Kata Sandi</label>
                <input type="password" name="password" required
                       class="w-full px-4 py-3 rounded-lg bg-gray-100 border-transparent focus:border-blue-500 focus:bg-white focus:ring-0">
            </div>
            
            <div class="mb-6">
                <label class="block text-gray-700 mb-2">Konfirmasi Kata Sandi</label>
                <input type="password" name="password_confirm" required
                       class="w-full px-4 py-3 rounded-lg bg-gray-100 border-transparent focus:border-blue-500 focus:bg-white focus:ring-0">
            </div>
            
            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-bold hover:bg-blue-700 transition">
                Buat Akun
            </button>
        </form>
        
        <p class="text-center mt-6 text-gray-600">
            Sudah memiliki akun? <a href="login.php" class="text-blue-600 hover:underline">Masuk</a>
        </p>
    </div>
</div>

<?php include '../includes/footer.php'; ?>