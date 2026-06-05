<?php
require_once __DIR__ . '/config/database.php';

if (session_status() === PHP_SESSION_NONE) session_start();

session_destroy();
$_SESSION = [];

redirect(APP_URL . '/index.php');
