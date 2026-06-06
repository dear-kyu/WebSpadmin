<?php

require_once __DIR__ . '/../models/Reservasi.php';
require_once __DIR__ . '/../models/Terapis.php';
require_once __DIR__ . '/../models/Pembayaran.php';
require_once __DIR__ . '/../models/Transaksi.php';

class ReservasiController {
    private $reservasiModel;
    private $terapisModel;
    private $pembayaranModel;
    private $transaksiModel;

    public function __construct() {
        $this->reservasiModel  = new Reservasi();
        $this->terapisModel    = new Terapis();
        $this->pembayaranModel = new Pembayaran();
        $this->transaksiModel  = new Transaksi();
    }

    public function index() {
        $statusFilter = isset($_GET['status']) ? trim($_GET['status']) : 'Semua';
        
        $mappedStatus = null;
        if ($statusFilter === 'Menunggu') {
            $mappedStatus = ['Menunggu', 'Menunggu Pembayaran', 'Menunggu Validasi'];
        } elseif ($statusFilter === 'Diterima') {
            $mappedStatus = ['Diterima', 'Dikonfirmasi'];
        } elseif ($statusFilter === 'Ditolak') {
            $mappedStatus = ['Ditolak'];
        } elseif ($statusFilter === 'Dibatalkan') {
            $mappedStatus = ['Dibatalkan', 'Hangus'];
        } elseif ($statusFilter === 'Selesai') {
            $mappedStatus = ['Selesai'];
        }

        $reservasiList = $this->reservasiModel->getAll($mappedStatus);
        $counts = $this->reservasiModel->getCounts();
        $ruanganStatus = $this->reservasiModel->getRuanganStatus();

        $success = isset($_SESSION['successMsg']) ? $_SESSION['successMsg'] : null;
        $error   = isset($_SESSION['errorMsg'])   ? $_SESSION['errorMsg']   : null;
        unset($_SESSION['successMsg'], $_SESSION['errorMsg']);

        $page = 'reservasi';
        require_once __DIR__ . '/../views/admin/templates/header.php';
        require_once __DIR__ . '/../views/admin/reservasi/index.php';
        require_once __DIR__ . '/../views/admin/templates/footer.php';
    }

    public function detail() {
        $id  = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $res = $this->reservasiModel->getById($id);

        if (!$res) {
            $_SESSION['errorMsg'] = "Reservasi tidak ditemukan!";
            header("Location: admin.php?page=reservasi");
            exit();
        }

        $pembayaran  = $this->pembayaranModel->getByReservasiId($id);
        $metodePembayaranAktif = $this->pembayaranModel->getMetodePelunasanAktif();
        $terapisAktif = $this->terapisModel->getActive();

        $success = isset($_SESSION['successMsg']) ? $_SESSION['successMsg'] : null;
        $error   = isset($_SESSION['errorMsg'])   ? $_SESSION['errorMsg']   : null;
        unset($_SESSION['successMsg'], $_SESSION['errorMsg']);

        $page = 'reservasi';
        require_once __DIR__ . '/../views/admin/templates/header.php';
        require_once __DIR__ . '/../views/admin/reservasi/detail.php';
        require_once __DIR__ . '/../views/admin/templates/footer.php';
    }

    public function assignTerapis() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id        = isset($_POST['idReservasi']) ? intval($_POST['idReservasi']) : (isset($_POST['reservasi_id']) ? intval($_POST['reservasi_id']) : 0);
            $idDetail  = isset($_POST['idDetail'])    ? intval($_POST['idDetail'])    : 0;
            $terapisId = isset($_POST['idTerapis'])   ? intval($_POST['idTerapis'])   : (isset($_POST['terapis_id']) ? intval($_POST['terapis_id']) : 0);
            $status    = isset($_POST['status'])      ? trim($_POST['status'])        : '';

            $res = $this->reservasiModel->getById($id);
            if (!$res) {
                $_SESSION['errorMsg'] = "Reservasi tidak ditemukan!";
                header("Location: admin.php?page=reservasi");
                exit();
            }

            if (!empty($terapisId)) {
                $reservationDate = $res['reservationDate'] ?? $res['reservation_date'];
                $available = $this->reservasiModel->isTherapistAvailable($terapisId, $reservationDate, $id);
                if (!$available) {
                    $_SESSION['errorMsg'] = "Terapis tersebut sudah memiliki jadwal reservasi aktif di waktu yang berdekatan (kurang dari 1 jam)!";
                    header("Location: admin.php?page=reservasi&action=detail&id=" . $id);
                    exit();
                }
            }

            if (isset($_POST['idDetail']) || isset($_POST['terapis_id']) || isset($_POST['idTerapis'])) {
                if ($idDetail > 0) {
                    $this->reservasiModel->assignTerapisToDetail($idDetail, $terapisId);
                } else {
                    $this->reservasiModel->assignTerapis($id, $terapisId);
                }
            }

            if (!empty($status)) {
                if ($status === 'Diterima') {
                    $pay = $this->pembayaranModel->getByReservasiId($id);
                    if ((empty($pay) || empty($pay['payment_proof'])) && $res['status_reservation'] !== 'Diterima') {
                        $_SESSION['errorMsg'] = "Gagal memperbarui status! Bukti pembayaran belum diunggah oleh pelanggan.";
                        header("Location: admin.php?page=reservasi&action=detail&id=" . $id);
                        exit();
                    }
                }
                $this->reservasiModel->updateStatus($id, $status);

                $pay = $this->pembayaranModel->getByReservasiId($id);
                if ($pay) {
                    $method = $pay['paymentMethod'] ?? $pay['payment_method'];
                    $paymentStatus = $pay['status_payment'] ?? $pay['statusPayment'] ?? '';
                    $jenisPembayaran = $pay['jenis_pembayaran'] ?? $pay['jenisPembayaran'] ?? 'DP 50%';
                    $adminId   = $_SESSION['adminId'] ?? 1;
                    $idPayment = $pay['idPayment'] ?? $pay['id_payment'];

                    if ($status === 'Dibatalkan') {
                        $pembayaranSudahDiverifikasi = in_array($paymentStatus, ['verified', 'Diterima', 'Lunas'], true);
                        $statusHangus = $jenisPembayaran === 'Lunas 100%' ? 'Pembayaran Hangus' : 'DP Hangus';
                        if ($pembayaranSudahDiverifikasi && strpos($method, 'Hangus') === false) {
                            $method .= ' (' . $statusHangus . ')';
                        }
                        $statusPayment = $pembayaranSudahDiverifikasi ? $statusHangus : 'rejected';
                        $this->pembayaranModel->verifikasi($idPayment, $statusPayment, $adminId, $method);
                    } elseif ($status === 'Selesai' && in_array($paymentStatus, ['verified', 'Diterima', 'DP Hangus', 'Pembayaran Hangus'], true)) {
                        $method = trim(preg_replace('/\s*\((DP Hangus|Pembayaran Hangus)\)\s*/i', '', $method));
                        $this->pembayaranModel->verifikasi($idPayment, 'Lunas', $adminId, $method);
                        $this->pembayaranModel->updateNominalPayment($idPayment, $res['total_price']);
                        $this->transaksiModel->updateTotalByReservasiId($id, $res['total_price']);
                    } elseif (in_array($paymentStatus, ['DP Hangus', 'Pembayaran Hangus'], true)) {
                        $method = trim(preg_replace('/\s*\((DP Hangus|Pembayaran Hangus)\)\s*/i', '', $method));
                        $statusPayment = $jenisPembayaran === 'Lunas 100%' ? 'Lunas' : 'verified';
                        $this->pembayaranModel->verifikasi($idPayment, $statusPayment, $adminId, $method);
                    }
                }

            }

            $_SESSION['successMsg'] = "Data reservasi & penugasan terapis berhasil diperbarui!";
            header("Location: admin.php?page=reservasi&action=detail&id=" . $id);
            exit();
        }

        header("Location: admin.php?page=reservasi");
        exit();
    }
    public function pelunasan() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: admin.php?page=reservasi");
            exit();
        }

        $id    = intval($_POST['reservasi_id'] ?? 0);
        $metode = trim($_POST['metode_pelunasan'] ?? '');
        $uangBayar = floatval($_POST['pelunasan_uang_bayar'] ?? 0);

        if ($id <= 0 || empty($metode)) {
            $_SESSION['errorMsg'] = "Data pelunasan tidak valid!";
            header("Location: admin.php?page=reservasi&action=detail&id=" . $id);
            exit();
        }

        $validMetode = array_merge(['Cash', 'Transfer Bank', 'E-Wallet'], $this->pembayaranModel->getMetodePelunasanAktif());
        if (!in_array($metode, $validMetode, true)) {
            $_SESSION['errorMsg'] = "Metode pembayaran tidak valid!";
            header("Location: admin.php?page=reservasi&action=detail&id=" . $id);
            exit();
        }

        $res = $this->reservasiModel->getById($id);
        if (!$res) {
            $_SESSION['errorMsg'] = "Reservasi tidak ditemukan!";
            header("Location: admin.php?page=reservasi");
            exit();
        }

        // Hanya reservasi online yang memiliki alur DP.
        if (($res['reservation_type'] ?? '') !== 'online') {
            $_SESSION['errorMsg'] = "Fitur pelunasan hanya untuk reservasi online.";
            header("Location: admin.php?page=reservasi&action=detail&id=" . $id);
            exit();
        }

        $sisaPembayaran = max(0, $res['total_price'] - ($res['total_price'] * 0.5));
        $kembalian = 0;
        if ($metode === 'Cash') {
            if ($uangBayar < $sisaPembayaran) {
                $_SESSION['errorMsg'] = "Uang bayar kurang dari sisa pembayaran (Minimal: Rp " . number_format($sisaPembayaran, 0, ',', '.') . ")!";
                header("Location: admin.php?page=reservasi&action=detail&id=" . $id);
                exit();
            }
            $kembalian = $uangBayar - $sisaPembayaran;
        } else {
            $uangBayar = $sisaPembayaran;
        }

        $pay = $this->pembayaranModel->getByReservasiId($id);
        if (!$pay) {
            $_SESSION['errorMsg'] = "Data pembayaran DP tidak ditemukan!";
            header("Location: admin.php?page=reservasi&action=detail&id=" . $id);
            exit();
        }

        $spNow = $pay['status_payment'] ?? '';
        if ($spNow === 'Lunas') {
            $_SESSION['errorMsg'] = "Reservasi ini sudah berstatus Lunas.";
            header("Location: admin.php?page=reservasi&action=detail&id=" . $id);
            exit();
        }

        if (!in_array($spNow, ['verified', 'Diterima'], true)) {
            $_SESSION['errorMsg'] = "Pelunasan hanya bisa dilakukan setelah DP diverifikasi.";
            header("Location: admin.php?page=reservasi&action=detail&id=" . $id);
            exit();
        }

        $idPayment = $pay['idPayment'] ?? $pay['id_payment'];
        $adminId   = $_SESSION['adminId'] ?? 1;

        $ok = $this->pembayaranModel->prosesLunas($idPayment, $metode, $uangBayar, $kembalian, $adminId);
        if ($ok) {
            $this->pembayaranModel->updateNominalPayment($idPayment, $res['total_price']);
            $this->transaksiModel->updateTotalByReservasiId($id, $res['total_price']);
            $_SESSION['successMsg'] = "Pelunasan berhasil dikonfirmasi dengan metode " . htmlspecialchars($metode) . ". Status pembayaran sekarang Lunas.";
        } else {
            $_SESSION['errorMsg'] = "Gagal menyimpan data pelunasan. Silakan coba lagi.";
        }

        header("Location: admin.php?page=reservasi&action=detail&id=" . $id);
        exit();
    }
}
?>
