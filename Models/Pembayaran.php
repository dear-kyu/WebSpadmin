<?php

require_once __DIR__ . '/BaseModel.php';

class Pembayaran extends BaseModel {

    public function __construct() {
        parent::__construct();
        $this->pastikanKolomPelunasan();
    }

    private function pastikanKolomPelunasan() {
        $this->pastikanKolomTransaksiPembayaran();

        $columns = $this->fetchAll("SHOW COLUMNS FROM payment LIKE 'pelunasan_method'");
        if (empty($columns)) {
            $this->execute("ALTER TABLE payment ADD COLUMN pelunasan_method VARCHAR(50) NULL AFTER verified_by");
        } elseif (stripos($columns[0]['type'] ?? '', 'varchar(50)') === false) {
            $this->execute("ALTER TABLE payment MODIFY COLUMN pelunasan_method VARCHAR(50) NULL");
        }

        $columns = $this->fetchAll("SHOW COLUMNS FROM payment LIKE 'pelunasan_date'");
        if (empty($columns)) {
            $this->execute("ALTER TABLE payment ADD COLUMN pelunasan_date DATETIME NULL AFTER pelunasan_method");
        }

        $columns = $this->fetchAll("SHOW COLUMNS FROM payment LIKE 'pelunasan_uang_bayar'");
        if (empty($columns)) {
            $this->execute("ALTER TABLE payment ADD COLUMN pelunasan_uang_bayar INT NOT NULL DEFAULT 0 AFTER pelunasan_date");
        }

        $columns = $this->fetchAll("SHOW COLUMNS FROM payment LIKE 'pelunasan_kembalian'");
        if (empty($columns)) {
            $this->execute("ALTER TABLE payment ADD COLUMN pelunasan_kembalian INT NOT NULL DEFAULT 0 AFTER pelunasan_uang_bayar");
        }

        $columns = $this->fetchAll("SHOW COLUMNS FROM payment LIKE 'jenis_pembayaran'");
        if (empty($columns)) {
            $this->execute("ALTER TABLE payment ADD COLUMN jenis_pembayaran VARCHAR(20) NOT NULL DEFAULT 'DP 50%' AFTER status_payment");
        }

        $columns = $this->fetchAll("SHOW COLUMNS FROM payment LIKE 'nominal_payment'");
        if (empty($columns)) {
            $this->execute("ALTER TABLE payment ADD COLUMN nominal_payment INT NOT NULL DEFAULT 0 AFTER jenis_pembayaran");
            $this->execute("UPDATE payment p
                            JOIN Reservasi r ON p.id_reservasi = r.id_reservasi
                            SET p.nominal_payment = CASE
                                WHEN r.reservation_type = 'online' THEN r.total_price * 0.5
                                ELSE r.total_price
                            END
                            WHERE p.nominal_payment = 0");
            $this->execute("UPDATE transaksi t
                            JOIN payment p ON t.id_reservasi = p.id_reservasi
                            JOIN Reservasi r ON t.id_reservasi = r.id_reservasi
                            SET t.total_payment = p.nominal_payment,
                                t.uang_bayar = p.nominal_payment,
                                t.kembalian = 0
                            WHERE r.reservation_type = 'online'
                              AND p.status_payment NOT IN ('Lunas')");
        }
    }

    private function pastikanKolomTransaksiPembayaran() {
        $columns = $this->fetchAll("SHOW COLUMNS FROM transaksi LIKE 'uang_bayar'");
        if (empty($columns)) {
            $this->execute("ALTER TABLE transaksi ADD COLUMN uang_bayar INT NOT NULL DEFAULT 0 AFTER total_payment");
            $this->execute("UPDATE transaksi SET uang_bayar = total_payment WHERE uang_bayar = 0");
        }

        $columns = $this->fetchAll("SHOW COLUMNS FROM transaksi LIKE 'kembalian'");
        if (empty($columns)) {
            $this->execute("ALTER TABLE transaksi ADD COLUMN kembalian INT NOT NULL DEFAULT 0 AFTER uang_bayar");
        }
    }

    public function getAll($status = null) {
        $query = "SELECT pb.*, r.reservation_date, r.status_reservation, r.total_price,
                         COALESCE(NULLIF(pb.nominal_payment, 0), r.total_price * 0.5) AS nominal_bayar,
                         p.nama AS namaPelanggan,
                         GROUP_CONCAT(l.nama_layanan SEPARATOR ', ') AS layananNames
                  FROM payment pb
                  JOIN Reservasi r ON pb.id_reservasi = r.id_reservasi
                  JOIN users p ON r.id_user = p.id_user
                  LEFT JOIN Reservasi_detail rd ON r.id_reservasi = rd.id_reservasi
                  LEFT JOIN Layanan l ON rd.id_layanan = l.id_layanan";

        $params = [];
        if ($status !== null) {
            $query .= " WHERE pb.status_payment = :status";
            $params[':status'] = $status;
        }

        $query .= " GROUP BY pb.id_payment ORDER BY pb.id_payment DESC";

        return $this->fetchAll($query, $params);
    }

    public function sinkronkanPembayaranSelesai() {
        $this->execute("UPDATE payment pb
                        JOIN Reservasi r ON pb.id_reservasi = r.id_reservasi
                        SET pb.status_payment = 'Lunas',
                            pb.nominal_payment = r.total_price,
                            pb.payment_method = TRIM(REPLACE(REPLACE(pb.payment_method, ' (DP Hangus)', ''), ' (Pembayaran Hangus)', ''))
                        WHERE r.status_reservation = 'Selesai'
                          AND pb.status_payment IN ('verified', 'Diterima', 'DP Hangus', 'Pembayaran Hangus')");

        return $this->execute("UPDATE transaksi t
                               JOIN Reservasi r ON t.id_reservasi = r.id_reservasi
                               JOIN payment pb ON pb.id_reservasi = r.id_reservasi
                               SET t.total_payment = r.total_price,
                                   t.uang_bayar = r.total_price,
                                   t.kembalian = 0
                               WHERE r.status_reservation = 'Selesai'
                                 AND pb.status_payment = 'Lunas'");
    }

    public function getById($idPayment) {
        $query = "SELECT pb.*, r.reservation_date, r.status_reservation, r.total_price,
                         COALESCE(NULLIF(pb.nominal_payment, 0), r.total_price * 0.5) AS nominal_bayar,
                         p.nama AS namaPelanggan, p.no_telepon AS noHpPelanggan,
                         uAdmin.nama AS namaVerifier,
                         GROUP_CONCAT(l.nama_layanan SEPARATOR ', ') AS layananNames
                  FROM payment pb
                  JOIN Reservasi r ON pb.id_reservasi = r.id_reservasi
                  JOIN users p ON r.id_user = p.id_user
                  LEFT JOIN Reservasi_detail rd ON r.id_reservasi = rd.id_reservasi
                  LEFT JOIN Layanan l ON rd.id_layanan = l.id_layanan
                  LEFT JOIN users uAdmin ON pb.verified_by = uAdmin.id_user
                  WHERE pb.id_payment = :id_payment 
                  GROUP BY pb.id_payment 
                  LIMIT 1";

        return $this->fetchOne($query, [':id_payment' => $idPayment]);
    }

    public function getByReservasiId($idReservasi) {
        $query = "SELECT pb.*, uAdmin.nama AS namaVerifier 
                  FROM payment pb
                  LEFT JOIN users uAdmin ON pb.verified_by = uAdmin.id_user
                  WHERE pb.id_reservasi = :id_reservasi 
                  LIMIT 1";

        return $this->fetchOne($query, [':id_reservasi' => $idReservasi]);
    }

    public function verifikasi($idPayment, $statusPayment, $verifiedBy, $paymentMethod = null) {
        $params = [
            ':id_payment'     => $idPayment,
            ':status_payment' => $statusPayment,
            ':verified_by'    => empty($verifiedBy) ? null : $verifiedBy
        ];

        if ($paymentMethod !== null) {
            $query = "UPDATE payment 
                      SET status_payment = :status_payment, 
                          verified_by = :verified_by, 
                          payment_method = :payment_method 
                      WHERE id_payment = :id_payment";
            $params[':payment_method'] = $paymentMethod;
        } else {
            $query = "UPDATE payment 
                      SET status_payment = :status_payment, 
                          verified_by = :verified_by 
                      WHERE id_payment = :id_payment";
        }

        return $this->execute($query, $params);
    }

    public function prosesLunas($idPayment, $metodePelunasan, $uangBayar, $kembalian, $verifiedBy) {
        $query = "UPDATE payment 
                  SET status_payment = 'Lunas',
                      pelunasan_method = :pelunasan_method,
                      pelunasan_date = NOW(),
                      pelunasan_uang_bayar = :pelunasan_uang_bayar,
                      pelunasan_kembalian = :pelunasan_kembalian,
                      verified_by = :verified_by
                  WHERE id_payment = :id_payment
                    AND status_payment IN ('verified', 'Diterima')";

        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':id_payment'       => $idPayment,
            ':pelunasan_method' => $metodePelunasan,
            ':pelunasan_uang_bayar' => $uangBayar,
            ':pelunasan_kembalian'  => $kembalian,
            ':verified_by'      => empty($verifiedBy) ? null : $verifiedBy,
        ]);

        return $stmt->rowCount() === 1;
    }

    public function updateNominalPayment($idPayment, $nominalPayment) {
        $query = "UPDATE payment
                  SET nominal_payment = :nominal_payment
                  WHERE id_payment = :id_payment";

        return $this->execute($query, [
            ':id_payment' => $idPayment,
            ':nominal_payment' => $nominalPayment
        ]);
    }

    public function verifikasiLunasAwal($idPayment, $verifiedBy) {
        $query = "UPDATE payment
                  SET status_payment = 'Lunas',
                      pelunasan_date = NOW(),
                      verified_by = :verified_by
                  WHERE id_payment = :id_payment";

        return $this->execute($query, [
            ':id_payment' => $idPayment,
            ':verified_by' => empty($verifiedBy) ? null : $verifiedBy
        ]);
    }

    public function create($idReservasi, $paymentMethod, $paymentProof = null, $statusPayment = 'pending', $verifiedBy = null, $jenisPembayaran = 'Lunas 100%', $nominalPayment = 0) {
        $query = "INSERT INTO payment (id_reservasi, payment_method, payment_proof, status_payment, verified_by, jenis_pembayaran, nominal_payment) 
                  VALUES (:id_reservasi, :payment_method, :payment_proof, :status_payment, :verified_by, :jenis_pembayaran, :nominal_payment)";

        return $this->execute($query, [
            ':id_reservasi'   => $idReservasi,
            ':payment_method' => $paymentMethod,
            ':payment_proof'  => $paymentProof,
            ':status_payment' => $statusPayment,
            ':verified_by'    => empty($verifiedBy) ? null : $verifiedBy,
            ':jenis_pembayaran' => $jenisPembayaran,
            ':nominal_payment'  => $nominalPayment
        ]);
    }

    public function getAllRekening() {
        $query = "SELECT * FROM rekening ORDER BY id_rekening ASC";
        return $this->fetchAll($query);
    }

    public function getMetodePelunasanAktif() {
        $query = "SELECT DISTINCT TRIM(nama_bank) AS nama_bank
                  FROM rekening
                  WHERE TRIM(nama_bank) <> ''
                    AND LOWER(TRIM(nama_bank)) <> 'cash'
                  ORDER BY nama_bank ASC";
        $rows = $this->fetchAll($query);

        return array_values(array_filter(array_map(function($row) {
            return trim($row['nama_bank'] ?? '');
        }, $rows)));
    }

    public function createRekening($namaBank, $nomorRekening, $atasNama) {
        $query = "INSERT INTO rekening (nama_bank, nomor_rekening, atas_nama) VALUES (:nama_bank, :nomor_rekening, :atas_nama)";
        return $this->execute($query, [
            ':nama_bank'       => $namaBank,
            ':nomor_rekening'  => $nomorRekening,
            ':atas_nama'       => $atasNama
        ]);
    }

    public function deleteRekening($id) {
        $query = "DELETE FROM rekening WHERE id_rekening = :id";
        return $this->execute($query, [':id' => $id]);
    }
}
?>
