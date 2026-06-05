<?php
define('DB_HOST',   'localhost');
define('DB_USER',   'root');
define('DB_PASS',   '');
define('DB_NAME',   'ngonser');
define('DB_PORT',   3306);
define('DB_CHARSET','utf8mb4');

define('APP_NAME',  'Ngonser.id');
define('APP_URL',   'http://localhost/PDT');
define('APP_VERSION','1.0.0');

function getDB(): mysqli {
    static $conn = null;
    if ($conn === null) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
        if ($conn->connect_error) {
            die(json_encode([
                'status'  => 'error',
                'message' => 'Koneksi database gagal: ' . $conn->connect_error
            ]));
        }
        $conn->set_charset(DB_CHARSET);
    }
    return $conn;
}

function e(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function sanitize(string $str): string {
    return trim(strip_tags($str));
}

function redirect(string $url): void {
    header("Location: $url");
    exit;
}

function flashMessage(string $type, string $msg): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}

function getFlash(): ?array {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function isLoggedIn(): bool {
    if (session_status() === PHP_SESSION_NONE) session_start();
    return isset($_SESSION['user_id']);
}

function isAdmin(): bool {
    if (session_status() === PHP_SESSION_NONE) session_start();
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function requireLogin(): void {
    if (!isLoggedIn()) redirect(APP_URL . '/index.php');
}

function requireAdmin(): void {
    requireLogin();
    if (!isAdmin()) redirect(APP_URL . '/dashboard.php');
}

function formatRupiah(float $angka): string {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

function generateKodeTrx(int $userId): string {
    return 'TRX-' . date('Ymd') . '-' . str_pad($userId, 4, '0', STR_PAD_LEFT)
         . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
}

function slugStatus(string $status): string {
    return match($status) {
        'paid'      => '<span class="badge badge-success">Lunas</span>',
        'pending'   => '<span class="badge badge-warning">Menunggu Bayar</span>',
        'cancelled' => '<span class="badge badge-danger">Dibatalkan</span>',
        'refunded'  => '<span class="badge badge-info">Refunded</span>',
        default     => '<span class="badge badge-secondary">' . e($status) . '</span>'
    };
}
