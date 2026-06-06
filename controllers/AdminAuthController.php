<?php


require_once __DIR__ . '/../models/Admin.php';

class AdminAuthController {
    private $adminModel;

    public function __construct() {
        $this->adminModel = new Admin();
    }

    public function showLoginForm() {
        $error = isset($_SESSION['loginError']) ? $_SESSION['loginError'] : null;
        unset($_SESSION['loginError']); // Hapus setelah dibaca
        
        // Load view login
        require_once __DIR__ . '/../views/admin/auth/login.php';
    }

    public function processLogin() {
        $username = isset($_POST['username']) ? trim($_POST['username']) : '';
        $password = isset($_POST['password']) ? trim($_POST['password']) : '';

        // Validasi input kosong
        if (empty($username) || empty($password)) {
            $_SESSION['loginError'] = "Username/Email dan Password tidak boleh kosong.";
            header("Location: admin.php?page=login");
            exit();
        }

        // Standardize login identifier
        $email = $username;
        if ($email === 'admin') {
            $email = 'admin@spadmin.com';
        }

        // Jalankan login
        $admin = $this->adminModel->login($email, $password);

        if ($admin) {
            // Set session login admin
            $_SESSION['adminLoggedIn'] = true;
            $_SESSION['adminId'] = $admin['idUser'];
            $_SESSION['adminUsername'] = $admin['email'];
            $_SESSION['adminNama'] = $admin['nama'];
            
            // Alihkan ke dashboard
            header("Location: admin.php?page=dashboard");
            exit();
        } else {
            $_SESSION['loginError'] = "Username/Email atau Password salah.";
            header("Location: admin.php?page=login");
            exit();
        }
    }

    public function logout() {
        $_SESSION = array();
        session_destroy();
        header("Location: admin.php?page=login");
        exit();
    }
}
?>