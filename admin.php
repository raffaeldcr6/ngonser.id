<?php
require_once __DIR__ . '/config/database.php';
if (session_status() === PHP_SESSION_NONE) session_start();
requireAdmin();

$db   = getDB();
$page = sanitize($_GET['page'] ?? 'dashboard');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $act = sanitize($_POST['action'] ?? '');

    if ($act === 'tambah_konser') {
    $nama_konser    = sanitize($_POST['nama_konser'] ?? '');
    $artis          = sanitize($_POST['artis'] ?? '');
    $venue          = sanitize($_POST['venue'] ?? '');
    $kota           = sanitize($_POST['kota'] ?? '');
    $tanggal_konser = sanitize($_POST['tanggal_konser'] ?? '');
    $jam_mulai      = sanitize($_POST['jam_mulai'] ?? '');
    $deskripsi      = sanitize($_POST['deskripsi'] ?? '');
    $status         = sanitize($_POST['status'] ?? 'upcoming');

    $stmt = $db->prepare("
        INSERT INTO konser 
        (nama_konser, artis, venue, kota, tanggal_konser, jam_mulai, deskripsi, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        'ssssssss',
        $nama_konser,
        $artis,
        $venue,
        $kota,
        $tanggal_konser,
        $jam_mulai,
        $deskripsi,
        $status
    );

    $stmt->execute()
        ? flashMessage('success', 'Konser berhasil ditambahkan.')
        : flashMessage('error', 'Gagal menambahkan konser.');

    redirect(APP_URL . '/admin.php?page=konser');
}

    if ($act === 'hapus_konser') {
        $id = (int)$_POST['id'];
        $db->query("DELETE FROM konser WHERE id=$id");
        flashMessage('success', 'Konser dihapus.');
        redirect(APP_URL . '/admin.php?page=konser');
    }

    if ($act === 'tambah_tiket') {
        $kid  = (int)$_POST['konser_id'];
        $kat  = sanitize($_POST['kategori']    ?? '');
        $hrg  = (float)($_POST['harga']        ?? 0);
        $kta  = (int)($_POST['kuota']          ?? 0);
        $ket  = sanitize($_POST['keterangan']  ?? '');

        
        $stmt = $db->prepare("CALL sp_tambah_tiket(?,?,?,?,?,@result)");
        $stmt->bind_param('isdis', $kid, $kat, $hrg, $kta, $ket);
        $stmt->execute();
        $res = $db->query("SELECT @result AS r")->fetch_assoc();
        str_starts_with($res['r'],'SUCCESS')
            ? flashMessage('success', $res['r'])
            : flashMessage('error',   $res['r']);
        redirect(APP_URL . '/admin.php?page=tiket');
    }

    if ($act === 'hapus_tiket') {
        $id = (int)$_POST['id'];
        $stmt = $db->prepare("CALL sp_hapus_tiket(?, @result)");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $db->query("SELECT @result AS r")->fetch_assoc();
        str_starts_with($res['r'],'SUCCESS')
            ? flashMessage('success', $res['r'])
            : flashMessage('error',   $res['r']);
        redirect(APP_URL . '/admin.php?page=tiket');
    }

    if ($act === 'update_status_trx') {
        $kode   = sanitize($_POST['kode_trx'] ?? '');
        $status = sanitize($_POST['status']   ?? '');
        if (in_array($status, ['paid','cancelled','refunded'])) {
            if ($status === 'paid') {
                $stmt = $db->prepare("CALL sp_konfirmasi_bayar(?, @result)");
                $stmt->bind_param('s', $kode);
                $stmt->execute();
                $res = $db->query("SELECT @result AS r")->fetch_assoc();
                flashMessage(str_starts_with($res['r'],'SUCCESS')?'success':'error', $res['r']);
            } else {
                $db->prepare("UPDATE transaksi SET status=? WHERE kode_transaksi=?")->execute()
                    ?: $db->query("UPDATE transaksi SET status='$status' WHERE kode_transaksi='".mysqli_real_escape_string($db,$kode)."'");
                flashMessage('success', 'Status transaksi diperbarui.');
            }
        }
        redirect(APP_URL . '/admin.php?page=transaksi');
    }
}


$data = [];

switch ($page) {
    case 'dashboard':
        $data['stats'] = $db->query("
            SELECT
                (SELECT COUNT(*) FROM users WHERE role='user') AS total_users,
                (SELECT COUNT(*) FROM konser)                   AS total_konser,
                (SELECT COUNT(*) FROM transaksi)                AS total_trx,
                (SELECT SUM(total_harga) FROM transaksi WHERE status='paid') AS total_revenue
        ")->fetch_assoc();
        
        $data['konser_stats'] = $db->query("SELECT * FROM v_statistik_konser LIMIT 5")->fetch_all(MYSQLI_ASSOC);
        
        $data['recent_trx']   = $db->query("
            SELECT tr.kode_transaksi, u.nama, k.nama_konser, tr.total_harga, tr.status, tr.created_at
            FROM transaksi tr
            INNER JOIN users u  ON tr.user_id  = u.id
            INNER JOIN tiket t  ON tr.tiket_id = t.id
            INNER JOIN konser k ON t.konser_id = k.id
            ORDER BY tr.created_at DESC LIMIT 8
        ")->fetch_all(MYSQLI_ASSOC);
        break;

    case 'konser':
        $data['list'] = $db->query("SELECT * FROM konser ORDER BY tanggal_konser DESC")->fetch_all(MYSQLI_ASSOC);
        break;

    case 'tiket':
        
        $data['list'] = $db->query("
            SELECT t.*, k.nama_konser, k.artis,
                   (t.kuota - t.terjual) AS sisa
            FROM tiket t
            LEFT JOIN konser k ON t.konser_id = k.id
            ORDER BY k.tanggal_konser, t.harga DESC
        ")->fetch_all(MYSQLI_ASSOC);
        $data['konser_list'] = $db->query("SELECT id, nama_konser FROM konser ORDER BY nama_konser")->fetch_all(MYSQLI_ASSOC);
        break;

    case 'transaksi':
        
        $data['list'] = $db->query("SELECT * FROM v_riwayat_transaksi ORDER BY waktu_pesan DESC")->fetch_all(MYSQLI_ASSOC);
        
        $data['set_ops'] = $db->query("
            SELECT u.nama, u.email, 'Paid+Pending' AS kategori
            FROM users u
            WHERE u.id IN (SELECT user_id FROM transaksi WHERE status='paid')
              AND u.id IN (SELECT user_id FROM transaksi WHERE status='pending')
            LIMIT 5
        ")->fetch_all(MYSQLI_ASSOC);
        break;

    case 'users':
        $data['list'] = $db->query("SELECT id, nama, email, phone, role, created_at FROM users ORDER BY id")->fetch_all(MYSQLI_ASSOC);
        break;

    case 'deadlock':
        
        $data['tikets'] = $db->query("
            SELECT t.id, t.kategori, k.nama_konser FROM tiket t JOIN konser k ON t.konser_id=k.id LIMIT 5
        ")->fetch_all(MYSQLI_ASSOC);
        break;
}

$pageTitle = ucfirst($page);
require_once __DIR__ . '/admin_layout.php';
?>

<?php if ($page === 'dashboard'): ?>
<div class="d-flex justify-between align-center mb-4 flex-wrap">
  <div>
    <h1 class="page-title">DASHBOARD ADMIN</h1>
    <p class="page-subtitle">Ringkasan sistem Ngonser.id</p>
  </div>
</div>


<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-label">Total Users</div>
    <div class="stat-value"><?= $data['stats']['total_users'] ?? 0 ?></div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Total Konser</div>
    <div class="stat-value white"><?= $data['stats']['total_konser'] ?? 0 ?></div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Total Transaksi</div>
    <div class="stat-value white"><?= $data['stats']['total_trx'] ?? 0 ?></div>
  </div>
  <div class="stat-card">
    <div class="stat-label">Total Revenue</div>
    <div class="stat-value" style="font-size:1.3rem"><?= formatRupiah($data['stats']['total_revenue'] ?? 0) ?></div>
  </div>
</div>

<div class="highlight-box mb-4">
  <strong class="text-accent">⚡ PDT Implementation Aktif:</strong>
  <span class="text-muted"> Views, JOINs, Stored Procedures, Triggers, Transactions, Backup, Deadlock Simulation</span>
</div>


<h2 class="section-title mb-1">STATISTIK KONSER</h2>
<p class="page-subtitle">Via View: <code>v_statistik_konser</code> (Modul 2: Database Views)</p>
<div class="table-wrapper mb-4">
  <table>
    <thead><tr><th>Konser</th><th>Artis</th><th>Transaksi</th><th>Tiket Terjual</th><th>Pendapatan</th></tr></thead>
    <tbody>
      <?php foreach ($data['konser_stats'] as $r): ?>
      <tr>
        <td><?= e($r['nama_konser']) ?></td>
        <td><?= e($r['artis']) ?></td>
        <td><?= $r['total_transaksi'] ?></td>
        <td><?= number_format($r['tiket_terjual'] ?? 0) ?></td>
        <td><?= formatRupiah($r['total_pendapatan'] ?? 0) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<h2 class="section-title mb-1">TRANSAKSI TERBARU</h2>
<p class="page-subtitle">Via <code>INNER JOIN</code> users + tiket + konser (Modul 2: SQL Joins)</p>
<div class="table-wrapper">
  <table>
    <thead><tr><th>Kode</th><th>Pembeli</th><th>Konser</th><th>Total</th><th>Status</th><th>Waktu</th></tr></thead>
    <tbody>
      <?php foreach ($data['recent_trx'] as $r): ?>
      <tr>
        <td><code><?= e($r['kode_transaksi']) ?></code></td>
        <td><?= e($r['nama']) ?></td>
        <td><?= e($r['nama_konser']) ?></td>
        <td><?= formatRupiah($r['total_harga']) ?></td>
        <td><?= slugStatus($r['status']) ?></td>
        <td><?= date('d/m/Y H:i', strtotime($r['created_at'])) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php elseif ($page === 'konser'): ?>
<div class="d-flex justify-between align-center mb-3 flex-wrap">
  <div>
    <h1 class="page-title">MANAJEMEN KONSER</h1>
    <p class="page-subtitle">Tambah dan kelola data konser</p>
  </div>
  <button class="btn btn-primary" data-modal-target="modal-tambah-konser">+ Tambah Konser</button>
</div>

<div class="table-wrapper">
  <table>
    <thead><tr><th>Nama Konser</th><th>Artis</th><th>Venue</th><th>Tanggal</th><th>Status</th><th>Aksi</th></tr></thead>
    <tbody>
      <?php foreach ($data['list'] as $r): ?>
      <tr>
        <td><?= e($r['nama_konser']) ?></td>
        <td><?= e($r['artis']) ?></td>
        <td><?= e($r['venue']) ?>, <?= e($r['kota']) ?></td>
        <td><?= date('d M Y', strtotime($r['tanggal_konser'])) ?></td>
        <td><span class="badge badge-<?= $r['status']==='upcoming'?'success':($r['status']==='selesai'?'secondary':'danger') ?>"><?= e($r['status']) ?></span></td>
        <td>
          <a href="<?= APP_URL ?>/admin.php?page=tiket&konser_id=<?= $r['id'] ?>" class="btn btn-primary btn-sm">+ Tambah Tiket</a>

          <form method="POST" style="display:inline">
            <input type="hidden" name="action" value="hapus_konser">
            <input type="hidden" name="id" value="<?= $r['id'] ?>">
            <button type="submit" class="btn btn-danger btn-sm" data-confirm="Hapus konser ini?">Hapus</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<div id="modal-tambah-konser" class="modal-overlay">
  <div class="modal">
    <button class="modal-close" onclick="this.closest('.modal-overlay').classList.remove('open')">✕</button>
    <h3 style="font-family:var(--font-display);font-size:1.5rem;margin-bottom:1.5rem;">TAMBAH KONSER</h3>
    <form method="POST">
      <input type="hidden" name="action" value="tambah_konser">

      <div class="form-group">
        <label class="form-label">Nama Konser</label>
        <input type="text" name="nama_konser" class="form-control" required>
      </div>

      <div class="form-group">
        <label class="form-label">Artis</label>
        <input type="text" name="artis" class="form-control" required>
      </div>

      <div class="form-group">
        <label class="form-label">Venue</label>
        <input type="text" name="venue" class="form-control" required>
      </div>

      <div class="form-group">
        <label class="form-label">Kota</label>
        <input type="text" name="kota" class="form-control" required>
      </div>

      <div class="form-group">
        <label class="form-label">Tanggal</label>
        <input type="date" name="tanggal_konser" class="form-control" required>
      </div>

      <div class="form-group">
        <label class="form-label">Jam Mulai</label>
        <input type="time" name="jam_mulai" class="form-control" required>
      </div>

      <div class="form-group">
        <label class="form-label">Deskripsi</label>
        <textarea name="deskripsi" class="form-control" rows="3"></textarea>
      </div>

      <div class="form-group">
        <label class="form-label">Status</label>
        <select name="status" class="form-control">
          <option value="upcoming">Upcoming</option>
          <option value="ongoing">Ongoing</option>
          <option value="selesai">Selesai</option>
          <option value="batal">Batal</option>
        </select>
      </div>

      <button type="submit" class="btn btn-primary btn-block">Simpan Konser</button>
    </form>
  </div>
</div>

<?php elseif ($page === 'tiket'): ?>
<div class="d-flex justify-between align-center mb-3 flex-wrap">
  <div>
    <h1 class="page-title">MANAJEMEN TIKET</h1>
    <p class="page-subtitle">Via <code>sp_tambah_tiket</code>, <code>sp_hapus_tiket</code> (Stored Procedure — Modul 7)</p>
  </div>
  <button class="btn btn-primary" data-modal-target="modal-tambah-tiket">+ Tambah Tiket</button>
</div>

<div class="table-wrapper">
  <table>
    <thead><tr><th>Konser</th><th>Kategori</th><th>Harga</th><th>Kuota</th><th>Terjual</th><th>Sisa</th><th>Status</th><th>Aksi</th></tr></thead>
    <tbody>
      <?php foreach ($data['list'] as $r): ?>
      <?php $pct = $r['kuota'] > 0 ? round($r['terjual']/$r['kuota']*100) : 0; ?>
      <tr>
        <td><?= e($r['nama_konser']) ?></td>
        <td><?= e($r['kategori']) ?></td>
        <td><?= formatRupiah($r['harga']) ?></td>
        <td><?= number_format($r['kuota']) ?></td>
        <td><?= number_format($r['terjual']) ?></td>
        <td>
          <div><?= number_format($r['sisa']) ?></div>
          <div class="progress" style="margin-top:.3rem;width:80px">
            <div class="progress-bar <?= $pct>80?'danger':'' ?>" style="width:<?= $pct ?>%"></div>
          </div>
        </td>
        <td>
          <span class="badge <?= $r['sisa']<=0?'badge-danger':($r['sisa']<=10?'badge-warning':'badge-success') ?>">
            <?= $r['sisa']<=0?'HABIS':($r['sisa']<=10?'HAMPIR HABIS':'TERSEDIA') ?>
          </span>
        </td>
        <td>
          <form method="POST" style="display:inline">
            <input type="hidden" name="action" value="hapus_tiket">
            <input type="hidden" name="id" value="<?= $r['id'] ?>">
            <button class="btn btn-danger btn-sm" data-confirm="Hapus tiket ini?">Hapus</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<div id="modal-tambah-tiket" class="modal-overlay">
  <div class="modal">
    <button class="modal-close" onclick="this.closest('.modal-overlay').classList.remove('open')">✕</button>
    <h3 style="font-family:var(--font-display);font-size:1.5rem;margin-bottom:1.5rem;">TAMBAH TIKET</h3>
    <form method="POST">
      <input type="hidden" name="action" value="tambah_tiket">

      <div class="form-group">
        <label class="form-label">Konser</label>
        <select name="konser_id" class="form-control" required>
          <?php foreach ($data['konser_list'] as $k): ?>
            <option value="<?= $k['id'] ?>" <?= ((int)$k['id'] === (int)($_GET['konser_id'] ?? 0)) ? 'selected' : '' ?>>
              <?= e($k['nama_konser']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group">
        <label class="form-label">Kategori</label>
        <input type="text" name="kategori" class="form-control" placeholder="VIP, VVIP, Festival..." required>
      </div>

      <div class="form-group">
        <label class="form-label">Harga (Rp)</label>
        <input type="number" name="harga" class="form-control" min="0" required>
      </div>

      <div class="form-group">
        <label class="form-label">Kuota</label>
        <input type="number" name="kuota" class="form-control" min="1" required>
      </div>

      <div class="form-group">
        <label class="form-label">Keterangan</label>
        <textarea name="keterangan" class="form-control" rows="2"></textarea>
      </div>

      <button type="submit" class="btn btn-primary btn-block">Simpan Tiket</button>
    </form>
  </div>
</div>

<?php if (isset($_GET['konser_id'])): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
  document.getElementById('modal-tambah-tiket')?.classList.add('open');
});
</script>
<?php endif; ?>

<?php elseif ($page === 'transaksi'): ?>
<h1 class="page-title">SEMUA TRANSAKSI</h1>
<p class="page-subtitle">Via View: <code>v_riwayat_transaksi</code> (Modul 2: Views + JOIN)</p>

<?php if (!empty($data['set_ops'])): ?>
<div class="highlight-box mb-3">
  <strong>📊 Set Operations (Modul 2 INTERSECT simulasi):</strong> User dengan transaksi PAID dan PENDING:
  <?php foreach ($data['set_ops'] as $u): ?>
    <span class="badge badge-gold ml-1"><?= e($u['nama']) ?></span>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<div class="table-wrapper">
  <table>
    <thead><tr><th>Kode</th><th>Pembeli</th><th>Konser</th><th>Kategori</th><th>Jml</th><th>Total</th><th>Status</th><th>Metode</th><th>Aksi</th></tr></thead>
    <tbody>
      <?php foreach ($data['list'] as $r): ?>
      <tr>
        <td><code><?= e($r['kode_transaksi']) ?></code></td>
        <td><?= e($r['nama_pembeli']) ?></td>
        <td><?= e($r['nama_konser']) ?></td>
        <td><?= e($r['kategori_tiket']) ?></td>
        <td><?= $r['jumlah_tiket'] ?></td>
        <td><?= formatRupiah($r['total_harga']) ?></td>
        <td><?= slugStatus($r['status']) ?></td>
        <td><?= e($r['metode_bayar'] ?? '-') ?></td>
        <td>
          <?php if ($r['status'] === 'pending'): ?>
          <form method="POST" style="display:inline">
            <input type="hidden" name="action" value="update_status_trx">
            <input type="hidden" name="kode_trx" value="<?= e($r['kode_transaksi']) ?>">
            <input type="hidden" name="status" value="paid">
            <button class="btn btn-success btn-sm">✓ Konfirmasi</button>
          </form>
          <?php elseif ($r['status'] === 'paid'): ?>
          <form method="POST" style="display:inline">
            <input type="hidden" name="action" value="update_status_trx">
            <input type="hidden" name="kode_trx" value="<?= e($r['kode_transaksi']) ?>">
            <input type="hidden" name="status" value="refunded">
            <button class="btn btn-sm" style="background:var(--info);color:#fff" data-confirm="Refund transaksi ini?">Refund</button>
          </form>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php elseif ($page === 'users'): ?>
<h1 class="page-title">DAFTAR PENGGUNA</h1>
<p class="page-subtitle">Semua user yang terdaftar di sistem</p>
<div class="table-wrapper">
  <table>
    <thead><tr><th>#</th><th>Nama</th><th>Email</th><th>Phone</th><th>Role</th><th>Bergabung</th></tr></thead>
    <tbody>
      <?php foreach ($data['list'] as $r): ?>
      <tr>
        <td><?= $r['id'] ?></td>
        <td><?= e($r['nama']) ?></td>
        <td><?= e($r['email']) ?></td>
        <td><?= e($r['phone'] ?? '-') ?></td>
        <td><span class="badge <?= $r['role']==='admin'?'badge-gold':'badge-info' ?>"><?= e($r['role']) ?></span></td>
        <td><?= date('d M Y', strtotime($r['created_at'])) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php elseif ($page === 'deadlock'): ?>
<h1 class="page-title">SIMULASI DEADLOCK</h1>
<p class="page-subtitle">Visualisasi interaktif kebuntuan transaksi (Modul 4: Deadlock Management)</p>

<div class="grid-2 mb-4" style="gap:2rem;align-items:start;">
  <div>
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-between align-center mb-3">
          <h3 style="font-family:var(--font-display);font-size:1.3rem">ARENA SIMULASI</h3>
          <span id="step-counter" class="badge badge-info">Langkah 0/8</span>
        </div>

        
        <div id="deadlock-container" class="deadlock-arena" style="min-height:300px;position:relative;">
          <svg class="arrow-svg"></svg>
          <div id="node-andi"  class="process-node"  style="top:30%;left:8%;">👤 Andi<br><small>Transaksi 1</small></div>
          <div id="node-budi"  class="process-node"  style="top:30%;right:8%;">👤 Budi<br><small>Transaksi 2</small></div>
          <div id="node-tikA"  class="resource-node" style="top:60%;left:30%;">🎟️ Tiket A<br><small>VIP</small></div>
          <div id="node-tikB"  class="resource-node" style="top:60%;right:30%;">🎟️ Tiket B<br><small>Festival</small></div>
          <div style="text-align:center;color:var(--text-muted);font-size:.85rem;position:absolute;top:5%;left:50%;transform:translateX(-50%);">
            Circular Wait Diagram
          </div>
        </div>

        <div class="d-flex gap-1 mt-3 flex-wrap">
          <button class="btn btn-primary btn-sm" onclick="deadlockSim.nextStep()">▶ Langkah Berikutnya</button>
          <button class="btn btn-outline btn-sm"  onclick="deadlockSim.autoRun(1200)">⚡ Jalankan Otomatis</button>
          <button class="btn btn-sm" style="background:var(--bg-elevated);color:var(--text-muted);" onclick="deadlockSim.reset()">↺ Reset</button>
        </div>
      </div>
    </div>
  </div>

  
  <div>
    <div class="card mb-3">
      <div class="card-body">
        <h3 style="font-family:var(--font-display);font-size:1.1rem;margin-bottom:.8rem;">📋 LOG SIMULASI</h3>
        <div id="deadlock-log" style="background:var(--bg-deep);border-radius:var(--radius-sm);padding:1rem;height:200px;overflow-y:auto;font-size:.82rem;font-family:monospace;">
          <div class="text-muted">Klik "Langkah Berikutnya" untuk memulai simulasi...</div>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <h3 style="font-family:var(--font-display);font-size:1.1rem;margin-bottom:1rem;">🛡️ PENANGANAN DEADLOCK</h3>
        <div class="mb-3">
          <strong class="text-danger">1. Deteksi Otomatis DBMS</strong>
          <p class="text-muted" style="font-size:.85rem;margin-top:.3rem;">
            MySQL mendeteksi <em>circular wait</em> dan memilih satu transaksi sebagai <strong>victim</strong>,
            lalu men-<code>ROLLBACK</code> otomatis. Error: <code>ERROR 1213 (40001)</code>
          </p>
        </div>
        <div class="mb-3">
          <strong class="text-warning">2. Lock Ordering (Pencegahan)</strong>
          <p class="text-muted" style="font-size:.85rem;margin-top:.3rem;">
            Semua transaksi mengunci resource dengan <strong>urutan ID yang sama</strong>.
            Tidak mungkin terjadi circular wait.
          </p>
          <code style="font-size:.75rem;display:block;margin-top:.5rem;padding:.5rem;background:var(--bg-deep);border-radius:4px;">
            SELECT ... FROM tiket WHERE id IN (1,2) ORDER BY id FOR UPDATE;
          </code>
        </div>
        <div>
          <strong class="text-success">3. Retry Mechanism</strong>
          <p class="text-muted" style="font-size:.85rem;margin-top:.3rem;">
            Aplikasi mendeteksi error deadlock dan mengulang transaksi secara otomatis
            hingga berhasil atau batas retry tercapai.
          </p>
        </div>
      </div>
    </div>
  </div>
</div>


<div class="card">
  <div class="card-body">
    <h3 style="font-family:var(--font-display);font-size:1.3rem;margin-bottom:1rem;">📚 KARAKTERISTIK DEADLOCK (Coffman Conditions)</h3>
    <div class="grid-2" style="grid-template-columns:repeat(2,1fr);gap:1rem;">
      <?php
      $chars = [
        ['🔒','Mutual Exclusion','Hanya satu proses yang bisa menggunakan resource pada satu waktu.'],
        ['🤲','Hold and Wait','Proses memegang resource sambil menunggu resource lain.'],
        ['⛔','No Preemption','Resource tidak bisa diambil paksa dari proses yang memegangnya.'],
        ['🔄','Circular Wait','Terbentuk rantai proses yang saling menunggu secara melingkar.'],
      ];
      foreach ($chars as $c): ?>
      <div class="highlight-box">
        <div style="font-size:1.3rem;margin-bottom:.4rem;"><?= $c[0] ?></div>
        <strong><?= $c[1] ?></strong>
        <p class="text-muted" style="font-size:.83rem;margin-top:.3rem;"><?= $c[2] ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<style>
.log-line { padding:.25rem 0; border-bottom:1px solid rgba(255,255,255,.05); }
.log-time  { color:var(--text-muted);margin-right:.5rem; }
.log-success b { color:var(--success); }
.log-warning b { color:var(--warning); }
.log-danger b  { color:var(--danger);  }
.log-info b    { color:var(--info);    }
</style>
<script src="<?= APP_URL ?>/assets/js/main.js"></script>
<script>initDeadlock('deadlock-container');</script>

<?php endif; ?>

  </div>
</div>

<script src="<?= APP_URL ?>/assets/js/main.js"></script>
</body>
</html>
