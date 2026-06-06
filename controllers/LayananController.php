<?php

require_once __DIR__ . '/../models/Layanan.php';

class LayananController {

    private const UPLOAD_DIR      = __DIR__ . '/../uploads/layanan/';
    private const MAX_UKURAN_FILE = 5 * 1024 * 1024; // 5 MB
    private const EKSTENSI_VALID  = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    private $layananModel;

    public function __construct() {
        $this->layananModel = new Layanan();
    }

    public function index() {
        $layananList = $this->layananModel->getAll();

        $success = $_SESSION['successMsg'] ?? null;
        $error   = $_SESSION['errorMsg']   ?? null;
        unset($_SESSION['successMsg'], $_SESSION['errorMsg']);

        $page = 'layanan';
        require_once __DIR__ . '/../views/admin/templates/header.php';
        require_once __DIR__ . '/../views/admin/layanan/index.php';
        require_once __DIR__ . '/../views/admin/templates/footer.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $namaLayanan = trim($_POST['namaLayanan'] ?? $_POST['nama_layanan'] ?? '');
            $kategori    = trim($_POST['kategori']    ?? '');
            $durasi      = intval($_POST['durasi']    ?? 0);
            $harga       = floatval($_POST['harga']   ?? 0.0);
            $deskripsi   = trim($_POST['deskripsi']   ?? '');

            if (empty($namaLayanan) || empty($kategori) || $durasi <= 0 || $harga <= 0) {
                $_SESSION['errorMsg'] = "Nama layanan, kategori, durasi (menit), dan harga harus diisi dengan benar!";
                header("Location: admin.php?page=layanan&action=create");
                exit();
            }

            $media = $this->prosesUploadGambar("admin.php?page=layanan&action=create");

            $saved = $this->layananModel->create($namaLayanan, $kategori, $durasi, $harga, $deskripsi, $media);
            if ($saved) {
                $_SESSION['successMsg'] = "Layanan SPA baru berhasil ditambahkan!";
                header("Location: admin.php?page=layanan");
            } else {
                $_SESSION['errorMsg'] = "Gagal menyimpan data layanan.";
                header("Location: admin.php?page=layanan&action=create");
            }
            exit();
        }

        $error = $_SESSION['errorMsg'] ?? null;
        unset($_SESSION['errorMsg']);

        $page = 'layanan';
        require_once __DIR__ . '/../views/admin/templates/header.php';
        require_once __DIR__ . '/../views/admin/layanan/create.php';
        require_once __DIR__ . '/../views/admin/templates/footer.php';
    }

    public function edit() {
        $id      = intval($_GET['id'] ?? 0);
        $layanan = $this->layananModel->getById($id);

        if (!$layanan) {
            $_SESSION['errorMsg'] = "Layanan tidak ditemukan!";
            header("Location: admin.php?page=layanan");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $namaLayanan = trim($_POST['namaLayanan'] ?? $_POST['nama_layanan'] ?? '');
            $kategori    = trim($_POST['kategori']    ?? '');
            $durasi      = intval($_POST['durasi']    ?? 0);
            $harga       = floatval($_POST['harga']   ?? 0.0);
            $deskripsi   = trim($_POST['deskripsi']   ?? '');

            if (empty($namaLayanan) || empty($kategori) || $durasi <= 0 || $harga <= 0) {
                $_SESSION['errorMsg'] = "Nama layanan, kategori, durasi (menit), dan harga harus diisi dengan benar!";
                header("Location: admin.php?page=layanan&action=edit&id=" . $id);
                exit();
            }

            $media = $this->prosesUploadGambar("admin.php?page=layanan&action=edit&id=" . $id) ?? $layanan['media'];

            $updated = $this->layananModel->update($id, $namaLayanan, $kategori, $durasi, $harga, $deskripsi, $media);
            if ($updated) {
                $_SESSION['successMsg'] = "Layanan SPA berhasil diperbarui!";
                header("Location: admin.php?page=layanan");
            } else {
                $_SESSION['errorMsg'] = "Gagal memperbarui data layanan.";
                header("Location: admin.php?page=layanan&action=edit&id=" . $id);
            }
            exit();
        }

        $error = $_SESSION['errorMsg'] ?? null;
        unset($_SESSION['errorMsg']);

        $page = 'layanan';
        require_once __DIR__ . '/../views/admin/templates/header.php';
        require_once __DIR__ . '/../views/admin/layanan/edit.php';
        require_once __DIR__ . '/../views/admin/templates/footer.php';
    }

    public function delete() {
        $id      = intval($_GET['id'] ?? 0);
        $deleted = $this->layananModel->delete($id);

        if ($deleted) {
            $_SESSION['successMsg'] = "Layanan SPA berhasil dihapus!";
        } else {
            $_SESSION['errorMsg'] = "Gagal menghapus data layanan.";
        }

        header("Location: admin.php?page=layanan");
        exit();
    }

    private function prosesUploadGambar($redirectUrl) {
        if (!isset($_FILES['media']) || $_FILES['media']['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($_FILES['media']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['errorMsg'] = "Terjadi kesalahan saat mengunggah berkas gambar.";
            header("Location: " . $redirectUrl);
            exit();
        }

        $file      = $_FILES['media'];
        $ekstensi  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $baseName  = preg_replace("/[^a-zA-Z0-9_\-]/", "", pathinfo($file['name'], PATHINFO_FILENAME));
        $namaFile  = time() . '_' . $baseName . '.' . $ekstensi;

        if (!in_array($ekstensi, self::EKSTENSI_VALID)) {
            $_SESSION['errorMsg'] = "Format berkas tidak diizinkan! (Gunakan JPG, JPEG, PNG, WEBP, atau GIF)";
            header("Location: " . $redirectUrl);
            exit();
        }

        if ($file['size'] > self::MAX_UKURAN_FILE) {
            $_SESSION['errorMsg'] = "Ukuran berkas gambar maksimal 5 MB!";
            header("Location: " . $redirectUrl);
            exit();
        }

        if (!is_dir(self::UPLOAD_DIR)) {
            mkdir(self::UPLOAD_DIR, 0755, true);
        }

        if (!move_uploaded_file($file['tmp_name'], self::UPLOAD_DIR . $namaFile)) {
            $_SESSION['errorMsg'] = "Gagal menyimpan berkas gambar ke server.";
            header("Location: " . $redirectUrl);
            exit();
        }

        return $namaFile;
    }
}
?>
