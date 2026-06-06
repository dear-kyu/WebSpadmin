<?php
date_default_timezone_set('Asia/Jakarta');
require_once __DIR__ . '/config/session.php';
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/auth_helper.php';
require_once __DIR__ . '/config/CartHelper.php';
require_once __DIR__ . '/models/UserModel.php';

$aksi = $_GET['action'] ?? 'home';

switch ($aksi) {
    case 'login':
    case 'register':
    case 'logout':
        require_once __DIR__ . '/controllers/AuthController.php';
        break;

    case 'home':
    case 'dashboard':
    case 'layanan':
    case 'detail-layanan':
    case 'keranjang':
    case 'tambah-keranjang':
    case 'reservasi-langsung':
    case 'hapus-keranjang':
    case 'kosongkan-keranjang':
    case 'reservasi':
    case 'simpan-reservasi':
    case 'pembayaran':
    case 'upload-pembayaran':
    case 'riwayat':
    case 'profil':
    case 'simpan-profil':
    case 'ulasan':
    case 'simpan-ulasan':
        require_once __DIR__ . '/controllers/UserController.php';
        break;

    default:
        header("Location: index.php?action=home");
        exit;
}
?>
