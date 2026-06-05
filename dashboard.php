<?php
require_once __DIR__ . '/config/database.php';
if (session_status() === PHP_SESSION_NONE) session_start();
requireLogin();

$userId = $_SESSION['user_id'];
$db     = getDB();

$stats = $db->prepare("
    SELECT
        COUNT(*) AS total_trx,
        SUM(CASE WHEN status='paid' THEN 1 ELSE 0 END) AS trx_lunas,
        SUM(CASE WHEN status='paid' THEN total_harga ELSE 0 END) AS total_spent,
        SUM(CASE WHEN status='pending' THEN 1 ELSE 0 END) AS trx_pending
    FROM transaksi WHERE user_id = ?
");
$stats->bind_param('i', $userId);
$stats->execute();
$s = $stats->get_result()->fetch_assoc();

$trx = $db->prepare("
    SELECT * FROM v_riwayat_transaksi WHERE email = ? ORDER BY waktu_pesan DESC LIMIT 20
");
$trx->bind_param('s', $_SESSION['email']);
$trx->execute();
$transaksiList = $trx->get_result()->fetch_all(MYSQLI_ASSOC);

$konserList = $db->query("
    SELECT konser_id, nama_konser, artis, tanggal_konser,
           MIN(harga) AS harga_mulai, SUM(sisa_tiket) AS total_sisa
    FROM v_tiket_tersedia
    GROUP BY konser_id, nama_konser, artis, tanggal_konser
    ORDER BY tanggal_konser ASC LIMIT 6
")->fetch_all(MYSQLI_ASSOC);

$pageTitle = 'Dashboard';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard — <?= APP_NAME ?></title>
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/style.css">
</head>
<body>
<nav class="navbar">
  <a class="navbar-brand" href="<?= APP_URL ?>/index.php">NGONSER<span>.ID</span></a>
  <div class="nav-links">
    <a href="<?= APP_URL ?>/dashboard.php" class="active">Dashboard</a>
    <?php if (isAdmin()): ?>
      <a href="<?= APP_URL ?>/admin.php">Admin Panel</a>
    <?php endif; ?>
    <a href="<?= APP_URL ?>/logout.php" class="btn btn-outline btn-sm">Keluar</a>
  </div>
</nav>

<?php $flash = getFlash(); if ($flash): ?>
<div class="container" style="padding-top:1rem;">
  <div class="flash flash-<?= e($flash['type']) ?>"><?= e($flash['msg']) ?></div>
</div>
<?php endif; ?>

<div class="container section">
  <div class="d-flex justify-between align-center flex-wrap mb-4">
    <div>
      <h1 class="page-title">DASHBOARD</h1>
      <p class="page-subtitle">Halo, <strong style="color:var(--accent)"><?= e($_SESSION['nama']) ?></strong>! 👋</p>
    </div>
    <a href="<?= APP_URL ?>/booking.php" class="btn btn-primary">🎟️ Beli Tiket Baru</a>
  </div>

  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-label">Total Transaksi</div>
      <div class="stat-value"><?= $s['total_trx'] ?></div>
    </div>
    <div class="stat-card">
      <div class="stat-label">Tiket Lunas</div>
      <div class="stat-value"><?= $s['trx_lunas'] ?></div>
    </div>
    <div class="stat-card">
      <div class="stat-label">Menunggu Bayar</div>
      <div class="stat-value white"><?= $s['trx_pending'] ?></div>
    </div>
    <div class="stat-card">
      <div class="stat-label">Total Pengeluaran</div>
      <div class="stat-value" style="font-size:1.4rem"><?= formatRupiah($s['total_spent'] ?? 0) ?></div>
    </div>
  </div>

  <h2 class="section-title mb-1">KONSER TERSEDIA</h2>
  <p class="section-subtitle">Beli tiket sebelum kehabisan! <small class="text-muted">(Via View: v_tiket_tersedia)</small></p>
  <div class="grid-3 mb-4">
    <?php foreach ($konserList as $k): ?>
    <div class="card konser-card">
      <div class="card-img" style="background:linear-gradient(135deg,#0f1421,#161d2e);display:flex;align-items:center;justify-content:center;font-size:3rem;">🎤</div>
      <div class="card-body">
        <span class="kategori-tag">Tersedia</span>
        <h3><?= e($k['nama_konser']) ?></h3>
        <div class="meta">
          <span>🎸 <?= e($k['artis']) ?></span>
          <span>📅 <?= date('d M Y', strtotime($k['tanggal_konser'])) ?></span>
          <span>🎟️ Sisa <?= number_format($k['total_sisa']) ?> tiket</span>
        </div>
        <div class="price-row">
          <div>
            <div style="font-size:.75rem;color:var(--text-muted);">Mulai dari</div>
            <div class="price"><?= formatRupiah($k['harga_mulai']) ?></div>
          </div>
          <a href="<?= APP_URL ?>/booking.php?konser=<?= $k['konser_id'] ?>" class="btn btn-primary btn-sm">Beli</a>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
    <?php if (empty($konserList)): ?>
      <div style="grid-column:1/-1;text-align:center;padding:2rem;color:var(--text-muted);">Belum ada konser tersedia.</div>
    <?php endif; ?>
  </div>

  <div class="d-flex justify-between align-center mb-2">
    <h2 class="section-title">RIWAYAT TRANSAKSI</h2>
    <span class="text-muted" style="font-size:.8rem;">Via View: v_riwayat_transaksi</span>
  </div>
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>Kode</th><th>Konser</th><th>Kategori</th>
          <th>Jml</th><th>Total</th><th>Status</th>
          <th>Tanggal</th><th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($transaksiList as $t): ?>
        <tr>
          <td><code><?= e($t['kode_transaksi']) ?></code></td>
          <td><?= e($t['nama_konser']) ?></td>
          <td><?= e($t['kategori_tiket']) ?></td>
          <td><?= $t['jumlah_tiket'] ?></td>
          <td><?= formatRupiah($t['total_harga']) ?></td>
          <td><?= slugStatus($t['status']) ?></td>
          <td><?= date('d/m/Y', strtotime($t['waktu_pesan'])) ?></td>
          <td>
            <?php if ($t['status'] === 'pending'): ?>
              <a href="<?= APP_URL ?>/booking.php?bayar=<?= urlencode($t['kode_transaksi']) ?>"
                 class="btn btn-success btn-sm">Bayar</a>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($transaksiList)): ?>
          <tr><td colspan="8" class="text-center text-muted" style="padding:2rem;">Belum ada transaksi.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<footer>
  <p>© <?= date('Y') ?> <strong><?= APP_NAME ?></strong></p>
</footer>
<script src="<?= APP_URL ?>/assets/js/main.js"></script>
</body>
</html>
