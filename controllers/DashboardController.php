<?php

require_once __DIR__ . '/../models/Transaksi.php';
require_once __DIR__ . '/../models/Reservasi.php';
require_once __DIR__ . '/../models/Terapis.php';

class DashboardController {
    private $transaksiModel;
    private $reservasiModel;
    private $terapisModel;

    public function __construct() {
        $this->transaksiModel = new Transaksi();
        $this->reservasiModel = new Reservasi();
        $this->terapisModel   = new Terapis();
    }

    public function index() {
        $stats             = $this->transaksiModel->getStatistik();
        $allReservations   = $this->reservasiModel->getAll();
        $recentReservations = array_slice($allReservations, 0, 5);
        $activeTherapists  = $this->terapisModel->getActive();

        $page = 'dashboard';
        require_once __DIR__ . '/../views/admin/templates/header.php';
        require_once __DIR__ . '/../views/admin/dashboard/index.php';
        require_once __DIR__ . '/../views/admin/templates/footer.php';
    }
}
?>
