<?php
require_once __DIR__ . '/config/database.php';
if (session_status() === PHP_SESSION_NONE) session_start();
requireLogin();

$db     = getDB();
$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'bayar') {
    $kodeTrx = sanitize($_POST['kode_trx'] ?? '');
    if ($kodeTrx) {
        
        $stmt = $db->prepare("CALL sp_konfirmasi_bayar(?, @result)");
        $stmt->bind_param('s', $kodeTrx);
        $stmt->execute();
        while ($db->more_results() && $db->next_result()) {} // Clear buffer
        $res = $db->query("SELECT @result AS result")->fetch_assoc();
        $msg = $res['result'] ?? '';
        if (str_starts_with($msg, 'SUCCESS')) {
            flashMessage('success', '✅ Pembayaran berhasil! Tiket kamu sudah aktif.');
        } else {
            flashMessage('error', '❌ ' . $msg);
        }
    }
    redirect(APP_URL . '/dashboard.php');
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'checkout') {
    $tiketId = (int)($_POST['tiket_id'] ?? 0);
    $jumlah  = (int)($_POST['jumlah']   ?? 1);
    $metode  = sanitize($_POST['metode'] ?? 'transfer_bank');

    if ($tiketId < 1 || $jumlah < 1) {
        flashMessage('error', 'Data tidak valid.');
        redirect(APP_URL . '/booking.php');
    }
    $stmt = $db->prepare("CALL sp_checkout_tiket(?, ?, ?, ?, @result, @kode_trx)");
    $stmt->bind_param('iiis', $userId, $tiketId, $jumlah, $metode);
    $stmt->execute();
    while ($db->more_results() && $db->next_result()) {} 

    $res = $db->query("SELECT @result AS result, @kode_trx AS kode_trx")->fetch_assoc();
    $msg = $res['result']   ?? '';
    $kode = $res['kode_trx'] ?? '';

    if (str_starts_with($msg, 'SUCCESS')) {
        flashMessage('success', "🎟️ Booking berhasil! Kode: <strong>$kode</strong>. Segera lakukan pembayaran.");
        redirect(APP_URL . '/booking.php?bayar=' . urlencode($kode));
    } else {
        flashMessage('error', '❌ ' . $msg);
        redirect(APP_URL . '/booking.php?konser=' . $tiketId);
    }
}
$viewBayar = null;
if (isset($_GET['bayar'])) {
    $kodeTrx = sanitize($_GET['bayar']);
    $stmt = $db->prepare("
        SELECT tr.*, t.kategori, t.harga, k.nama_konser, k.artis, k.tanggal_konser, k.venue
        FROM transaksi tr
        JOIN tiket t  ON tr.tiket_id  = t.id
        JOIN konser k ON t.konser_id  = k.id
        WHERE tr.kode_transaksi = ? AND tr.user_id = ?
        LIMIT 1
    ");
    $stmt->bind_param('si', $kodeTrx, $userId);
    $stmt->execute();
    $viewBayar = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}


$konserData = null;
$tiketList  = [];
if (isset($_GET['konser'])) {
    $konserID = (int)$_GET['konser'];
    $stmt = $db->prepare("SELECT * FROM konser WHERE id = ? LIMIT 1");
    $stmt->bind_param('i', $konserID);
    $stmt->execute();
    $konserData = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    $result = $db->query("CALL sp_lihat_tiket($konserID)");
    
    $tiketList = ($result instanceof mysqli_result) ? $result->fetch_all(MYSQLI_ASSOC) : [];
    
    if ($result instanceof mysqli_result) {
        $result->free();
    }
    while ($db->more_results() && $db->next_result()) { 
        if ($r = $db->store_result()) $r->free();
    }  
    if (empty($tiketList)) {
        $stmt2 = $db->prepare("
            SELECT t.id, t.kategori,
                   CONCAT('Rp ', FORMAT(t.harga,0,'id_ID')) AS harga_format,
                   t.kuota, t.terjual, (t.kuota-t.terjual) AS sisa,
                   CASE WHEN (t.kuota-t.terjual)<=0 THEN 'HABIS'
                        WHEN (t.kuota-t.terjual)<=10 THEN 'HAMPIR HABIS'
                        ELSE 'TERSEDIA' END AS ketersediaan,
                   t.harga
            FROM tiket t WHERE t.konser_id = ? ORDER BY t.harga DESC
        ");
        $stmt2->bind_param('i', $konserID);
        $stmt2->execute();
        $tiketList = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt2->close();
    }
}
$konserList = $db->query("
    SELECT k.*, MIN(t.harga) AS harga_mulai, SUM(t.kuota-t.terjual) AS sisa_total
    FROM konser k LEFT JOIN tiket t ON k.id=t.konser_id
    WHERE k.status='upcoming' GROUP BY k.id ORDER BY k.tanggal_konser ASC
")->fetch_all(MYSQLI_ASSOC);

$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Beli Tiket — <?= APP_NAME ?></title>
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/style.css">
</head>
<body>
<nav class="navbar">
  <a class="navbar-brand" href="<?= APP_URL ?>/index.php">NGONSER<span>.ID</span></a>
  <div class="nav-links">
    <a href="<?= APP_URL ?>/dashboard.php">Dashboard</a>
    <a href="<?= APP_URL ?>/logout.php" class="btn btn-outline btn-sm">Keluar</a>
  </div>
</nav>

<?php if ($flash): ?>
<div class="container" style="padding-top:1rem;">
  <div class="flash flash-<?= e($flash['type']) ?>"><?= $flash['msg'] ?></div>
</div>
<?php endif; ?>

<div class="container section">

  <?php if ($viewBayar): ?>
  <div style="max-width:560px;margin:0 auto;">
    <h1 class="page-title">KONFIRMASI BAYAR</h1>
    <p class="page-subtitle">Selesaikan pembayaranmu sekarang!</p>

    <div class="card">
      <div class="card-body">
        <div class="highlight-box">
          <div style="font-size:.8rem;color:var(--text-muted);">Kode Transaksi</div>
          <code style="font-size:1.1rem;"><?= e($viewBayar['kode_transaksi']) ?></code>
        </div>
        <div class="divider"></div>
        <table style="border:none;width:100%">
          <tr><td class="text-muted" style="border:none;padding:.4rem 0">Konser</td><td style="border:none;padding:.4rem 0;font-weight:600"><?= e($viewBayar['nama_konser']) ?></td></tr>
          <tr><td class="text-muted" style="border:none;padding:.4rem 0">Artis</td><td style="border:none;padding:.4rem 0"><?= e($viewBayar['artis']) ?></td></tr>
          <tr><td class="text-muted" style="border:none;padding:.4rem 0">Tanggal</td><td style="border:none;padding:.4rem 0"><?= date('d M Y', strtotime($viewBayar['tanggal_konser'])) ?></td></tr>
          <tr><td class="text-muted" style="border:none;padding:.4rem 0">Venue</td><td style="border:none;padding:.4rem 0"><?= e($viewBayar['venue']) ?></td></tr>
          <tr><td class="text-muted" style="border:none;padding:.4rem 0">Kategori</td><td style="border:none;padding:.4rem 0"><?= e($viewBayar['kategori']) ?></td></tr>
          <tr><td class="text-muted" style="border:none;padding:.4rem 0">Jumlah</td><td style="border:none;padding:.4rem 0"><?= $viewBayar['jumlah_tiket'] ?> tiket</td></tr>
          <tr><td class="text-muted" style="border:none;padding:.4rem 0">Metode</td><td style="border:none;padding:.4rem 0"><?= e($viewBayar['metode_bayar']) ?></td></tr>
          <tr><td class="text-muted" style="border:none;padding:.4rem 0">Status</td><td style="border:none;padding:.4rem 0"><?= slugStatus($viewBayar['status']) ?></td></tr>
        </table>
        <div class="divider"></div>
        <div class="d-flex justify-between align-center">
          <span class="text-muted">Total Pembayaran</span>
          <span class="stat-value" style="font-size:1.6rem;"><?= formatRupiah($viewBayar['total_harga']) ?></span>
        </div>

        <?php if ($viewBayar['status'] === 'pending'): ?>
        <div class="mt-3">
          <p class="text-muted mb-2" style="font-size:.85rem;">
            Simulasi: Klik tombol di bawah untuk mengkonfirmasi pembayaran.<br>
            <em>Trigger <code>trg_after_update_transaksi</code> akan otomatis memotong stok tiket.</em>
          </p>
          <form method="POST">
            <input type="hidden" name="action"   value="bayar">
            <input type="hidden" name="kode_trx" value="<?= e($viewBayar['kode_transaksi']) ?>">
            <button type="submit" class="btn btn-success btn-block">✅ Konfirmasi Pembayaran</button>
          </form>
        </div>
        <?php else: ?>
          <div class="flash flash-success mt-3">✅ Pembayaran sudah dikonfirmasi!</div>
        <?php endif; ?>
      </div>
    </div>
    <a href="<?= APP_URL ?>/dashboard.php" class="btn btn-outline mt-3">← Kembali ke Dashboard</a>
  </div>

  <?php elseif ($konserData && !empty($tiketList)): ?>
  <a href="<?= APP_URL ?>/booking.php" class="btn btn-outline btn-sm mb-3">← Pilih Konser Lain</a>
  <h1 class="page-title"><?= e($konserData['nama_konser']) ?></h1>
  <p class="page-subtitle">
    🎸 <?= e($konserData['artis']) ?> &nbsp;·&nbsp;
    📍 <?= e($konserData['venue']) ?>, <?= e($konserData['kota']) ?> &nbsp;·&nbsp;
    📅 <?= date('d M Y', strtotime($konserData['tanggal_konser'])) ?>
  </p>
  <div class="highlight-box mb-4">
    <small class="text-muted">💡 PDT: Data tiket via <code>sp_lihat_tiket()</code> (Stored Procedure) + <code>fn_status_tiket()</code> (Custom Function)</small>
  </div>

  <div class="grid-3">
    <?php foreach ($tiketList as $t): ?>
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-between align-center mb-2">
          <span class="badge <?= $t['ketersediaan']==='HABIS'?'badge-danger':($t['ketersediaan']==='HAMPIR HABIS'?'badge-warning':'badge-success') ?>">
            <?= e($t['ketersediaan']) ?>
          </span>
          <span class="text-muted" style="font-size:.8rem;"><?= $t['sisa'] ?> sisa</span>
        </div>
        <h3 style="font-size:1.2rem;margin-bottom:.3rem;"><?= e($t['kategori']) ?></h3>
        <div class="price" style="margin-bottom:1rem;"><?= e($t['harga_format']) ?></div>

        <?php
        $sisa = (int)($t['sisa'] ?? 0);
        $hargaRaw = (float)($t['harga'] ?? 0);

if ($hargaRaw <= 0 && isset($t['harga_format'])) {
    $hargaRaw = (float) str_replace(['Rp', '.', ',', ' '], '', $t['harga_format']);
}
        ?>
        <?php if ($sisa > 0): ?>
        <form method="POST" data-harga="<?= $hargaRaw ?>">
          <input type="hidden" name="action"   value="checkout">
          <input type="hidden" name="tiket_id" value="<?= $t['id'] ?>">
          <div class="form-group">
            <label class="form-label">Jumlah Tiket</label>
            <div class="ticket-counter">
  <button type="button" class="btn-minus">−</button>
  <input 
  type="text" 
  name="jumlah" 
  value="1" 
  min="1" 
  max="<?= min($sisa,5) ?>" 
  class="form-control"
  readonly
  style="color:#ffffff !important; background:#1f2937 !important; text-align:center !important; font-size:18px !important; font-weight:700 !important; width:58px !important; height:58px !important;"
>
  <button type="button" class="btn-plus">+</button>
</div>
          </div>
          <div class="form-group">
            <label class="form-label">Metode Bayar</label>
            <select name="metode" class="form-control">
              <option value="transfer_bank">Transfer Bank</option>
              <option value="gopay">GoPay</option>
              <option value="ovo">OVO</option>
              <option value="dana">DANA</option>
              <option value="qris">QRIS</option>
            </select>
          </div>
          <div class="d-flex justify-between align-center mb-2">
            <span class="text-muted">Total:</span>
            <strong id="total-harga" class="text-accent"><?= formatRupiah($hargaRaw) ?></strong>
            <input type="hidden" id="hidden-total" name="total_harga" value="<?= $hargaRaw ?>">
          </div>
          <button type="submit" class="btn btn-primary btn-block">Pesan Sekarang →</button>
        </form>
        <?php else: ?>
          <button class="btn btn-sm btn-block" disabled style="opacity:.5;background:var(--bg-elevated);color:var(--text-muted);">Tiket Habis</button>
        <?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <?php else: ?>
  <h1 class="page-title">BELI TIKET</h1>
  <p class="page-subtitle">Pilih konser yang ingin kamu hadiri</p>
  <div class="grid-3">
    <?php foreach ($konserList as $k): ?>
    <div class="card konser-card">
      <div class="card-img" style="background:linear-gradient(135deg,#0f1421,#161d2e);display:flex;align-items:center;justify-content:center;font-size:3.5rem;">🎤</div>
      <div class="card-body">
        <span class="kategori-tag"><?= e($k['kota']) ?></span>
        <h3><?= e($k['nama_konser']) ?></h3>
        <div class="meta">
          <span>🎸 <?= e($k['artis']) ?></span>
          <span>📅 <?= date('d M Y', strtotime($k['tanggal_konser'])) ?></span>
          <span>🎟️ Sisa <?= number_format($k['sisa_total'] ?? 0) ?> tiket</span>
        </div>
        <div class="price-row">
          <div class="price"><?= formatRupiah($k['harga_mulai'] ?? 0) ?></div>
          <?php if (($k['sisa_total'] ?? 0) > 0): ?>
            <a href="?konser=<?= $k['id'] ?>" class="btn btn-primary btn-sm">Beli →</a>
          <?php else: ?>
            <span class="badge badge-danger">Habis</span>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

<footer>
  <p>© <?= date('Y') ?> <strong><?= APP_NAME ?></strong></p>
</footer>
<script src="<?= APP_URL ?>/assets/js/main.js?v=10"></script>
</body>
</html>
