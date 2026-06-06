<?php

require_once __DIR__ . '/../config/session.php';
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth_helper.php';
require_once __DIR__ . '/../models/UserModel.php';

define('LAYANAN_PER_HALAMAN', 6);
define('MAKS_UKURAN_BUKTI_BAYAR', 2 * 1024 * 1024);
define('PASSWORD_MIN_LENGTH', 6);

pastikanStrukturUser($conn);
hanguskanReservasiKadaluwarsa($conn);

$aksi = $_GET['action'] ?? 'dashboard';

if ($aksi === 'home') {
    tampilBeranda();
} elseif ($aksi === 'dashboard') {
    requireLogin();
    tampilDashboard();
} elseif ($aksi === 'layanan') {
    tampilLayanan();
} elseif ($aksi === 'detail-layanan') {
    tampilDetailLayanan();
} elseif ($aksi === 'keranjang') {
    requireLogin();
    tampilKeranjang();
} elseif ($aksi === 'tambah-keranjang') {
    requireLogin();
    prosesTambahKeranjang();
} elseif ($aksi === 'reservasi-langsung') {
    requireLogin();
    prosesReservasiLangsung();
} elseif ($aksi === 'hapus-keranjang') {
    requireLogin();
    prosesHapusKeranjang();
} elseif ($aksi === 'kosongkan-keranjang') {
    requireLogin();
    prosesKosongkanKeranjang();
} elseif ($aksi === 'reservasi') {
    requireLogin();
    tampilReservasi();
} elseif ($aksi === 'simpan-reservasi') {
    requireLogin();
    prosesReservasi();
} elseif ($aksi === 'pembayaran') {
    requireLogin();
    tampilPembayaran();
} elseif ($aksi === 'upload-pembayaran') {
    requireLogin();
    prosesUploadPembayaran();
} elseif ($aksi === 'riwayat') {
    requireLogin();
    tampilRiwayat();
} elseif ($aksi === 'profil') {
    requireLogin();
    tampilProfil();
} elseif ($aksi === 'simpan-profil') {
    requireLogin();
    prosesProfil();
} elseif ($aksi === 'ulasan') {
    requireLogin();
    tampilUlasan();
} elseif ($aksi === 'simpan-ulasan') {
    requireLogin();
    prosesUlasan();
} else {
    tampilBeranda();
}

function tampilBeranda() {
    global $conn;
    $layanan = array_slice(ambilLayanan($conn), 0, LAYANAN_PER_HALAMAN);
    $terapis = ambilTerapis($conn);
    include __DIR__ . '/../views/user/home/index.php';
}

function tampilDashboard() {
    global $conn;
    hanguskanReservasiKadaluwarsa($conn);
    $userId   = (int) $_SESSION['user_id'];
    $reservasi = ambilReservasiPelanggan($conn, $userId);
    include __DIR__ . '/../views/user/riwayat/index.php';
}

function tampilLayanan() {
    global $conn;
    $keyword  = trim($_GET['keyword']  ?? '');
    $kategori = trim($_GET['kategori'] ?? '');
    $durasi   = trim($_GET['durasi']   ?? '');
    $sort     = trim($_GET['sort']     ?? '');

    $kategoriLayanan = ambilKategoriLayanan($conn);
    $semuaLayanan    = ambilLayanan($conn, $keyword, $kategori, $durasi, $sort);

    $semuaLayanan = tandaiLayananPremium($semuaLayanan);

    $totalLayanan = count($semuaLayanan);
    $page         = max(1, (int)($_GET['page'] ?? 1));
    $totalHalaman = (int) ceil($totalLayanan / LAYANAN_PER_HALAMAN);

    if ($totalHalaman > 0 && $page > $totalHalaman) {
        $page = $totalHalaman;
    }

    $offset  = ($page - 1) * LAYANAN_PER_HALAMAN;
    $layanan = array_slice($semuaLayanan, $offset, LAYANAN_PER_HALAMAN);

    include __DIR__ . '/../views/user/layanan/index.php';
}

function tandaiLayananPremium($semuaLayanan) {
    $hitungNama = [];
    foreach ($semuaLayanan as $item) {
        $hitungNama[trim($item['nama_layanan'])][] = $item['durasi'];
    }

    $namaGanda = [];
    foreach ($hitungNama as $nama => $daftarDurasi) {
        if (count($daftarDurasi) > 1) {
            $namaGanda[$nama] = max($daftarDurasi);
        }
    }

    foreach ($semuaLayanan as &$item) {
        $nama = trim($item['nama_layanan']);
        if (isset($namaGanda[$nama]) && $item['durasi'] == $namaGanda[$nama]) {
            $item['nama_layanan'] = $nama . ' (Premium)';
        }
    }
    unset($item);

    return $semuaLayanan;
}

function tampilDetailLayanan() {
    global $conn;
    $id      = (int) ($_GET['id'] ?? 0);
    $layanan = ambilLayananById($conn, $id);

    if (!$layanan) {
        http_response_code(404);
        include __DIR__ . '/../views/user/tidak_ditemukan/index.php';
        return;
    }

    $terapis = array_values(array_filter(ambilTerapis($conn), function($item) use ($layanan) {
        $status = strtolower($item['status'] ?? '');
        if ($status !== 'aktif') {
            return false;
        }

        $serviceName = strtolower((string) ($layanan['nama_layanan'] ?? ''));
        $serviceName = preg_replace('/\s*\([^)]*\)\s*/', ' ', $serviceName);
        $serviceName = preg_replace('/\s+/', ' ', trim($serviceName));

        $specialization = strtolower((string) ($item['spesialisasi'] ?? ''));
        $specialization = preg_replace('/\s+/', ' ', trim($specialization));

        return $serviceName !== '' && strpos($specialization, $serviceName) !== false;
    }));
    $ulasan  = ambilUlasanLayanan($conn, $id);
    include __DIR__ . '/../views/user/layanan/detail.php';
}

function tampilReservasi() {
    $cartItems = getCart();

    if (empty($cartItems)) {
        header("Location: index.php?action=layanan&pesan_error=" . urlencode("Keranjang belanja Anda kosong! Silakan pilih layanan terlebih dahulu."));
        exit;
    }

    if (!hasMainServiceInCart()) {
        header("Location: index.php?action=layanan&pesan_error=" . urlencode("Anda harus memilih setidaknya satu layanan utama sebelum melakukan reservasi!"));
        exit;
    }

    tampilFormReservasi();
}

function prosesReservasi() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: index.php?action=reservasi");
        exit;
    }

    global $conn;
    $userId        = (int) $_SESSION['user_id'];
    $genderTerapis = trim($_POST['gender_terapis'] ?? 'Bebas');
    $tanggal       = trim($_POST['tanggal']        ?? '');
    $jam           = trim($_POST['jam']            ?? '');
    $catatan       = trim($_POST['catatan']        ?? '');

    $cartItems = getCart();
    if (empty($cartItems)) {
        header("Location: index.php?action=layanan&pesan_error=" . urlencode("Keranjang belanja Anda kosong!"));
        exit;
    }

    if (!hasMainServiceInCart()) {
        header("Location: index.php?action=layanan&pesan_error=" . urlencode("Anda harus memilih setidaknya satu layanan utama sebelum melakukan reservasi!"));
        exit;
    }

    if ($tanggal === '' || $jam === '') {
        tampilFormReservasi("Data reservasi belum lengkap.");
        return;
    }

    if ($tanggal < date('Y-m-d')) {
        tampilFormReservasi("Tanggal reservasi tidak boleh sebelum hari ini.");
        return;
    }

    if ($tanggal === date('Y-m-d') && $jam < date('H:i')) {
        tampilFormReservasi("Jam reservasi untuk hari ini tidak boleh sebelum jam sekarang.");
        return;
    }

    $layananIds  = array_keys($cartItems);
    try {
        $reservasiId = simpanReservasi($conn, $userId, $layananIds, $genderTerapis, $tanggal, $jam, $catatan);
        if ($reservasiId) {
            clearCart();
            header("Location: index.php?action=pembayaran&id=" . $reservasiId);
            exit;
        }
        tampilFormReservasi("Reservasi gagal disimpan.");
    } catch (Exception $e) {
        tampilFormReservasi($e->getMessage());
    }
}

function tampilFormReservasi($pesanError = null) {
    global $conn;
    $terapis = ambilTerapis($conn);
    include __DIR__ . '/../views/user/reservasi/index.php';
}

function tampilPembayaran() {
    global $conn;
    hanguskanReservasiKadaluwarsa($conn);
    $userId      = (int) $_SESSION['user_id'];
    $reservasiId = (int) ($_GET['id'] ?? 0);
    $reservasi   = ambilReservasiById($conn, $reservasiId, $userId);

    if (!$reservasi) {
        http_response_code(404);
        include __DIR__ . '/../views/user/tidak_ditemukan/index.php';
        return;
    }

    include __DIR__ . '/../views/user/pembayaran/index.php';
}

function prosesUploadPembayaran() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: index.php?action=riwayat");
        exit;
    }

    global $conn;
    $userId      = (int) $_SESSION['user_id'];
    $reservasiId = (int) ($_POST['reservasi_id'] ?? 0);
    $rekeningId  = (int) ($_POST['rekening_id'] ?? 0);
    $jenisPembayaran = trim($_POST['jenis_pembayaran'] ?? 'DP 50%');
    $reservasi   = ambilReservasiById($conn, $reservasiId, $userId);

    if (!$reservasi) {
        header("Location: index.php?action=riwayat&pesan=reservasi-tidak-valid");
        exit;
    }

    if (!in_array($jenisPembayaran, ['DP 50%', 'Lunas 100%'], true)) {
        header("Location: index.php?action=pembayaran&id=" . $reservasiId . "&pesan=jenis-pembayaran-tidak-valid");
        exit;
    }

    if (($reservasi['status_reservasi'] ?? '') !== 'Menunggu Pembayaran') {
        header("Location: index.php?action=riwayat&pesan=reservasi-tidak-valid");
        exit;
    }

    $createdTime = strtotime($reservasi['created_at'] ?? '');
    if ($createdTime && time() >= ($createdTime + MENIT_KADALUWARSA_RESERVASI * 60)) {
        hanguskanReservasiKadaluwarsa($conn);
        header("Location: index.php?action=pembayaran&id=" . $reservasiId);
        exit;
    }

    $rekeningTujuan = ambilRekeningById($conn, $rekeningId);
    if (!$rekeningTujuan) {
        header("Location: index.php?action=pembayaran&id=" . $reservasiId . "&pesan=rekening-tidak-valid");
        exit;
    }

    if (!isset($_FILES['bukti']) || $_FILES['bukti']['error'] !== UPLOAD_ERR_OK) {
        header("Location: index.php?action=pembayaran&id=" . $reservasiId . "&pesan=upload-gagal");
        exit;
    }

    $file       = $_FILES['bukti'];
    $ekstensi   = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $formatValid = in_array($ekstensi, ['jpg', 'jpeg', 'png'], true);
    $ukuranValid = $file['size'] <= MAKS_UKURAN_BUKTI_BAYAR;

    if (!$formatValid || !$ukuranValid) {
        header("Location: index.php?action=pembayaran&id=" . $reservasiId . "&pesan=format-bukti-salah");
        exit;
    }

    $folderUpload = __DIR__ . '/../uploads/pembayaran';
    if (!is_dir($folderUpload)) {
        mkdir($folderUpload, 0755, true);
    }

    $namaFile = 'bukti_' . $reservasiId . '_' . time() . '.' . $ekstensi;
    $tujuan   = $folderUpload . '/' . $namaFile;

    $metodePembayaran = 'Transfer ' . $rekeningTujuan['nama_bank'];
    $nominalPembayaran = $jenisPembayaran === 'Lunas 100%' ? $reservasi['harga'] : ($reservasi['harga'] * 0.5);
    if (move_uploaded_file($file['tmp_name'], $tujuan) && simpanBuktiPembayaran($conn, $reservasiId, $namaFile, $metodePembayaran, $jenisPembayaran, $nominalPembayaran)) {
        header("Location: index.php?action=riwayat&pesan=upload-berhasil");
        exit;
    }

    header("Location: index.php?action=pembayaran&id=" . $reservasiId . "&pesan=upload-gagal");
    exit;
}

function tampilRiwayat() {
    global $conn;
    hanguskanReservasiKadaluwarsa($conn);
    $userId    = (int) $_SESSION['user_id'];
    $reservasi = ambilReservasiPelanggan($conn, $userId);
    include __DIR__ . '/../views/user/riwayat/index.php';
}

function tampilProfil() {
    global $conn;
    $user = cariPelangganById($conn, (int) $_SESSION['user_id']);
    include __DIR__ . '/../views/user/profil/index.php';
}

function prosesProfil() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: index.php?action=profil");
        exit;
    }

    global $conn;
    $userId      = (int) $_SESSION['user_id'];
    $nama        = trim($_POST['nama']         ?? '');
    $telepon     = trim($_POST['telepon']      ?? '');
    $passwordBaru = trim($_POST['password_baru'] ?? '');

    if ($nama === '' || $telepon === '') {
        $pesanError = "Nama dan nomor telepon wajib diisi.";
        $user = cariPelangganById($conn, $userId);
        include __DIR__ . '/../views/user/profil/index.php';
        return;
    }

    if (!preg_match("/^[A-Za-z\s']+$/", $nama) || strlen($nama) > 100) {
        $pesanError = "Nama hanya boleh berisi huruf, spasi, tanda petik tunggal, dan maksimal 100 karakter.";
        $user = cariPelangganById($conn, $userId);
        include __DIR__ . '/../views/user/profil/index.php';
        return;
    }

    if (!preg_match("/^[0-9]{8,13}$/", $telepon)) {
        $pesanError = "Nomor telepon harus berupa angka antara 8 sampai 13 digit.";
        $user = cariPelangganById($conn, $userId);
        include __DIR__ . '/../views/user/profil/index.php';
        return;
    }

    updateProfilPelanggan($conn, $userId, $nama, $telepon);
    $_SESSION['nama'] = $nama;

    $pesanErrorPassword = validasiDanUpdatePassword($conn, $userId, $passwordBaru);
    if ($pesanErrorPassword !== null) {
        $pesanError = $pesanErrorPassword;
        $user = cariPelangganById($conn, $userId);
        include __DIR__ . '/../views/user/profil/index.php';
        return;
    }

    header("Location: index.php?action=profil&pesan=profil-berhasil");
    exit;
}

function validasiDanUpdatePassword($conn, $userId, $passwordBaru) {
    if ($passwordBaru === '') return null;

    if (strlen($passwordBaru) < PASSWORD_MIN_LENGTH) {
        return "Password baru minimal " . PASSWORD_MIN_LENGTH . " karakter.";
    }

    updatePasswordPelanggan($conn, $userId, $passwordBaru);
    return null;
}

function ambilReservasiSelesaiTanpaUlasan($conn, $userId) {
    $semuaReservasi = ambilReservasiPelanggan($conn, $userId);
    return array_filter($semuaReservasi, fn($item) =>
        $item['status_reservasi'] === 'Selesai' && !ulasanSudahAda($conn, (int)$item['id'])
    );
}

function tampilUlasan() {
    global $conn;
    $userId   = (int) $_SESSION['user_id'];
    $reservasi = array_values(ambilReservasiSelesaiTanpaUlasan($conn, $userId));
    $ulasanPelanggan = ambilUlasanPelanggan($conn, $userId);
    include __DIR__ . '/../views/user/ulasan/index.php';
}

function prosesUlasan() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: index.php?action=ulasan");
        exit;
    }

    global $conn;
    $userId      = (int) $_SESSION['user_id'];
    $reservasiId = (int) ($_POST['reservasi_id'] ?? 0);
    $rating      = (int) ($_POST['rating']       ?? 0);
    $isiUlasan   = trim($_POST['isi_ulasan']      ?? '');

    $reservasi = ambilReservasiById($conn, $reservasiId, $userId);

    if (!$reservasi || $reservasi['status_reservasi'] !== 'Selesai' || ulasanSudahAda($conn, $reservasiId)) {
        header("Location: index.php?action=ulasan&pesan=ulasan-tidak-valid");
        exit;
    }

    if ($rating < 1 || $rating > 5 || $isiUlasan === '') {
        $pesanError = "Data ulasan belum lengkap.";
        $reservasi  = array_values(ambilReservasiSelesaiTanpaUlasan($conn, $userId));
        $ulasanPelanggan = ambilUlasanPelanggan($conn, $userId);
        include __DIR__ . '/../views/user/ulasan/index.php';
        return;
    }

    simpanUlasan($conn, $userId, (int) $reservasi['layanan_id'], $reservasiId, $rating, $isiUlasan);
    header("Location: index.php?action=ulasan&pesan=ulasan-berhasil");
    exit;
}

function tampilKeranjang() {
    global $conn;
    $cart = getCart();

    foreach ($cart as $id => $item) {
        if (!empty($item['media'])) {
            continue;
        }

        $layanan = ambilLayananById($conn, (int)$id);
        if ($layanan && !empty($layanan['media'])) {
            $_SESSION['cart'][$id]['media'] = $layanan['media'];
            $cart[$id]['media'] = $layanan['media'];
        }
    }

    include __DIR__ . '/../views/user/keranjang/index.php';
}

function prosesTambahKeranjang() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        if (isAjaxRequest()) {
            jsonCartResponse(['success' => false, 'message' => 'Metode request tidak valid.'], 405);
        }
        header("Location: index.php?action=layanan");
        exit;
    }

    global $conn;
    $layananId = (int)($_POST['layanan_id'] ?? 0);
    $layanan   = ambilLayananById($conn, $layananId);

    if (!$layanan) {
        if (isAjaxRequest()) {
            jsonCartResponse(['success' => false, 'message' => 'Layanan tidak ditemukan!'], 404);
        }
        header("Location: index.php?action=layanan&pesan_error=" . urlencode("Layanan tidak ditemukan!"));
        exit;
    }

    $res      = addToCart($layanan['id'], $layanan['nama_layanan'], $layanan['harga'], $layanan['kategori'], $layanan['durasi'], $layanan['media'] ?? '');
    if (isAjaxRequest()) {
        jsonCartResponse($res, $res['success'] ? 200 : 422, [
            'mode' => 'add',
            'layanan_id' => (int)$layanan['id'],
            'in_cart' => isInCart($layanan['id']),
        ]);
    }

    $referer  = bersihkanReferer($_POST['redirect_to'] ?? 'index.php?action=layanan');
    $separator = strpos($referer, '?') !== false ? '&' : '?';

    $param = $res['success'] ? 'pesan_sukses' : 'pesan_error';
    header("Location: " . $referer . $separator . $param . "=" . urlencode($res['message']));
    exit;
}

function prosesReservasiLangsung() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: index.php?action=layanan");
        exit;
    }

    global $conn;
    $layananId = (int)($_POST['layanan_id'] ?? 0);
    $layanan   = ambilLayananById($conn, $layananId);

    if (!$layanan) {
        header("Location: index.php?action=layanan&pesan_error=" . urlencode("Layanan tidak ditemukan."));
        exit;
    }

    $isAddon = ($layanan['kategori'] === 'Tambahan' || $layanan['kategori'] === 'Tambahan Bekam');
    if ($isAddon) {
        header("Location: index.php?action=detail-layanan&id=" . $layananId . "&pesan_error=" . urlencode("Layanan tambahan harus digabung dengan layanan utama melalui keranjang."));
        exit;
    }

    clearCart();
    $res = addToCart($layanan['id'], $layanan['nama_layanan'], $layanan['harga'], $layanan['kategori'], $layanan['durasi'], $layanan['media'] ?? '');

    if (!$res['success']) {
        header("Location: index.php?action=detail-layanan&id=" . $layananId . "&pesan_error=" . urlencode($res['message']));
        exit;
    }

    header("Location: index.php?action=reservasi");
    exit;
}

function prosesHapusKeranjang() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        if (isAjaxRequest()) {
            jsonCartResponse(['success' => false, 'message' => 'Metode request tidak valid.'], 405);
        }
        header("Location: index.php?action=keranjang");
        exit;
    }

    $layananId = (int)($_POST['layanan_id'] ?? 0);
    $res       = removeFromCart($layananId);
    if (isAjaxRequest()) {
        jsonCartResponse($res, $res['success'] ? 200 : 422, [
            'mode' => 'remove',
            'layanan_id' => $layananId,
            'in_cart' => isInCart($layananId),
        ]);
    }

    $referer   = bersihkanReferer($_POST['redirect_to'] ?? 'index.php?action=keranjang');
    $separator = strpos($referer, '?') !== false ? '&' : '?';

    if ($res['success']) {
        $param = isset($res['cleared']) && $res['cleared'] ? 'pesan_error' : 'pesan_sukses';
        header("Location: " . $referer . $separator . $param . "=" . urlencode($res['message']));
    } else {
        header("Location: " . $referer . $separator . "pesan_error=" . urlencode($res['message']));
    }
    exit;
}

function prosesKosongkanKeranjang() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: index.php?action=keranjang");
        exit;
    }

    clearCart();
    header("Location: index.php?action=keranjang&pesan_sukses=" . urlencode("Keranjang belanja berhasil dikosongkan."));
    exit;
}

function bersihkanReferer($referer) {
    return preg_replace('/[&?](pesan_sukses|pesan_error)=[^&]*/', '', $referer);
}

function isAjaxRequest() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH'])
        && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

function jsonCartResponse($res, $status = 200, $extra = []) {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode(array_merge($res, $extra, [
        'cart_count' => cartCount(),
    ]));
    exit;
}
?>
