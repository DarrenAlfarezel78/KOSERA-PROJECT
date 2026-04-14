<?php
/**
 * Script untuk menambahkan gambar ke layanan dari URL
 */

require_once __DIR__ . '/config/database.php';

$conn = getConnection();
$message = '';
$success = false;

// Proses form POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $serviceId = isset($_POST['service_id']) ? (int)$_POST['service_id'] : 0;
    $imageUrl = trim($_POST['image_url'] ?? '');
    $type = $_POST['type'] ?? 'image';

    if ($serviceId <= 0 || empty($imageUrl)) {
        $message = 'Service ID dan Image URL wajib diisi.';
    } elseif (!in_array($type, ['image', 'certificate'], true)) {
        $message = 'Jenis gambar tidak valid.';
    } else {
        // Verify service exists
        $checkStmt = $conn->prepare('SELECT id FROM services WHERE id = ?');
        $checkStmt->bind_param('i', $serviceId);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        
        if ($checkResult->num_rows === 0) {
            $message = 'Layanan tidak ditemukan.';
        } else {
            // Download image
            $imageData = @file_get_contents($imageUrl, false, stream_context_create([
                'http' => ['timeout' => 10],
                'https' => ['timeout' => 10],
            ]));

            if ($imageData === false) {
                $message = 'Gagal mengunduh gambar. Pastikan URL valid.';
            } else {
                // Validate image
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_buffer($finfo, $imageData);
                finfo_close($finfo);

                if (!in_array($mimeType, ['image/jpeg', 'image/png', 'image/webp'], true)) {
                    $message = 'Format file tidak didukung. Hanya JPG, PNG, atau WebP.';
                } elseif (strlen($imageData) > 2 * 1024 * 1024) {
                    $message = 'Ukuran file terlalu besar (max 2MB).';
                } else {
                    // Update database
                    $column = $type === 'image' ? 'image' : 'certificate';
                    $updateStmt = $conn->prepare("UPDATE services SET $column = ? WHERE id = ?");
                    $updateStmt->bind_param('si', $imageData, $serviceId);
                    
                    if ($updateStmt->execute()) {
                        $message = 'Gambar berhasil ditambahkan!';
                        $success = true;
                    } else {
                        $message = 'Gagal menyimpan gambar.';
                    }
                    $updateStmt->close();
                }
            }
        }
        $checkStmt->close();
    }
}

// Get services list
$serviceOptions = '';
$result = $conn->query('SELECT id, title, provider_name FROM services ORDER BY title ASC');
while ($row = $result->fetch_assoc()) {
    $serviceOptions .= '<option value="' . (int)$row['id'] . '">' . htmlspecialchars($row['title']) . ' - ' . htmlspecialchars($row['provider_name']) . '</option>';
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Gambar - KOSERA</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        
        .modal-box {
            background: white;
            border-radius: 16px;
            padding: 32px;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 20px 60px rgba(15, 23, 42, 0.2);
        }
        
        .modal-box h2 {
            margin: 0 0 8px 0;
            font-size: 1.4rem;
            color: #0f172a;
            text-align: center;
        }
        
        .modal-box .subtitle {
            text-align: center;
            color: #64748b;
            font-size: 0.9rem;
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 16px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 6px;
            color: #374151;
            font-weight: 500;
            font-size: 0.9rem;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-size: 0.9rem;
            font-family: inherit;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #0a7ea2;
            background: #f0f9fb;
        }
        
        .form-group small {
            display: block;
            margin-top: 4px;
            color: #9ca3af;
            font-size: 0.8rem;
        }
        
        .actions {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }
        
        .btn {
            flex: 1;
            padding: 10px 16px;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            text-align: center;
            transition: all 0.2s ease;
        }
        
        .btn-primary {
            background: #0a7ea2;
            color: white;
        }
        
        .btn-primary:hover {
            background: #06546f;
        }
        
        .btn-secondary {
            background: #e5e7eb;
            color: #0f172a;
        }
        
        .btn-secondary:hover {
            background: #d1d5db;
        }
        
        .message {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 16px;
            font-size: 0.9rem;
        }
        
        .success {
            background: #d1fae5;
            border: 1px solid #6ee7b7;
            color: #065f46;
        }
        
        .error {
            background: #fee2e2;
            border: 1px solid #fecaca;
            color: #7f1d1d;
        }
    </style>
</head>
<body style="background: #eef7fb; margin: 0; padding: 0;">
<div class="modal-overlay">
    <div class="modal-box">
        <h2>Tambah Gambar Layanan</h2>
        <div class="subtitle">Gunakan URL dari stock photo open source</div>
        
        <?php if (!empty($message)): ?>
            <div class="message <?php echo $success ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="service_id">Pilih Layanan</label>
                <select id="service_id" name="service_id" required>
                    <option value="">-- Pilih --</option>
                    <?php echo $serviceOptions; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="image_url">URL Gambar</label>
                <input type="text" id="image_url" name="image_url" placeholder="https://source.unsplash.com/600x400?ac repair" required>
                <small>Unsplash: https://source.unsplash.com/600x400?keyword</small>
                <small>Picsum: https://picsum.photos/600/400</small>
            </div>
            
            <div class="form-group">
                <label for="type">Jenis Gambar</label>
                <select id="type" name="type" required>
                    <option value="image">Cover/Logo Jasa</option>
                    <option value="certificate">Sertifikat</option>
                </select>
            </div>
            
            <div class="actions">
                <button type="submit" class="btn btn-primary">Tambahkan</button>
                <a href="index.php" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
