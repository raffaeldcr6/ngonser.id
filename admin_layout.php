<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/config/database.php';
requireAdmin();
$flash = getFlash();
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($pageTitle) ? e($pageTitle) . ' — ' : '' ?>Admin · <?= APP_NAME ?></title>
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/style.css">
</head>
<body>
<div class="layout-admin">
  <aside class="sidebar">
    <div class="sidebar-brand">
      NGONSER<span style="color:var(--text-muted)">.ID</span>
      <small>Admin Panel</small>
    </div>
    <nav class="sidebar-menu">
      <div class="sidebar-section">Utama</div>
      <a href="<?= APP_URL ?>/admin.php" class="sidebar-item <?= $currentPage==='admin.php' && empty($_GET['page']) ? 'active' : '' ?>">
        <span class="icon">📊</span> Dashboard
      </a>
      <a href="<?= APP_URL ?>/dashboard.php" class="sidebar-item">
        <span class="icon">🏠</span> Kembali ke Beranda
      </a>

      <div class="sidebar-section">Manajemen</div>
      <a href="<?= APP_URL ?>/admin.php?page=konser" class="sidebar-item <?= ($_GET['page']??'')==='konser'?'active':'' ?>">
        <span class="icon">🎵</span> Konser
      </a>
      <a href="<?= APP_URL ?>/admin.php?page=tiket" class="sidebar-item <?= ($_GET['page']??'')==='tiket'?'active':'' ?>">
        <span class="icon">🎟️</span> Tiket
      </a>
      <a href="<?= APP_URL ?>/admin.php?page=transaksi" class="sidebar-item <?= ($_GET['page']??'')==='transaksi'?'active':'' ?>">
        <span class="icon">💳</span> Transaksi
      </a>
      <a href="<?= APP_URL ?>/admin.php?page=users" class="sidebar-item <?= ($_GET['page']??'')==='users'?'active':'' ?>">
        <span class="icon">👥</span> Pengguna
      </a>

      <div class="sidebar-section">PDT Advanced</div>
      <a href="<?= APP_URL ?>/backup.php" class="sidebar-item <?= $currentPage==='backup.php'?'active':'' ?>">
        <span class="icon">💾</span> Backup Database
      </a>
      <a href="<?= APP_URL ?>/backup_list.php" class="sidebar-item <?= $currentPage==='backup_list.php'?'active':'' ?>">
        <span class="icon">📂</span> Daftar Backup
      </a>
      <a href="<?= APP_URL ?>/admin.php?page=fragmentasi" class="sidebar-item <?= ($_GET['page']??'')==='fragmentasi'?'active':'' ?>">
        <span class="icon">🧩</span> Fragmentasi Data
      </a>
      <a href="<?= APP_URL ?>/admin.php?page=deadlock" class="sidebar-item <?= ($_GET['page']??'')==='deadlock'?'active':'' ?>">
        <span class="icon">🔄</span> Simulasi Deadlock
      </a>

      <div class="sidebar-section">Akun</div>
      <a href="<?= APP_URL ?>/logout.php" class="sidebar-item">
        <span class="icon">🚪</span> Keluar
      </a>
    </nav>
    <div style="padding:1rem 1.5rem; font-size:.75rem; color:var(--text-muted);">
      Login sebagai: <strong style="color:var(--accent);"><?= e($_SESSION['nama']??'Admin') ?></strong>
    </div>
  </aside>

  <div class="main-content">
    <?php if ($flash): ?>
      <div class="flash flash-<?= e($flash['type']) ?>"><?= e($flash['msg']) ?></div>
    <?php endif; ?>