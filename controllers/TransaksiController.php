<?php

require_once __DIR__ . '/../models/Transaksi.php';
require_once __DIR__ . '/../models/Layanan.php';
require_once __DIR__ . '/../models/Pelanggan.php';
require_once __DIR__ . '/../models/Reservasi.php';
require_once __DIR__ . '/../models/Terapis.php';
require_once __DIR__ . '/../models/Pembayaran.php';

class TransaksiController {

    private $transaksiModel;
    private $layananModel;
    private $pelangganModel;
    private $reservasiModel;
    private $terapisModel;
    private $pembayaranModel;

    public function __construct() {
        $this->transaksiModel  = new Transaksi();
        $this->layananModel    = new Layanan();
        $this->pelangganModel  = new Pelanggan();
        $this->reservasiModel  = new Reservasi();
        $this->terapisModel    = new Terapis();
        $this->pembayaranModel = new Pembayaran();
    }

    public function index() {
        $transaksiList = $this->transaksiModel->getAll();

        $success = $_SESSION['successMsg'] ?? null;
        $error   = $_SESSION['errorMsg']   ?? null;
        unset($_SESSION['successMsg'], $_SESSION['errorMsg']);

        $page = 'transaksi';
        require_once __DIR__ . '/../views/admin/templates/header.php';
        require_once __DIR__ . '/../views/admin/transaksi/index.php';
        require_once __DIR__ . '/../views/admin/templates/footer.php';
    }

    public function detail() {
        $id = intval($_GET['id'] ?? 0);
        $tx = $this->transaksiModel->getById($id);

        if (!$tx) {
            $_SESSION['errorMsg'] = "Transaksi tidak ditemukan!";
            header("Location: admin.php?page=transaksi");
            exit();
        }

        $page = 'transaksi';
        require_once __DIR__ . '/../views/admin/templates/header.php';
        require_once __DIR__ . '/../views/admin/transaksi/detail.php';
        require_once __DIR__ . '/../views/admin/templates/footer.php';
    }

    public function nota() {
        $id = intval($_GET['id'] ?? 0);
        $tx = $this->transaksiModel->getById($id);

        if (!$tx) {
            echo "Transaksi tidak ditemukan!";
            exit();
        }

        require_once __DIR__ . '/../views/admin/transaksi/nota.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->prosesCreateTransaksi();
            return;
        }

        $layananList   = $this->layananModel->getAll();
        $pelangganList = $this->pelangganModel->getAll();
        $terapisList   = $this->terapisModel->getActive();
        $metodePembayaranAktif = $this->pembayaranModel->getMetodePelunasanAktif();

        $error = $_SESSION['errorMsg'] ?? null;
        unset($_SESSION['errorMsg']);

        $page = 'transaksi';
        require_once __DIR__ . '/../views/admin/templates/header.php';
        require_once __DIR__ . '/../views/admin/transaksi/create.php';
        require_once __DIR__ . '/../views/admin/templates/footer.php';
    }

    public function laporan() {
        $this->pembayaranModel->sinkronkanPembayaranSelesai();
        $startDate = trim($_GET['start_date'] ?? date('Y-m-01'));
        $endDate   = trim($_GET['end_date']   ?? date('Y-m-d'));

        $error = $_SESSION['errorMsg'] ?? null;
        unset($_SESSION['errorMsg']);

        if (!$error && strtotime($startDate) > strtotime($endDate)) {
            $error = "Tanggal awal tidak boleh lebih besar dari tanggal akhir.";
        }

        if ($error) {
            $laporanList     = [];
            $totalPendapatan = 0;
            $totalTxCount    = 0;
        } else {
            $laporanList     = $this->transaksiModel->generateLaporan($startDate, $endDate);
            $totalPendapatan = $this->hitungTotalPendapatan($laporanList);
            $totalTxCount    = count($laporanList);
        }

        $page = 'laporan';
        require_once __DIR__ . '/../views/admin/templates/header.php';
        require_once __DIR__ . '/../views/admin/laporan/index.php';
        require_once __DIR__ . '/../views/admin/templates/footer.php';
    }

    public function cetakLaporan() {
        $this->pembayaranModel->sinkronkanPembayaranSelesai();
        $startDate = trim($_GET['start_date'] ?? date('Y-m-01'));
        $endDate   = trim($_GET['end_date']   ?? date('Y-m-d'));

        if (strtotime($startDate) > strtotime($endDate)) {
            $_SESSION['errorMsg'] = "Tanggal awal tidak boleh lebih besar dari tanggal akhir.";
            header("Location: admin.php?page=laporan&start_date=" . urlencode($startDate) . "&end_date=" . urlencode($endDate));
            exit();
        }

        $laporanList     = $this->transaksiModel->generateLaporan($startDate, $endDate);
        $totalPendapatan = $this->hitungTotalPendapatan($laporanList);
        $totalTxCount    = count($laporanList);

        require_once __DIR__ . '/../views/admin/laporan/cetak.php';
    }

    private function prosesCreateTransaksi() {
        $pelangganId      = $_POST['pelangganId']        ?? $_POST['pelanggan_id']        ?? '';
        $idTerapis        = intval($_POST['idTerapis']   ?? $_POST['id_terapis']          ?? 0);
        $metodePembayaran = trim($_POST['metodePembayaran'] ?? $_POST['metode_pembayaran'] ?? 'Tunai');
        $cartItemsJson    = $_POST['cart_items']         ?? '[]';
        $bayar            = floatval($_POST['nominal_bayar']    ?? 0.0);
        $kembalian        = floatval($_POST['nominal_kembalian'] ?? 0.0);

        if ($metodePembayaran !== 'Tunai') {
            $bayar = $kembalian = 0.0;
        }

        $serviceIds = json_decode($cartItemsJson, true);

        if (empty($serviceIds)) {
            $_SESSION['errorMsg'] = "Keranjang belanja kosong! Silakan pilih layanan SPA.";
            header("Location: admin.php?page=transaksi&action=create");
            exit();
        }

        if (empty($idTerapis)) {
            $_SESSION['errorMsg'] = "Pilih terapis terlebih dahulu untuk melakukan check-out POS!";
            header("Location: admin.php?page=transaksi&action=create");
            exit();
        }

        if (!$this->reservasiModel->isTherapistAvailable($idTerapis, date('Y-m-d H:i:s'))) {
            $_SESSION['errorMsg'] = "Terapis tersebut sedang sibuk melayani reservasi lain pada jam ini!";
            header("Location: admin.php?page=transaksi&action=create");
            exit();
        }

        if ($pelangganId === 'new') {
            $pelangganId = $this->daftarPelangganBaru();
        }

        if (empty($pelangganId)) {
            $_SESSION['errorMsg'] = "Pelanggan harus dipilih atau didaftarkan!";
            header("Location: admin.php?page=transaksi&action=create");
            exit();
        }

        [$services, $totalHarga] = $this->hitungServicesCart($serviceIds);

        if ($metodePembayaran === 'Tunai' && $bayar < $totalHarga) {
            $_SESSION['errorMsg'] = "Nominal pembayaran tunai kurang dari total tagihan (Total: Rp " . number_format($totalHarga, 0, ',', '.') . ")!";
            header("Location: admin.php?page=transaksi&action=create");
            exit();
        }

        $reservasiId = $this->reservasiModel->createReservation(
            $pelangganId, $idTerapis, date('Y-m-d H:i:s'),
            'Walk-in', 'Selesai', $totalHarga, $services
        );

        if (!$reservasiId) {
            $_SESSION['errorMsg'] = "Gagal membuat reservasi walk-in.";
            header("Location: admin.php?page=transaksi&action=create");
            exit();
        }

        $adminId = $_SESSION['adminId'] ?? 1;
        $this->pembayaranModel->create($reservasiId, $metodePembayaran, 'walk-in-payment', 'verified', $adminId);

        if ($metodePembayaran !== 'Tunai') {
            $bayar = $totalHarga;
        }

        $txId = $this->transaksiModel->create($reservasiId, $totalHarga, $services, $bayar, $kembalian);

        if ($txId) {
            $_SESSION['successMsg'] = "Transaksi kasir walk-in berhasil dicatat!";
            header("Location: admin.php?page=transaksi&action=detail&id=" . $txId);
        } else {
            $_SESSION['errorMsg'] = "Gagal memproses transaksi kasir.";
            header("Location: admin.php?page=transaksi&action=create");
        }
        exit();
    }

    private function daftarPelangganBaru() {
        $nama  = trim($_POST['new_nama']  ?? '');
        $noHp  = trim($_POST['new_no_hp'] ?? '');
        $email = trim($_POST['new_email'] ?? '');

        if (empty($nama) || empty($noHp)) {
            $_SESSION['errorMsg'] = "Nama dan Nomor Telepon pelanggan baru wajib diisi!";
            header("Location: admin.php?page=transaksi&action=create");
            exit();
        }

        if (!preg_match('/^[a-zA-Z\s]+$/', $nama)) {
            $_SESSION['errorMsg'] = "Nama pelanggan baru hanya boleh mengandung huruf dan spasi!";
            header("Location: admin.php?page=transaksi&action=create");
            exit();
        }

        if (!preg_match('/^[0-9]{10,13}$/', $noHp)) {
            $_SESSION['errorMsg'] = "Nomor telepon pelanggan baru harus berupa angka berjumlah 10-13 digit!";
            header("Location: admin.php?page=transaksi&action=create");
            exit();
        }

        $email = $email !== '' ? $email : null;

        return $this->pelangganModel->create($nama, $email, $noHp);
    }

    private function hitungServicesCart($serviceIds) {
        $idCounts = array_count_values($serviceIds);
        $services = [];
        $subtotal = 0;

        foreach ($idCounts as $sId => $qty) {
            $srv = $this->layananModel->getById($sId);
            if (!$srv) continue;

            $idLayanan    = $srv['idLayanan'] ?? $srv['id_layanan'];
            $itemSubtotal = (float)$srv['harga'] * $qty;
            $subtotal    += $itemSubtotal;
            $services[]   = ['idLayanan' => $idLayanan, 'qty' => $qty, 'subtotal' => $itemSubtotal];
        }

        return [$services, $subtotal];
    }

    private function hitungTotalPendapatan($laporanList) {
        return array_sum(array_map(
            fn($row) => (float)($row['totalPayment'] ?? $row['total_payment']),
            $laporanList
        ));
    }
}
?>
