<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/database.php';
$flash = getFlash();
$isAdmin = isAdmin();
$isLogged = isLoggedIn();
$userName = $_SESSION['nama'] ?? '';
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($pageTitle) ? e($pageTitle) . ' — ' : '' ?><?= APP_NAME ?></title>
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/style.css">
  <link rel="icon" href="<?= APP_URL ?>/assets/img/favicon.ico" type="image/x-icon">
</head>
<body>
<nav class="navbar">
  <a class="navbar-brand" href="<?= APP_URL ?>/index.php">NGONSER<span>.ID</span></a>
  <div class="nav-links">
    <a href="<?= APP_URL ?>/index.php"     class="<?= $currentPage==='index.php'?'active':'' ?>">Beranda</a>
    <?php if ($isLogged): ?>
      <a href="<?= APP_URL ?>/dashboard.php" class="<?= $currentPage==='dashboard.php'?'active':'' ?>">Dashboard</a>
      <?php if ($isAdmin): ?>
        <a href="<?= APP_URL ?>/admin.php"   class="<?= $currentPage==='admin.php'?'active':'' ?>">Admin Panel</a>
      <?php endif; ?>
      <a href="<?= APP_URL ?>/logout.php" class="btn btn-outline btn-sm">Keluar (<?= e($userName) ?>)</a>
    <?php else: ?>
      <a href="<?= APP_URL ?>/index.php#login" class="btn-nav btn">Masuk</a>
    <?php endif; ?>
  </div>
</nav>
<main>
<?php if ($flash): ?>
  <div class="container" style="padding-top:1rem;">
    <div class="flash flash-<?= e($flash['type']) ?>">
      <?= e($flash['msg']) ?>
    </div>
  </div>
<?php endif; ?>
