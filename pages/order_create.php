<?php
require_once '../auth/auth_check.php';
require_once '../config/database.php';
require_once '../functions/order_functions.php';

$page_title = "Tambah Orderan Baru - KOSERA";

// Proses saat tombol simpan diklik
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $nama_pemesan = $_POST['nama_pemesan'];
    $alamat = $_POST['alamat'];
    $jenis_layanan = $_POST['jenis_layanan'];
    $harga = $_POST['harga'];

    // VALIDASI SERVER-SIDE (Kriteria ETS)
    if (empty($nama_pemesan) || empty($alamat) || empty($harga)) {
        $error = "Semua kolom wajib diisi!";
    } elseif (!is_numeric($harga)) {
        $error = "Harga harus berupa angka!";
    } else {
        // Jika valid, masukkan ke database via function
        $result = createOrder($user_id, $nama_pemesan, $alamat, $jenis_layanan, $harga);
        
        if ($result) {
            echo "<script>alert('Orderan berhasil ditambahkan!'); window.location='orders.php';</script>";
        } else {
            $error = "Gagal menambah data.";
        }
    }
}

include '../includes/header.php';
?>

<div class="flex">
    <?php include '../includes/navbar.php'; ?>
    
    <main class="ml-64 flex-1 p-8">
        <h1 class="text-3xl font-bold mb-6">Tambah Orderan Baru</h1>

        <?php if (isset($error)): ?>
            <div class="bg-red-100 text-red-700 p-4 rounded-lg mb-6"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" class="bg-white p-8 rounded-2xl shadow-lg max-w-2xl">
            <div class="mb-4">
                <label class="block mb-2 font-bold">Nama Anak Kos (Pemesan)</label>
                <input type="text" name="nama_pemesan" class="w-full p-3 border rounded-lg" placeholder="Contoh: Budi">
            </div>
            <div class="mb-4">
                <label class="block mb-2 font-bold">Alamat / No. Kamar</label>
                <textarea name="alamat" class="w-full p-3 border rounded-lg"></textarea>
            </div>
            <div class="mb-4">
                <label class="block mb-2 font-bold">Jenis Layanan</label>
                <select name="jenis_layanan" class="w-full p-3 border rounded-lg">
                    <option value="Perbaikan">Perbaikan Alat</option>
                    <option value="Antar Jemput">Antar Jemput</option>
                    <option value="Titip Beli">Titip Beli / Jastip</option>
                </select>
            </div>
            <div class="mb-6">
                <label class="block mb-2 font-bold">Harga Jasa (Rp)</label>
                <input type="number" name="harga" class="w-full p-3 border rounded-lg" placeholder="20000">
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-bold hover:bg-blue-700">
                Simpan Pesanan
            </button>
        </form>
    </main>
</div>

<?php include '../includes/footer.php'; ?>