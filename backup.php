<?php
require_once __DIR__ . '/config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

requireAdmin();

$db = getDB();
$backupDir = __DIR__ . '/storage/backups';

if (!is_dir($backupDir)) {
    mkdir($backupDir, 0755, true);
}

$response = ['status' => 'error', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'create_backup') {
    try {
        $timestamp = date('Y-m-d_H-i-s');
        $backupFile = $backupDir . '/backup_' . DB_NAME . '_' . $timestamp . '.sql';
        $mysqlDir = 'C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysqldump.exe';

        $command = sprintf(
            '"%s" --host=%s --user=%s %s --single-transaction --quick --lock-tables=false > %s',
            $mysqlDir,
            escapeshellarg(DB_HOST),
            escapeshellarg(DB_USER),
            escapeshellarg(DB_NAME),
            escapeshellarg($backupFile)
        );

        if (defined('DB_PASS') && DB_PASS !== '') {
            $command = sprintf(
                '"%s" --host=%s --user=%s --password=%s %s --single-transaction --quick --lock-tables=false > %s',
                $mysqlDir,
                escapeshellarg(DB_HOST),
                escapeshellarg(DB_USER),
                escapeshellarg(DB_PASS),
                escapeshellarg(DB_NAME),
                escapeshellarg($backupFile)
            );
        }

        $output = null;
        $returnVar = null;
        exec($command, $output, $returnVar);

        if ($returnVar === 0 && file_exists($backupFile)) {
            $fileSize = filesize($backupFile);
            $filename = basename($backupFile);
            $ukuranKb = (int) ceil($fileSize / 1024);
            $createdBy = $_SESSION['nama'] ?? 'Admin';

            $stmt = $db->prepare("
                INSERT INTO backup_log (filename, tipe, ukuran_kb, created_by)
                VALUES (?, 'manual', ?, ?)
            ");
            $stmt->bind_param("sis", $filename, $ukuranKb, $createdBy);
            $stmt->execute();

            $response = [
                'status' => 'success',
                'message' => "✅ Backup berhasil dibuat! ({$timestamp})<br>Ukuran: " . number_format($fileSize / 1024, 2) . ' KB'
            ];
            flashMessage('success', $response['message']);
        } else {
            $response = [
                'status' => 'error',
                'message' => '❌ Gagal membuat backup. Pastikan mysqldump terinstall.'
            ];
            flashMessage('error', $response['message']);
        }
    } catch (Exception $e) {
        $response = [
            'status' => 'error',
            'message' => '❌ Error: ' . $e->getMessage()
        ];
        flashMessage('error', $response['message']);
    }

    redirect(APP_URL . '/backup_list.php');
}

$flash = getFlash();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backup Database — Admin · <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/style.css">
</head>
<body>
    <nav class="navbar">
        <a class="navbar-brand" href="<?= APP_URL ?>/index.php">NGONSER<span>.ID</span></a>
        <div class="nav-links">
            <a href="<?= APP_URL ?>/admin.php">Admin Panel</a>
            <a href="<?= APP_URL ?>/logout.php" class="btn btn-outline btn-sm">Keluar</a>
        </div>
    </nav>

    <?php if ($flash): ?>
        <div class="container" style="padding-top:1rem;">
            <div class="flash flash-<?= e($flash['type']) ?>"><?= $flash['msg'] ?></div>
        </div>
    <?php endif; ?>

    <div class="container section">
        <div style="max-width:600px;margin:0 auto;">
            <h1 class="page-title">BACKUP DATABASE</h1>
            <p class="page-subtitle">Buat backup database <?= e(DB_NAME) ?> sekarang</p>

            <div class="card">
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="action" value="create_backup">
                        <button type="submit" class="btn btn-primary" style="width:100%;padding:12px;">
                            🔄 Buat Backup Sekarang
                        </button>
                    </form>

                    <hr style="margin:20px 0;border:none;border-top:1px solid var(--border);">

                    <p style="font-size:.9rem;color:var(--text-muted);margin:0;">
                        <strong>Info:</strong> Backup akan disimpan di folder <code>storage/backups/</code> dengan format SQL.<br>
                        Backup otomatis mencakup semua tabel dan data dengan timestamp.
                    </p>
                </div>
            </div>

            <div style="margin-top:20px;">
                <a href="<?= APP_URL ?>/backup_list.php" class="btn btn-outline" style="width:100%;text-align:center;padding:10px;">
                    📋 Lihat Daftar Backup
                </a>
            </div>
        </div>
    </div>
</body>
<<<<<<< HEAD
</html>
=======
</html>
>>>>>>> d92982debed2a94ebf82ac7c14b8d6213e088eac
