<?php
require_once __DIR__ . '/config/database.php';
if (session_status() === PHP_SESSION_NONE) session_start();
requireAdmin();

$backupDir = __DIR__ . '/storage/backups';

if (isset($_GET['download'])) {
    $fileName = basename($_GET['download']);
    $filePath = $backupDir . '/' . $fileName;
    
    if (file_exists($filePath) && strpos($fileName, 'backup_') === 0 && substr($fileName, -4) === '.sql') {
        header('Content-Type: application/sql');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    }
}

if (isset($_GET['delete'])) {
    $fileName = basename($_GET['delete']);
    $filePath = $backupDir . '/' . $fileName;
    
    if (file_exists($filePath) && strpos($fileName, 'backup_') === 0 && substr($fileName, -4) === '.sql') {
        unlink($filePath);
        flashMessage('success', '✅ Backup berhasil dihapus!');
        redirect(APP_URL . '/backup_list.php');
    }
}

$backupFiles = [];
if (is_dir($backupDir)) {
    $files = scandir($backupDir, SCANDIR_SORT_DESCENDING);
    foreach ($files as $file) {
        if (substr($file, -4) === '.sql' && strpos($file, 'backup_') === 0) {
            $filePath = $backupDir . '/' . $file;
            $backupFiles[] = [
                'name'      => $file,
                'size'      => filesize($filePath),
                'date'      => filemtime($filePath),
                'path'      => $filePath
            ];
        }
    }
}

$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar Backup — Admin · <?= APP_NAME ?></title>
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/style.css">
  <style>
    .backup-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    .backup-table th {
      background-color: var(--bg-elevated);
      padding: 12px;
      text-align: left;
      border-bottom: 2px solid var(--border);
      font-weight: 600;
    }
    .backup-table td {
      padding: 12px;
      border-bottom: 1px solid var(--border);
    }
    .backup-table tr:hover {
      background-color: var(--bg-elevated);
    }
    .action-btn {
      padding: 6px 12px;
      margin-right: 8px;
      font-size: 0.85rem;
      display: inline-block;
      text-decoration: none;
      border-radius: 4px;
      cursor: pointer;
    }
    .btn-download {
      background-color: var(--success);
      color: white;
    }
    .btn-download:hover {
      opacity: 0.9;
    }
    .btn-delete {
      background-color: var(--danger);
      color: white;
    }
    .btn-delete:hover {
      opacity: 0.9;
    }
  </style>
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
  <div style="max-width:900px;margin:0 auto;">
    <h1 class="page-title">DAFTAR BACKUP</h1>
    <p class="page-subtitle">Kelola file backup database <?= e(DB_NAME) ?></p>
    
    <div class="card">
      <div class="card-body">
        <a href="<?= APP_URL ?>/backup.php" class="btn btn-primary">🔄 Buat Backup Baru</a>
        
        <?php if (empty($backupFiles)): ?>
          <div style="text-align:center;padding:40px 20px;color:var(--text-muted);">
            <p>📭 Belum ada backup database</p>
            <p style="font-size:0.9rem;">Klik tombol di atas untuk membuat backup pertama Anda</p>
          </div>
        <?php else: ?>
          <table class="backup-table">
            <thead>
              <tr>
                <th>📁 Nama File</th>
                <th>📊 Ukuran</th>
                <th>📅 Tanggal</th>
                <th>⚙️ Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($backupFiles as $backup): ?>
              <tr>
                <td>
                  <code style="color:var(--accent);"><?= e($backup['name']) ?></code>
                </td>
                <td><?= number_format($backup['size'] / 1024 / 1024, 2) ?> MB</td>
                <td><?= date('d/m/Y H:i:s', $backup['date']) ?></td>
                <td>
                  <a href="?download=<?= urlencode($backup['name']) ?>" class="action-btn btn-download">💾 Download</a>
                  <a href="?delete=<?= urlencode($backup['name']) ?>" class="action-btn btn-delete" onclick="return confirm('Yakin hapus backup ini?');">🗑️ Hapus</a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
          <p style="font-size:0.85rem;color:var(--text-muted);margin-top:15px;margin-bottom:0;">
            Total backup: <strong><?= count($backupFiles) ?></strong> file | 
            Ukuran total: <strong><?= number_format(array_sum(array_column($backupFiles, 'size')) / 1024 / 1024, 2) ?> MB</strong>
          </p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

</body>
</html>
