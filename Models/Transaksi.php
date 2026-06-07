<?php

require_once __DIR__ . '/BaseModel.php';

class Transaksi extends BaseModel {

    public function getAll() {
        $query = "SELECT t.*, r.reservation_date, r.reservation_type, r.id_user,
                         (SELECT MIN(rd3.id_terapis) FROM reservasi_detail rd3 WHERE rd3.id_reservasi = r.id_reservasi) AS id_terapis,
                         p.nama AS namaPelanggan, p.no_telepon AS noHpPelanggan,
                         (SELECT GROUP_CONCAT(DISTINCT t2.nama_terapis SEPARATOR ', ') FROM reservasi_detail rd2 JOIN terapis t2 ON rd2.id_terapis = t2.id_terapis WHERE rd2.id_reservasi = r.id_reservasi) AS namaTerapis,
                         (SELECT pay.payment_method FROM payment pay WHERE pay.id_reservasi = r.id_reservasi ORDER BY pay.id_payment DESC LIMIT 1) AS payment_method,
                         (SELECT pay.status_payment FROM payment pay WHERE pay.id_reservasi = r.id_reservasi ORDER BY pay.id_payment DESC LIMIT 1) AS status_payment,
                         (SELECT uAdmin.nama FROM payment pay JOIN users uAdmin ON pay.verified_by = uAdmin.id_user WHERE pay.id_reservasi = r.id_reservasi ORDER BY pay.id_payment DESC LIMIT 1) AS namaAdmin,
                         GROUP_CONCAT(DISTINCT l.nama_layanan SEPARATOR ', ') AS layananNames
                   FROM transaksi t
                   LEFT JOIN reservasi r ON t.id_reservasi = r.id_reservasi
                   LEFT JOIN users p ON r.id_user = p.id_user
                   LEFT JOIN detail_transaksi dt ON t.id_transaksi = dt.id_transaksi
                   LEFT JOIN layanan l ON dt.id_layanan = l.id_layanan
                   GROUP BY t.id_transaksi
                   ORDER BY t.id_transaksi DESC";

        return $this->fetchAll($query);
    }

    public function getById($idTransaksi) {
        $query = "SELECT t.*, r.reservation_date, r.reservation_type, r.id_user,
                         (SELECT MIN(rd3.id_terapis) FROM reservasi_detail rd3 WHERE rd3.id_reservasi = r.id_reservasi) AS id_terapis,
                         p.nama AS namaPelanggan, p.no_telepon AS noHpPelanggan, p.email AS emailPelanggan, p.rating_pelanggan,
                         (SELECT GROUP_CONCAT(DISTINCT t2.nama_terapis SEPARATOR ', ') FROM reservasi_detail rd2 JOIN terapis t2 ON rd2.id_terapis = t2.id_terapis WHERE rd2.id_reservasi = r.id_reservasi) AS namaTerapis,
                         (SELECT pay.payment_method FROM payment pay WHERE pay.id_reservasi = r.id_reservasi ORDER BY pay.id_payment DESC LIMIT 1) AS payment_method,
                         (SELECT pay.status_payment FROM payment pay WHERE pay.id_reservasi = r.id_reservasi ORDER BY pay.id_payment DESC LIMIT 1) AS status_payment,
                         (SELECT uAdmin.nama FROM payment pay JOIN users uAdmin ON pay.verified_by = uAdmin.id_user WHERE pay.id_reservasi = r.id_reservasi ORDER BY pay.id_payment DESC LIMIT 1) AS namaAdmin
                  FROM transaksi t
                  LEFT JOIN reservasi r ON t.id_reservasi = r.id_reservasi
                  LEFT JOIN users p ON r.id_user = p.id_user
                  WHERE t.id_transaksi = :id_transaksi 
                  LIMIT 1";

        $tx = $this->fetchOne($query, [':id_transaksi' => $idTransaksi]);

        if ($tx) {
            $tx['details'] = $this->getDetails($idTransaksi);
        }

        return $tx;
    }

    public function getDetails($idTransaksi) {
        $query = "SELECT dt.*, l.nama_layanan, l.harga, l.durasi 
                  FROM detail_transaksi dt
                  JOIN layanan l ON dt.id_layanan = l.id_layanan
                  WHERE dt.id_transaksi = :id_transaksi";

        return $this->fetchAll($query, [':id_transaksi' => $idTransaksi]);
    }

    public function getByReservasiId($idReservasi) {
        $query = "SELECT * 
                  FROM transaksi 
                  WHERE id_reservasi = :id_reservasi 
                  LIMIT 1";

        return $this->fetchOne($query, [':id_reservasi' => $idReservasi]);
    }

    public function create($idReservasi, $totalPayment, $services, $uangBayar = 0, $kembalian = 0) {
        try {
            $this->db->beginTransaction();

            $query = "INSERT INTO transaksi (id_reservasi, total_payment, uang_bayar, kembalian) 
                      VALUES (:id_reservasi, :total_payment, :uang_bayar, :kembalian)";

            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id_reservasi', empty($idReservasi) ? null : $idReservasi, PDO::PARAM_INT);
            $stmt->bindParam(':total_payment', $totalPayment);
            $stmt->bindParam(':uang_bayar', $uangBayar);
            $stmt->bindParam(':kembalian', $kembalian);
            $stmt->execute();

            $idTransaksi = $this->db->lastInsertId();

            $detailQuery = "INSERT INTO detail_transaksi (id_transaksi, id_layanan, qty, subtotal) 
                            VALUES (:id_transaksi, :id_layanan, :qty, :subtotal)";
            $detailStmt = $this->db->prepare($detailQuery);

            foreach ($services as $service) {
                $detailStmt->bindParam(':id_transaksi', $idTransaksi, PDO::PARAM_INT);
                $idLayanan = $service['idLayanan'] ?? $service['id_layanan'];
                $detailStmt->bindParam(':id_layanan', $idLayanan, PDO::PARAM_INT);
                $qty = $service['qty'] ?? 1;
                $detailStmt->bindParam(':qty', $qty, PDO::PARAM_INT);
                $detailStmt->bindParam(':subtotal', $service['subtotal']);
                $detailStmt->execute();
            }

            $this->db->commit();
            return $idTransaksi;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function deleteByReservasiId($idReservasi) {
        $query = "DELETE FROM transaksi 
                  WHERE id_reservasi = :id_reservasi";

        return $this->execute($query, [':id_reservasi' => $idReservasi]);
    }

    public function updateTotalByReservasiId($idReservasi, $totalPayment) {
        $query = "UPDATE transaksi
                  SET total_payment = :total_payment,
                      uang_bayar = :total_payment,
                      kembalian = 0
                  WHERE id_reservasi = :id_reservasi";

        return $this->execute($query, [
            ':id_reservasi' => $idReservasi,
            ':total_payment' => $totalPayment
        ]);
    }

    public function generateLaporan($startDate, $endDate) {
        $query = "SELECT t.*, r.reservation_date, r.reservation_type, r.status_reservation, r.total_price,
                         p.nama AS namaPelanggan,
                         (SELECT GROUP_CONCAT(DISTINCT t2.nama_terapis SEPARATOR ', ') FROM reservasi_detail rd2 JOIN terapis t2 ON rd2.id_terapis = t2.id_terapis WHERE rd2.id_reservasi = r.id_reservasi) AS namaTerapis,
                         (SELECT pay.payment_method FROM payment pay WHERE pay.id_reservasi = r.id_reservasi ORDER BY pay.id_payment DESC LIMIT 1) AS payment_method,
                         (SELECT pay.status_payment FROM payment pay WHERE pay.id_reservasi = r.id_reservasi ORDER BY pay.id_payment DESC LIMIT 1) AS status_payment,
                         (SELECT pay.jenis_pembayaran FROM payment pay WHERE pay.id_reservasi = r.id_reservasi ORDER BY pay.id_payment DESC LIMIT 1) AS jenis_pembayaran,
                         (SELECT pay.pelunasan_method FROM payment pay WHERE pay.id_reservasi = r.id_reservasi ORDER BY pay.id_payment DESC LIMIT 1) AS pelunasan_method,
                         GROUP_CONCAT(DISTINCT l.nama_layanan SEPARATOR ', ') AS layananNames
                  FROM transaksi t
                  LEFT JOIN reservasi r ON t.id_reservasi = r.id_reservasi
                  LEFT JOIN users p ON r.id_user = p.id_user
                  LEFT JOIN detail_transaksi dt ON t.id_transaksi = dt.id_transaksi
                  LEFT JOIN layanan l ON dt.id_layanan = l.id_layanan
                  WHERE DATE(t.transaction_date) BETWEEN :startDate AND :endDate
                  GROUP BY t.id_transaksi
                  ORDER BY t.transaction_date ASC";

        return $this->fetchAll($query, [
            ':startDate' => $startDate,
            ':endDate'   => $endDate
        ]);
    }

    public function getStatistik() {
        $revenue      = $this->fetchOne("SELECT SUM(total_payment) AS total FROM transaksi");
        $reservations = $this->fetchOne("SELECT COUNT(*) AS total FROM reservasi");
        $transactions = $this->fetchOne("SELECT COUNT(*) AS total FROM transaksi");
        $customers    = $this->fetchOne("SELECT COUNT(*) AS total FROM users WHERE role = 'pelanggan'");
        $resToday     = $this->fetchOne("SELECT COUNT(*) AS total FROM reservasi WHERE DATE(reservation_date) = CURRENT_DATE()");
        $services     = $this->fetchOne("SELECT COUNT(*) AS total FROM layanan");
        $therapists   = $this->fetchOne("SELECT COUNT(*) AS total FROM terapis WHERE status = 'aktif'");

        $chartData = $this->fetchAll(
            "SELECT DATE(transaction_date) AS tanggal, SUM(total_payment) AS total 
             FROM transaksi 
             WHERE transaction_date >= DATE_SUB(CURRENT_DATE(), INTERVAL 6 DAY)
             GROUP BY DATE(transaction_date)
             ORDER BY tanggal ASC"
        );

        return [
            'totalPendapatan'       => $revenue['total'] ?? 0,
            'totalReservasi'        => $reservations['total'] ?? 0,
            'totalTransaksi'        => $transactions['total'] ?? 0,
            'totalPelanggan'        => $customers['total'] ?? 0,
            'totalReservasiHariIni' => $resToday['total'] ?? 0,
            'totalLayanan'          => $services['total'] ?? 0,
            'totalTerapisAktif'     => $therapists['total'] ?? 0,
            'chartData'             => $chartData
        ];
    }
}
?>
