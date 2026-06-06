<?php
require_once __DIR__ . '/../config/session.php';
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth_helper.php';
require_once __DIR__ . '/../models/UserModel.php';

pastikanStrukturUser($conn);

$aksi = $_GET['action'] ?? '';

if ($aksi === 'login') {
    prosesLogin();
} elseif ($aksi === 'register') {
    prosesRegister();
} elseif ($aksi === 'logout') {
    prosesLogout();
} else {
    include __DIR__ . '/../views/auth/login.php';
}

function prosesLogin() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        include __DIR__ . '/../views/auth/login.php';
        return;
    }

    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        $pesanError = "Email dan password wajib diisi.";
        include __DIR__ . '/../views/auth/login.php';
        return;
    }

    global $conn;
    $dataUser = cariPelangganByEmail($conn, $email);

    if ($dataUser && password_verify($password, $dataUser['password'])) {

        $_SESSION['user_id'] = $dataUser['id'];
        $_SESSION['nama']    = $dataUser['nama'];
        $_SESSION['email']   = $dataUser['email'];
        $_SESSION['role']    = $dataUser['role'];

        header("Location: index.php?action=home");
        exit;

    } else {
        $pesanError = "Email atau password salah.";
        include __DIR__ . '/../views/auth/login.php';
    }
}

function prosesRegister() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        include __DIR__ . '/../views/auth/register.php';
        return;
    }

    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telepon = trim($_POST['telepon'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $konfirmasiPassword = trim($_POST['konfirmasi_password'] ?? '');

    if ($nama === '' || $email === '' || $telepon === '' || $password === '' || $konfirmasiPassword === '') {
        $pesanError = "Semua data registrasi wajib diisi.";
        include __DIR__ . '/../views/auth/register.php';
        return;
    }

    if (!preg_match("/^[A-Za-z\s']+$/", $nama) || strlen($nama) > 100) {
        $pesanError = "Nama hanya boleh berisi huruf, spasi, tanda petik tunggal, dan maksimal 100 karakter.";
        include __DIR__ . '/../views/auth/register.php';
        return;
    }

    if (!preg_match("/^[0-9]{8,13}$/", $telepon)) {
        $pesanError = "Nomor telepon harus berupa angka antara 8 sampai 13 digit.";
        include __DIR__ . '/../views/auth/register.php';
        return;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $pesanError = "Format email belum benar.";
        include __DIR__ . '/../views/auth/register.php';
        return;
    }

    if (strlen($password) < 6) {
        $pesanError = "Password minimal 6 karakter.";
        include __DIR__ . '/../views/auth/register.php';
        return;
    }

    if ($password !== $konfirmasiPassword) {
        $pesanError = "Konfirmasi password tidak sama.";
        include __DIR__ . '/../views/auth/register.php';
        return;
    }

    global $conn;

    if (emailPelangganSudahAda($conn, $email)) {
        $pesanError = "Email sudah terdaftar.";
        include __DIR__ . '/../views/auth/register.php';
        return;
    }

    if (buatPelanggan($conn, $nama, $email, $telepon, $password)) {
        $pesanSukses = "Registrasi berhasil. Silakan login.";
        include __DIR__ . '/../views/auth/login.php';
    } else {
        $pesanError = "Registrasi gagal. Silakan coba lagi.";
        include __DIR__ . '/../views/auth/register.php';
    }
}

function prosesLogout() {
    session_unset(); 
    session_destroy();
    header("Location: index.php?action=home");
    exit;
}
?>
