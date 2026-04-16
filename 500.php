<?php

if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

$errorData = $_SESSION['app_last_error'] ?? [];
unset($_SESSION['app_last_error']);

$errorId = (string) ($errorData['id'] ?? 'ERR-UNKNOWN');
$errorMessage = (string) ($errorData['message'] ?? 'Terjadi kesalahan pada server.');
$errorFile = (string) ($errorData['file'] ?? '-');
$errorLine = (int) ($errorData['line'] ?? 0);
$errorTime = (string) ($errorData['time'] ?? date('Y-m-d H:i:s'));

http_response_code(500);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HTTP 500 - Internal Server Error</title>
    <style>
        :root {
            --bg: #f4f7fb;
            --card: #ffffff;
            --text: #0f172a;
            --muted: #475569;
            --line: #d8e0eb;
            --accent: #b91c1c;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: "Segoe UI", Tahoma, sans-serif;
            color: var(--text);
            background: radial-gradient(circle at 20% 10%, #fff6f6, var(--bg));
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
        }

        .card {
            width: min(760px, 100%);
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 14px;
            box-shadow: 0 14px 40px rgba(15, 23, 42, 0.09);
            overflow: hidden;
        }

        .head {
            padding: 22px 24px;
            background: linear-gradient(120deg, #ffe4e6, #fff1f2);
            border-bottom: 1px solid var(--line);
        }

        .head h1 {
            margin: 0;
            font-size: 24px;
            color: var(--accent);
        }

        .head p {
            margin: 8px 0 0;
            color: var(--muted);
        }

        .body {
            padding: 24px;
            display: grid;
            gap: 14px;
        }

        .row {
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 12px 14px;
            background: #fbfdff;
        }

        .label {
            display: block;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #64748b;
            margin-bottom: 5px;
        }

        .value {
            margin: 0;
            color: var(--text);
            word-break: break-word;
            font-family: "Consolas", "Courier New", monospace;
            font-size: 14px;
            white-space: pre-wrap;
        }

        .actions {
            margin-top: 8px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            text-decoration: none;
            border: 1px solid #cbd5e1;
            color: #0f172a;
            padding: 10px 14px;
            border-radius: 8px;
            font-weight: 600;
            background: #fff;
        }

        .btn:hover {
            background: #f8fafc;
        }
    </style>
</head>
<body>
    <section class="card">
        <header class="head">
            <h1>HTTP 500 - Internal Server Error</h1>
            <p>Terjadi kesalahan saat memproses permintaan. Detail ditampilkan di halaman ini.</p>
        </header>

        <div class="body">
            <div class="row">
                <span class="label">Error ID</span>
                <p class="value"><?php echo htmlspecialchars($errorId); ?></p>
            </div>

            <div class="row">
                <span class="label">Waktu</span>
                <p class="value"><?php echo htmlspecialchars($errorTime); ?></p>
            </div>

            <div class="row">
                <span class="label">Pesan Error</span>
                <p class="value"><?php echo htmlspecialchars($errorMessage); ?></p>
            </div>

            <div class="row">
                <span class="label">Lokasi</span>
                <p class="value"><?php echo htmlspecialchars($errorFile); ?><?php echo $errorLine > 0 ? ':' . $errorLine : ''; ?></p>
            </div>

            <div class="actions">
                <a class="btn" href="javascript:location.reload();">Coba Muat Ulang</a>
                <a class="btn" href="index.php">Kembali ke Beranda</a>
            </div>
        </div>
    </section>
</body>
</html>
