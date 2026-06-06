<?php

require_once __DIR__ . '/../models/Pembayaran.php';
require_once __DIR__ . '/../models/Reservasi.php';
require_once __DIR__ . '/../models/Transaksi.php';
require_once __DIR__ . '/../models/Terapis.php';

class PembayaranController {

    private const STATUS_VERIFIED = 'verified';
    private const STATUS_REJECTED = 'rejected';
    private const STATUS_PENDING  = 'pending';

    private const STATUS_RES_DIKONFIRMASI      = 'Dikonfirmasi';
    private const STATUS_RES_DIBATALKAN        = 'Dibatalkan';
    private const STATUS_RES_MENUNGGU_BAYAR    = 'Menunggu Pembayaran';

    private $pembayaranModel;
    private $reservasiModel;
    private $transaksiModel;
    private $terapisModel;

    public function __construct() {
        $this->pembayaranModel = new Pembayaran();
        $this->reservasiModel  = new Reservasi();
        $this->transaksiModel  = new Transaksi();
        $this->terapisModel    = new Terapis();
    }

    public function index() {
        $this->pembayaranModel->sinkronkanPembayaranSelesai();
        $pembayaranList = $this->pembayaranModel->getAll();

        $success = $_SESSION['successMsg'] ?? null;
        $error   = $_SESSION['errorMsg']   ?? null;
        unset($_SESSION['successMsg'], $_SESSION['errorMsg']);

        $page = 'pembayaran';
        require_once __DIR__ . '/../views/admin/templates/header.php';
        require_once __DIR__ . '/../views/admin/pembayaran/index.php';
        require_once __DIR__ . '/../views/admin/templates/footer.php';
    }

    public function verifikasi() {
        $id  = intval($_GET['id'] ?? 0);
        $pay = $this->pembayaranModel->getById($id);

        if (!$pay) {
            $_SESSION['errorMsg'] = "Data pembayaran tidak ditemukan!";
            header("Location: admin.php?page=pembayaran");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->prosesVerifikasi($id, $pay);
            return;
        }

        $error = $_SESSION['errorMsg'] ?? null;
        unset($_SESSION['errorMsg']);

        $page = 'pembayaran';
        require_once __DIR__ . '/../views/admin/templates/header.php';
        require_once __DIR__ . '/../views/admin/pembayaran/verifikasi.php';
        require_once __DIR__ . '/../views/admin/templates/footer.php';
    }

    private function prosesVerifikasi($id, $pay) {
        $statusPembayaran = trim($_POST['statusPembayaran'] ?? $_POST['status_pembayaran'] ?? '');

        $statusValid = [self::STATUS_PENDING, self::STATUS_VERIFIED, self::STATUS_REJECTED];
        if (!in_array($statusPembayaran, $statusValid)) {
            $_SESSION['errorMsg'] = "Status pembayaran tidak valid!";
            header("Location: " . $this->redirectVerifikasiSource($pay, $id));
            exit();
        }

        if ($statusPembayaran === self::STATUS_VERIFIED
            && ($pay['status_reservation'] ?? '') === self::STATUS_RES_DIBATALKAN
        ) {
            $_SESSION['errorMsg'] = "Reservasi sudah dibatalkan otomatis karena melewati jam booking, sehingga pembayaran tidak bisa diverifikasi.";
            header("Location: " . $this->redirectVerifikasiSource($pay, $id));
            exit();
        }

        $adminId = $_SESSION['adminId'] ?? 1;
        $updated = $this->pembayaranModel->verifikasi($id, $statusPembayaran, $adminId);

        if (!$updated) {
            $_SESSION['errorMsg'] = "Gagal memproses verifikasi pembayaran.";
            header("Location: " . $this->redirectVerifikasiSource($pay, $id));
            exit();
        }

        $reservasiId = $pay['idReservasi'] ?? $pay['id_reservasi'];

        if ($statusPembayaran === self::STATUS_VERIFIED) {
            $this->prosesVerified($reservasiId, $pay);
        } elseif ($statusPembayaran === self::STATUS_REJECTED) {
            $this->reservasiModel->updateStatus($reservasiId, self::STATUS_RES_DIBATALKAN);
            $this->transaksiModel->deleteByReservasiId($reservasiId);
            $_SESSION['successMsg'] = "Status pembayaran diperbarui ke Rejected! Status reservasi terkait otomatis dibatalkan.";
        } else {
            $this->reservasiModel->updateStatus($reservasiId, self::STATUS_RES_MENUNGGU_BAYAR);
            $this->transaksiModel->deleteByReservasiId($reservasiId);
            $_SESSION['successMsg'] = "Status pembayaran dikembalikan ke Pending! Status reservasi dikembalikan ke Menunggu Pembayaran.";
        }

        header("Location: " . $this->redirectVerifikasiSource($pay, $id));
        exit();
    }

    private function redirectVerifikasiSource($pay, $paymentId) {
        $reservasiId = $pay['idReservasi'] ?? $pay['id_reservasi'] ?? 0;
        if (!empty($reservasiId)) {
            return "admin.php?page=reservasi&action=detail&id=" . (int) $reservasiId;
        }
        return "admin.php?page=pembayaran&action=verifikasi&id=" . (int) $paymentId;
    }

    private function prosesVerified($reservasiId, $pay) {
        $this->reservasiModel->updateStatus($reservasiId, self::STATUS_RES_DIKONFIRMASI);

        $assignedMsg = $this->autoAssignTerapis($reservasiId);
        $jenisPembayaran = $pay['jenis_pembayaran'] ?? 'DP 50%';

        if (!$this->transaksiModel->getByReservasiId($reservasiId)) {
            $this->buatTransaksiBaru($reservasiId, $pay);
        }

        if ($jenisPembayaran === 'Lunas 100%') {
            $adminId = $_SESSION['adminId'] ?? 1;
            $this->pembayaranModel->verifikasiLunasAwal($pay['id_payment'] ?? $pay['idPayment'], $adminId);
            $_SESSION['successMsg'] = "Pembayaran lunas 100% berhasil diverifikasi! Status pembayaran diperbarui ke Lunas dan reservasi dikonfirmasi." . $assignedMsg;
            return;
        }

        $_SESSION['successMsg'] = "DP 50% berhasil diverifikasi! Status reservasi diperbarui ke Dikonfirmasi," . $assignedMsg . " dan pembayaran DP masuk ke laporan.";
    }

    private function autoAssignTerapis($reservasiId) {
        $resData = $this->reservasiModel->getById($reservasiId);

        if (!$resData || !empty($resData['id_terapis'])) {
            return "";
        }

        $preferensi      = $resData['gender_terapis'] ?? $resData['genderTerapis'] ?? 'Bebas';
        $tanggalReservasi = $resData['reservation_date'] ?? $resData['reservationDate'];
        $semuaTerapis    = $this->terapisModel->getActive();

        $terapisCocok = $this->filterTerapisByGender($semuaTerapis, $preferensi);
        $terapisTerpilih = $this->cariTerapisYangTersedia($terapisCocok, $tanggalReservasi);

        if ($terapisTerpilih === null && $preferensi !== 'Bebas') {
            $terapisTerpilih = $this->cariTerapisYangTersedia($semuaTerapis, $tanggalReservasi);
        }

        if ($terapisTerpilih === null) {
            return " (Namun terapis sedang penuh, silakan tugaskan terapis manual nanti).";
        }

        $terapisId   = $terapisTerpilih['id_terapis']   ?? $terapisTerpilih['idTerapis'];
        $terapisNama = $terapisTerpilih['nama_terapis'] ?? $terapisTerpilih['namaTerapis'];
        $this->reservasiModel->assignTerapis($reservasiId, $terapisId);
        return " Terapis " . htmlspecialchars($terapisNama) . " otomatis ditugaskan sesuai preferensi.";
    }

    private function filterTerapisByGender($semuaTerapis, $preferensi) {
        if ($preferensi === 'Bebas') return $semuaTerapis;

        $isPria = in_array($preferensi, ['Laki-Laki', 'Laki-laki', 'Pria']);

        return array_filter($semuaTerapis, function($t) use ($isPria) {
            $gender = $t['jenis_kelamin'] ?? $t['jenisKelamin'] ?? 'Perempuan';
            $terapisIsPria = in_array($gender, ['Laki-Laki', 'Laki-laki', 'Pria']);
            return $isPria === $terapisIsPria;
        });
    }

    private function cariTerapisYangTersedia($daftarTerapis, $tanggalReservasi) {
        foreach ($daftarTerapis as $terapis) {
            $terapisId = $terapis['id_terapis'] ?? $terapis['idTerapis'];
            if ($this->reservasiModel->isTherapistAvailable($terapisId, $tanggalReservasi)) {
                return $terapis;
            }
        }
        return null;
    }

    private function buatTransaksiBaru($reservasiId, $pay) {
        $resDetails = $this->reservasiModel->getDetails($reservasiId);
        $services   = array_map(fn($d) => [
            'idLayanan' => $d['idLayanan'] ?? $d['id_layanan'],
            'qty'       => $d['qty'],
            'subtotal'  => $d['subtotal'],
        ], $resDetails);

        $totalPrice = $pay['totalPrice'] ?? $pay['total_price'];
        $nominalPayment = $pay['nominal_payment'] ?? $pay['nominalPayment'] ?? ($totalPrice * 0.5);
        $this->transaksiModel->create($reservasiId, $nominalPayment, $services, $nominalPayment, 0.0);
    }

    public function rekening() {
        $rekeningList = $this->pembayaranModel->getAllRekening();
        $success = $_SESSION['successMsg'] ?? null;
        $error   = $_SESSION['errorMsg']   ?? null;
        unset($_SESSION['successMsg'], $_SESSION['errorMsg']);

        $page = 'pembayaran';
        require_once __DIR__ . '/../views/admin/templates/header.php';
        require_once __DIR__ . '/../views/admin/pembayaran/rekening.php';
        require_once __DIR__ . '/../views/admin/templates/footer.php';
    }

    public function tambahRekening() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $namaBank      = trim($_POST['nama_bank'] ?? '');
            $nomorRekening = trim($_POST['nomor_rekening'] ?? '');
            $atasNama      = trim($_POST['atas_nama'] ?? '');

            if (empty($namaBank) || empty($nomorRekening) || empty($atasNama)) {
                $_SESSION['errorMsg'] = "Semua bidang formulir wajib diisi!";
                header("Location: admin.php?page=pembayaran&action=rekening");
                exit();
            }

            $saved = $this->pembayaranModel->createRekening($namaBank, $nomorRekening, $atasNama);
            if ($saved) {
                $_SESSION['successMsg'] = "Metode pembayaran baru berhasil ditambahkan!";
            } else {
                $_SESSION['errorMsg'] = "Gagal menyimpan metode pembayaran baru.";
            }
        }
        header("Location: admin.php?page=pembayaran&action=rekening");
        exit();
    }

    public function hapusRekening() {
        $id = intval($_GET['id'] ?? 0);
        if ($id > 0) {
            $deleted = $this->pembayaranModel->deleteRekening($id);
            if ($deleted) {
                $_SESSION['successMsg'] = "Metode pembayaran berhasil dihapus!";
            } else {
                $_SESSION['errorMsg'] = "Gagal menghapus metode pembayaran.";
            }
        }
        header("Location: admin.php?page=pembayaran&action=rekening");
        exit();
    }
}
?>
