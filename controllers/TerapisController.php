<?php

require_once __DIR__ . '/../models/Terapis.php';

class TerapisController {

    private $terapisModel;

    public function __construct() {
        $this->terapisModel = new Terapis();
    }

    public function index() {
        $terapisList = $this->terapisModel->getAll();

        $success = $_SESSION['successMsg'] ?? null;
        $error   = $_SESSION['errorMsg']   ?? null;
        unset($_SESSION['successMsg'], $_SESSION['errorMsg']);

        $page = 'terapis';
        require_once __DIR__ . '/../views/admin/templates/header.php';
        require_once __DIR__ . '/../views/admin/terapis/index.php';
        require_once __DIR__ . '/../views/admin/templates/footer.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $namaTerapis  = trim($_POST['namaTerapis']   ?? '');
            $spesialisasi = isset($_POST['spesialisasi']) ? (is_array($_POST['spesialisasi']) ? implode(', ', $_POST['spesialisasi']) : trim($_POST['spesialisasi'])) : '';
            $noTelp       = trim($_POST['noTelp']        ?? '');
            $status       = trim($_POST['status']        ?? 'Aktif');
            $jenisKelamin = trim($_POST['jenis_kelamin'] ?? 'Perempuan');

            $redirectCreate = "admin.php?page=terapis&action=create";

            if (empty($namaTerapis) || empty($spesialisasi) || empty($noTelp)) {
                $_SESSION['errorMsg'] = "Semua field wajib diisi!";
                header("Location: " . $redirectCreate);
                exit();
            }

            $this->validasiInputTerapis($namaTerapis, $noTelp, $redirectCreate);

            $saved = $this->terapisModel->create($namaTerapis, $spesialisasi, $noTelp, $status, $jenisKelamin);
            if ($saved) {
                $_SESSION['successMsg'] = "Data terapis berhasil ditambahkan!";
                header("Location: admin.php?page=terapis");
            } else {
                $_SESSION['errorMsg'] = "Gagal menyimpan data terapis.";
                header("Location: " . $redirectCreate);
            }
            exit();
        }

        $error = $_SESSION['errorMsg'] ?? null;
        unset($_SESSION['errorMsg']);

        require_once __DIR__ . '/../models/Layanan.php';
        $layananModel = new Layanan();
        $allLayanan = $layananModel->getAll();
        $layananOptions = [];
        foreach ($allLayanan as $l) {
            $name = trim($l['nama_layanan'] ?? $l['namaLayanan'] ?? '');
            if (!empty($name) && !in_array($name, $layananOptions)) {
                $layananOptions[] = $name;
            }
        }
        sort($layananOptions);

        $page = 'terapis';
        require_once __DIR__ . '/../views/admin/templates/header.php';
        require_once __DIR__ . '/../views/admin/terapis/create.php';
        require_once __DIR__ . '/../views/admin/templates/footer.php';
    }

    public function edit() {
        $id      = intval($_GET['id'] ?? 0);
        $terapis = $this->terapisModel->getById($id);

        if (!$terapis) {
            $_SESSION['errorMsg'] = "Data terapis tidak ditemukan!";
            header("Location: admin.php?page=terapis");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $namaTerapis  = trim($_POST['namaTerapis']   ?? '');
            $spesialisasi = isset($_POST['spesialisasi']) ? (is_array($_POST['spesialisasi']) ? implode(', ', $_POST['spesialisasi']) : trim($_POST['spesialisasi'])) : '';
            $noTelp       = trim($_POST['noTelp']        ?? '');
            $status       = trim($_POST['status']        ?? 'Aktif');
            $jenisKelamin = trim($_POST['jenis_kelamin'] ?? 'Perempuan');

            $redirectEdit = "admin.php?page=terapis&action=edit&id=" . $id;

            if (empty($namaTerapis) || empty($spesialisasi) || empty($noTelp)) {
                $_SESSION['errorMsg'] = "Semua field wajib diisi!";
                header("Location: " . $redirectEdit);
                exit();
            }

            $this->validasiInputTerapis($namaTerapis, $noTelp, $redirectEdit);

            $updated = $this->terapisModel->update($id, $namaTerapis, $spesialisasi, $noTelp, $status, $jenisKelamin);
            if ($updated) {
                $_SESSION['successMsg'] = "Data terapis berhasil diperbarui!";
                header("Location: admin.php?page=terapis");
            } else {
                $_SESSION['errorMsg'] = "Gagal memperbarui data terapis.";
                header("Location: " . $redirectEdit);
            }
            exit();
        }

        $error = $_SESSION['errorMsg'] ?? null;
        unset($_SESSION['errorMsg']);

        require_once __DIR__ . '/../models/Layanan.php';
        $layananModel = new Layanan();
        $allLayanan = $layananModel->getAll();
        $layananOptions = [];
        foreach ($allLayanan as $l) {
            $name = trim($l['nama_layanan'] ?? $l['namaLayanan'] ?? '');
            if (!empty($name) && !in_array($name, $layananOptions)) {
                $layananOptions[] = $name;
            }
        }
        sort($layananOptions);

        $page = 'terapis';
        require_once __DIR__ . '/../views/admin/templates/header.php';
        require_once __DIR__ . '/../views/admin/terapis/edit.php';
        require_once __DIR__ . '/../views/admin/templates/footer.php';
    }

    public function delete() {
        $id      = intval($_GET['id'] ?? 0);
        $deleted = $this->terapisModel->delete($id);

        if ($deleted) {
            $_SESSION['successMsg'] = "Data terapis berhasil dihapus!";
        } else {
            $_SESSION['errorMsg'] = "Gagal menghapus data terapis.";
        }

        header("Location: admin.php?page=terapis");
        exit();
    }

    private function validasiInputTerapis($namaTerapis, $noTelp, $redirectUrl) {
        if (!preg_match('/^[a-zA-Z\s]+$/', $namaTerapis)) {
            $_SESSION['errorMsg'] = "Nama Terapis hanya boleh mengandung huruf dan spasi!";
            header("Location: " . $redirectUrl);
            exit();
        }

        if (!preg_match('/^[0-9]{10,13}$/', $noTelp)) {
            $_SESSION['errorMsg'] = "Nomor telepon harus berupa angka berjumlah 10-13 digit!";
            header("Location: " . $redirectUrl);
            exit();
        }
    }
}
?>
