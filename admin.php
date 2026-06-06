<?php

require_once __DIR__ . '/config/session.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

define('BASE_URL', 'admin.php');

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/BaseModel.php';

$page   = isset($_GET['page'])   ? trim($_GET['page'])   : 'dashboard';
$action = isset($_GET['action']) ? trim($_GET['action']) : 'index';

$isLoggedIn = isset($_SESSION['adminLoggedIn']) && $_SESSION['adminLoggedIn'] === true;

if (!$isLoggedIn && $page !== 'login') {
    header('Location: ' . BASE_URL . '?page=login');
    exit();
}

if ($isLoggedIn && $page === 'login') {
    header('Location: ' . BASE_URL . '?page=dashboard');
    exit();
}

if ($isLoggedIn) {
    require_once __DIR__ . '/models/Reservasi.php';
    (new Reservasi())->batalkanPendingLewatJamBooking();
}

switch ($page) {
    case 'login':
        require_once __DIR__ . '/controllers/AdminAuthController.php';
        $controller = new AdminAuthController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->processLogin();
        } else {
            $controller->showLoginForm();
        }
        break;

    case 'logout':
        require_once __DIR__ . '/controllers/AdminAuthController.php';
        $controller = new AdminAuthController();
        $controller->logout();
        break;

    case 'dashboard':
        require_once __DIR__ . '/controllers/DashboardController.php';
        $controller = new DashboardController();
        $controller->index();
        break;

    case 'terapis':
        require_once __DIR__ . '/controllers/TerapisController.php';
        $controller = new TerapisController();
        if ($action === 'create') {
            $controller->create();
        } elseif ($action === 'edit') {
            $controller->edit();
        } elseif ($action === 'delete') {
            $controller->delete();
        } else {
            $controller->index();
        }
        break;

    case 'layanan':
        require_once __DIR__ . '/controllers/LayananController.php';
        $controller = new LayananController();
        if ($action === 'create') {
            $controller->create();
        } elseif ($action === 'edit') {
            $controller->edit();
        } elseif ($action === 'delete') {
            $controller->delete();
        } else {
            $controller->index();
        }
        break;

    case 'reservasi':
        require_once __DIR__ . '/controllers/ReservasiController.php';
        $controller = new ReservasiController();
        if ($action === 'detail') {
            $controller->detail();
        } elseif ($action === 'assign') {
            $controller->assignTerapis();
        } elseif ($action === 'pelunasan') {
            $controller->pelunasan();
        } else {
            $controller->index();
        }
        break;

    case 'pembayaran':
        require_once __DIR__ . '/controllers/PembayaranController.php';
        $controller = new PembayaranController();
        if ($action === 'verifikasi') {
            $controller->verifikasi();
        } elseif ($action === 'rekening') {
            $controller->rekening();
        } elseif ($action === 'tambah_rekening') {
            $controller->tambahRekening();
        } elseif ($action === 'hapus_rekening') {
            $controller->hapusRekening();
        } else {
            $controller->index();
        }
        break;

    case 'transaksi':
        require_once __DIR__ . '/controllers/TransaksiController.php';
        $controller = new TransaksiController();
        if ($action === 'create') {
            $controller->create();
        } elseif ($action === 'detail') {
            $controller->detail();
        } elseif ($action === 'nota') {
            $controller->nota();
        } else {
            $controller->index();
        }
        break;

    case 'laporan':
        require_once __DIR__ . '/controllers/TransaksiController.php';
        $controller = new TransaksiController();
        if ($action === 'cetak') {
            $controller->cetakLaporan();
        } else {
            $controller->laporan();
        }
        break;

    case 'ulasan':
        require_once __DIR__ . '/controllers/UlasanController.php';
        $controller = new UlasanController();
        if ($action === 'delete') {
            $controller->delete();
        } elseif ($action === 'balas') {
            $controller->balas();
        } else {
            $controller->index();
        }
        break;

    case 'ruangan':
        require_once __DIR__ . '/controllers/RuanganController.php';
        $controller = new RuanganController();
        if ($action === 'edit') {
            $controller->edit();
        } elseif ($action === 'toggle') {
            $controller->toggleStatus();
        } else {
            $controller->index();
        }
        break;

    case 'pengaturan':
        require_once __DIR__ . '/controllers/PengaturanController.php';
        $controller = new PengaturanController();
        if ($action === 'update_tampilan') {
            $controller->updateTampilan();
        } elseif ($action === 'update_sesi') {
            $controller->updateSesi();
        } elseif ($action === 'tambah_rekening') {
            $controller->tambahRekening();
        } elseif ($action === 'hapus_rekening') {
            $controller->hapusRekening();
        } else {
            $controller->index();
        }
        break;

    default:
        header('Location: ' . BASE_URL . '?page=dashboard');
        exit();
}
?>
