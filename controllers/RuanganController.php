<?php

require_once __DIR__ . '/../models/Ruangan.php';

class RuanganController {

    private $ruanganModel;

    public function __construct() {
        $this->ruanganModel = new Ruangan();
    }

    public function index() {
        $ruanganList = $this->ruanganModel->getAll();

        $success = isset($_SESSION['successMsg']) ? $_SESSION['successMsg'] : null;
        $error   = isset($_SESSION['errorMsg'])   ? $_SESSION['errorMsg']   : null;
        unset($_SESSION['successMsg'], $_SESSION['errorMsg']);

        $page = 'ruangan';
        require_once __DIR__ . '/../views/admin/templates/header.php';
        require_once __DIR__ . '/../views/admin/ruangan/index.php';
        require_once __DIR__ . '/../views/admin/templates/footer.php';
    }

    public function edit() {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $room = $this->ruanganModel->getById($id);

        if (!$room) {
            $_SESSION['errorMsg'] = "Data ruangan tidak ditemukan!";
            header("Location: admin.php?page=ruangan");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $namaRuangan = isset($_POST['namaRuangan']) ? trim($_POST['namaRuangan']) : '';
            $status      = isset($_POST['status'])      ? trim($_POST['status'])      : 'aktif';

            if (empty($namaRuangan)) {
                $_SESSION['errorMsg'] = "Nama ruangan wajib diisi!";
                header("Location: admin.php?page=ruangan&action=edit&id=" . $id);
                exit();
            }

            if ($status === 'tidak aktif' && $this->ruanganModel->hasActiveBooking($id)) {
                $_SESSION['errorMsg'] = "Ruangan tidak dapat dinonaktifkan karena sedang memiliki reservasi aktif!";
                header("Location: admin.php?page=ruangan&action=edit&id=" . $id);
                exit();
            }

            $updated = $this->ruanganModel->update($id, $namaRuangan, $status);
            if ($updated) {
                $_SESSION['successMsg'] = "Data ruangan berhasil diperbarui!";
                header("Location: admin.php?page=ruangan");
            } else {
                $_SESSION['errorMsg'] = "Gagal memperbarui data ruangan.";
                header("Location: admin.php?page=ruangan&action=edit&id=" . $id);
            }
            exit();
        }

        $error = $_SESSION['errorMsg'] ?? null;
        unset($_SESSION['errorMsg']);

        $page = 'ruangan';
        require_once __DIR__ . '/../views/admin/templates/header.php';
        require_once __DIR__ . '/../views/admin/ruangan/edit.php';
        require_once __DIR__ . '/../views/admin/templates/footer.php';
    }

    public function toggleStatus() {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $room = $this->ruanganModel->getById($id);
        if ($room && $room['status'] === 'aktif' && $this->ruanganModel->hasActiveBooking($id)) {
            $_SESSION['errorMsg'] = "Ruangan tidak dapat dinonaktifkan karena sedang memiliki reservasi aktif!";
            header("Location: admin.php?page=ruangan");
            exit();
        }

        $toggled = $this->ruanganModel->toggleStatus($id);

        if ($toggled) {
            $_SESSION['successMsg'] = "Status ketersediaan ruangan berhasil diubah!";
        } else {
            $_SESSION['errorMsg'] = "Gagal mengubah status ruangan.";
        }

        header("Location: admin.php?page=ruangan");
        exit();
    }
}
?>
