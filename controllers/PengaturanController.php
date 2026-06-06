<?php

require_once __DIR__ . '/../models/Setting.php';
require_once __DIR__ . '/../models/Pembayaran.php';

class PengaturanController {
    private $settingModel;
    private $pembayaranModel;

    public function __construct() {
        $this->settingModel = new Setting();
        $this->pembayaranModel = new Pembayaran();
    }

    public function index() {
        $settings = $this->settingModel->getAll();
        $rekeningList = $this->pembayaranModel->getAllRekening();
        
        $categoriesQuery = "SELECT DISTINCT kategori FROM layanan ORDER BY kategori ASC";
        $db = (new Database())->getConnection();
        $stmt = $db->query($categoriesQuery);
        $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $success = $_SESSION['successMsg'] ?? null;
        $error   = $_SESSION['errorMsg']   ?? null;
        unset($_SESSION['successMsg'], $_SESSION['errorMsg']);

        $page = 'pengaturan';
        require_once __DIR__ . '/../views/admin/templates/header.php';
        require_once __DIR__ . '/../views/admin/pengaturan/index.php';
        require_once __DIR__ . '/../views/admin/templates/footer.php';
    }

    public function tambahRekening() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $namaBank      = trim($_POST['nama_bank'] ?? '');
            $nomorRekening = trim($_POST['nomor_rekening'] ?? '');
            $atasNama      = trim($_POST['atas_nama'] ?? '');

            if (empty($namaBank) || empty($nomorRekening) || empty($atasNama)) {
                $_SESSION['errorMsg'] = "Semua bidang metode transfer wajib diisi!";
                header('Location: admin.php?page=pengaturan#metode-transfer');
                exit();
            }

            if ($this->pembayaranModel->createRekening($namaBank, $nomorRekening, $atasNama)) {
                $_SESSION['successMsg'] = "Metode transfer baru berhasil ditambahkan!";
            } else {
                $_SESSION['errorMsg'] = "Gagal menyimpan metode transfer baru.";
            }
        }

        header('Location: admin.php?page=pengaturan#metode-transfer');
        exit();
    }

    public function hapusRekening() {
        $id = intval($_GET['id'] ?? 0);
        if ($id > 0 && $this->pembayaranModel->deleteRekening($id)) {
            $_SESSION['successMsg'] = "Metode transfer berhasil dihapus!";
        } else {
            $_SESSION['errorMsg'] = "Gagal menghapus metode transfer.";
        }

        header('Location: admin.php?page=pengaturan#metode-transfer');
        exit();
    }

    public function updateTampilan() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $eyebrow = trim($_POST['featured_section_eyebrow'] ?? '');
            $title = trim($_POST['featured_section_title'] ?? '');
            $subtitle = trim($_POST['featured_section_subtitle'] ?? '');
            $category = trim($_POST['featured_section_category'] ?? '');

            if (empty($eyebrow) || empty($title) || empty($category)) {
                $_SESSION['errorMsg'] = "Semua bidang pengaturan tampilan wajib diisi!";
                header('Location: admin.php?page=pengaturan');
                exit();
            }

            $this->settingModel->save('featured_section_eyebrow', $eyebrow);
            $this->settingModel->save('featured_section_title', $title);
            $this->settingModel->save('featured_section_subtitle', $subtitle);
            $this->settingModel->save('featured_section_category', $category);

            $_SESSION['successMsg'] = "Pengaturan tampilan halaman utama berhasil diperbarui!";
            header('Location: admin.php?page=pengaturan');
            exit();
        }
    }

    public function updateSesi() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $interval = trim($_POST['interval_reservasi'] ?? '');
            $pMulai = trim($_POST['sesi_pagi_mulai'] ?? '');
            $pSelesai = trim($_POST['sesi_pagi_selesai'] ?? '');
            $sMulai = trim($_POST['sesi_siang_mulai'] ?? '');
            $sSelesai = trim($_POST['sesi_siang_selesai'] ?? '');
            $soMulai = trim($_POST['sesi_sore_mulai'] ?? '');
            $soSelesai = trim($_POST['sesi_sore_selesai'] ?? '');

            if (empty($interval) || 
                empty($pMulai) || empty($pSelesai) || 
                empty($sMulai) || empty($sSelesai) || 
                empty($soMulai) || empty($soSelesai)) {
                $_SESSION['errorMsg'] = "Semua parameter jam sesi wajib diisi!";
                header('Location: admin.php?page=pengaturan');
                exit();
            }

            $this->settingModel->save('interval_reservasi', $interval);
            $this->settingModel->save('sesi_pagi_mulai', $pMulai);
            $this->settingModel->save('sesi_pagi_selesai', $pSelesai);
            $this->settingModel->save('sesi_siang_mulai', $sMulai);
            $this->settingModel->save('sesi_siang_selesai', $sSelesai);
            $this->settingModel->save('sesi_sore_mulai', $soMulai);
            $this->settingModel->save('sesi_sore_selesai', $soSelesai);

            $_SESSION['successMsg'] = "Pengaturan parameter jam sesi reservasi berhasil diperbarui!";
            header('Location: admin.php?page=pengaturan');
            exit();
        }
    }
}
?>
