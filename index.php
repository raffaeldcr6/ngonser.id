<?php
require_once __DIR__ . '/config/database.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'login') {
    $email    = sanitize($_POST['email']    ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        flashMessage('error', 'Email dan password wajib diisi.');
    } else {
        $db   = getDB();
        $stmt = $db->prepare("SELECT id, nama, email, password, role FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama']    = $user['nama'];
            $_SESSION['email']   = $user['email'];
            $_SESSION['role']    = $user['role'];
            flashMessage('success', 'Selamat datang, ' . $user['nama'] . '!');
            redirect(APP_URL . ($user['role'] === 'admin' ? '/admin.php' : '/dashboard.php'));
        } else {
            flashMessage('error', 'Email atau password salah.');
        }
    }
    redirect(APP_URL . '/index.php#login');
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'register') {
    $nama     = sanitize($_POST['nama']     ?? '');
    $email    = sanitize($_POST['email']    ?? '');
    $phone    = sanitize($_POST['phone']    ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm']  ?? '';

    if (empty($nama) || empty($email) || empty($password)) {
        flashMessage('error', 'Semua field wajib diisi.');
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        flashMessage('error', 'Format email tidak valid.');
    } elseif (strlen($password) < 6) {
        flashMessage('error', 'Password minimal 6 karakter.');
    } elseif ($password !== $confirm) {
        flashMessage('error', 'Konfirmasi password tidak cocok.');
    } else {
        $db   = getDB();
        $chk  = $db->prepare("SELECT id FROM users WHERE email = ?");
        $chk->bind_param('s', $email);
        $chk->execute();
        if ($chk->get_result()->num_rows > 0) {
            flashMessage('error', 'Email sudah terdaftar.');
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $ins  = $db->prepare("INSERT INTO users (nama, email, password, phone) VALUES (?,?,?,?)");
            $ins->bind_param('ssss', $nama, $email, $hash, $phone);
            if ($ins->execute()) {
                flashMessage('success', 'Registrasi berhasil! Silakan login.');
            } else {
                flashMessage('error', 'Registrasi gagal, coba lagi.');
            }
        }
    }
    redirect(APP_URL . '/index.php#login');
}


if (isLoggedIn()) redirect(APP_URL . '/dashboard.php');


$db     = getDB();
$konser = $db->query("
    SELECT k.*, MIN(t.harga) AS harga_mulai,
           SUM(t.kuota - t.terjual) AS sisa_tiket
    FROM konser k
    LEFT JOIN tiket t ON k.id = t.konser_id
    WHERE k.status = 'upcoming'
    GROUP BY k.id ORDER BY k.tanggal_konser ASC LIMIT 6
")->fetch_all(MYSQLI_ASSOC);

$flash = getFlash();
$pageTitle = 'Beranda';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= APP_NAME ?> — Tiket Konser Terbaik</title>
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/style.css">
</head>
<body>


<nav class="navbar">
  <a class="navbar-brand" href="<?= APP_URL ?>/index.php">NGONSER<span>.ID</span></a>
  <div class="nav-links">
    <a href="#konser">Konser</a>
    <a href="#login" class="btn-nav btn">Masuk / Daftar</a>
  </div>
</nav>

<?php if ($flash): ?>
<div class="container" style="padding-top:1rem;">
  <div class="flash flash-<?= e($flash['type']) ?>"><?= e($flash['msg']) ?></div>
</div>
<?php endif; ?>


<section class="hero">
  <h1>RASAKAN <em>MAGIC</em><br>DARI PANGGUNG</h1>
  <p>Platform pemesanan tiket konser terpercaya di Indonesia. Dapatkan tiket favoritmu sebelum kehabisan!</p>
  <div class="hero-actions">
    <a href="#konser" class="btn btn-primary">🎵 Lihat Konser</a>
    <a href="#login"  class="btn btn-outline">Daftar Sekarang</a>
  </div>
</section>


<section id="konser" class="section">
  <div class="container">
    <div class="d-flex justify-between align-center flex-wrap mb-2">
      <div>
        <h2 class="section-title">KONSER MENDATANG</h2>
        <p class="section-subtitle">Jangan sampai ketinggalan!</p>
      </div>
    </div>
    <div class="grid-3">
      <?php foreach ($konser as $k): ?>
      <div class="card konser-card">
        <div class="card-img" style="background:linear-gradient(135deg,#0f1421,#161d2e);display:flex;align-items:center;justify-content:center;font-size:3rem;">🎤</div>
        <div class="card-body">
          <span class="kategori-tag"><?= e($k['kota']) ?></span>
          <h3><?= e($k['nama_konser']) ?></h3>
          <div class="meta">
            <span>🎸 <?= e($k['artis']) ?></span>
            <span>📍 <?= e($k['venue']) ?></span>
            <span>📅 <?= date('d M Y', strtotime($k['tanggal_konser'])) ?> · <?= substr($k['jam_mulai'],0,5) ?> WIB</span>
          </div>
          <?php if ($k['sisa_tiket'] > 0): ?>
            <div class="mb-2">
              <div class="d-flex justify-between" style="font-size:.8rem; margin-bottom:.3rem;">
                <span class="text-muted">Sisa tiket</span>
                <span class="text-accent"><?= number_format($k['sisa_tiket']) ?></span>
              </div>
            </div>
          <?php else: ?>
            <div class="mb-2"><span class="badge badge-danger">HABIS TERJUAL</span></div>
          <?php endif; ?>
          <div class="price-row">
            <div>
              <div style="font-size:.75rem;color:var(--text-muted);">Mulai dari</div>
              <div class="price"><?= formatRupiah($k['harga_mulai'] ?? 0) ?></div>
            </div>
            <a href="#login" class="btn btn-primary btn-sm">Beli Tiket</a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
      <?php if (empty($konser)): ?>
        <div style="grid-column:1/-1;text-align:center;padding:3rem;color:var(--text-muted);">
          Belum ada konser mendatang. Pantau terus!
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>


<section id="login" class="section" style="background:var(--bg-card);border-top:1px solid var(--border);border-bottom:1px solid var(--border);">
  <div class="container">
    <div class="grid-2" style="max-width:900px;margin:0 auto;gap:3rem;">

      
      <div>
        <h2 class="section-title">MASUK</h2>
        <p class="section-subtitle mb-3">Sudah punya akun? Login di sini.</p>
        <form method="POST" action="<?= APP_URL ?>/index.php">
          <input type="hidden" name="action" value="login">
          <div class="form-group">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" placeholder="email@kamu.com" required>
          </div>
          <div class="form-group">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
          </div>
          <button type="submit" class="btn btn-primary btn-block">Masuk →</button>
        </form>
        <p class="mt-2" style="font-size:.82rem;color:var(--text-muted);">
          Demo: <code>admin@ngonser.id</code> / <code>password</code>
        </p>
      </div>

      
      <div>
        <h2 class="section-title">DAFTAR</h2>
        <p class="section-subtitle mb-3">Belum punya akun? Buat sekarang.</p>
        <form method="POST" action="<?= APP_URL ?>/index.php">
          <input type="hidden" name="action" value="register">
          <div class="form-group">
            <label class="form-label">Nama Lengkap</label>
            <input type="text" name="nama" class="form-control" placeholder="Nama kamu" required>
          </div>
          <div class="form-group">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" placeholder="email@kamu.com" required>
          </div>
          <div class="form-group">
            <label class="form-label">No. HP</label>
            <input type="text" name="phone" class="form-control" placeholder="08xxxxxxxx">
          </div>
          <div class="form-group">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" placeholder="Min. 6 karakter" required>
          </div>
          <div class="form-group">
            <label class="form-label">Konfirmasi Password</label>
            <input type="password" name="confirm" class="form-control" placeholder="Ulangi password" required>
          </div>
          <button type="submit" class="btn btn-outline btn-block">Daftar Sekarang →</button>
        </form>
      </div>

    </div>
  </div>
</section>

<footer>
  <p>© <?= date('Y') ?> <strong><?= APP_NAME ?></strong> — UAP Pemrosesan Data Terdistribusi · Universitas Lampung</p>
</footer>
<script src="<?= APP_URL ?>/assets/js/main.js"></script>
</body>
</html>
