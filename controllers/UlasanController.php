<?php

require_once __DIR__ . '/../models/Ulasan.php';

class UlasanController {
    private $ulasanModel;

    public function __construct() {
        $this->ulasanModel = new Ulasan();
    }

    public function index() {
        $ulasanList = $this->ulasanModel->getAll();

        $success = isset($_SESSION['successMsg']) ? $_SESSION['successMsg'] : null;
        $error   = isset($_SESSION['errorMsg'])   ? $_SESSION['errorMsg']   : null;
        unset($_SESSION['successMsg'], $_SESSION['errorMsg']);

        $page = 'ulasan';
        require_once __DIR__ . '/../views/admin/templates/header.php';
        require_once __DIR__ . '/../views/admin/ulasan/index.php';
        require_once __DIR__ . '/../views/admin/templates/footer.php';
    }

    public function delete() {
        $id      = isset($_GET['id']) ? intval($_GET['id']) : 0;
        $deleted = $this->ulasanModel->delete($id);

        if ($deleted) {
            $_SESSION['successMsg'] = "Ulasan pengguna berhasil dimoderasi / dihapus!";
        } else {
            $_SESSION['errorMsg'] = "Gagal menghapus ulasan.";
        }

        header("Location: admin.php?page=ulasan");
        exit();
    }

    public function balas() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id      = isset($_POST['id'])      ? intval($_POST['id'])      : 0;
            $balasan = isset($_POST['balasan']) ? trim($_POST['balasan']) : '';

            if ($id > 0) {
                $updated = $this->ulasanModel->balas($id, $balasan);
                if ($updated) {
                    $_SESSION['successMsg'] = "Balasan ulasan berhasil disimpan!";
                } else {
                    $_SESSION['errorMsg'] = "Gagal menyimpan balasan ulasan.";
                }
            } else {
                $_SESSION['errorMsg'] = "ID ulasan tidak valid.";
            }
        }

        header("Location: admin.php?page=ulasan");
        exit();
    }
}
?>
