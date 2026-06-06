<?php

require_once __DIR__ . '/BaseModel.php';

class Reservasi extends BaseModel {
    private const MENIT_KADALUWARSA_RESERVASI = 30;

    public function batalkanPendingLewatJamBooking() {
        $this->execute("UPDATE payment p
                        JOIN Reservasi r ON p.id_reservasi = r.id_reservasi
                        SET p.status_payment = 'rejected'
                        WHERE r.status_reservation = 'Menunggu Validasi'
                          AND p.status_payment IN ('Menunggu Validasi', 'pending', 'Menunggu Pembayaran')
                          AND r.reservation_date < NOW()");

        $this->execute("UPDATE Reservasi r
                        LEFT JOIN payment p ON p.id_reservasi = r.id_reservasi
                        SET r.status_reservation = 'Dibatalkan'
                        WHERE r.status_reservation = 'Menunggu Pembayaran'
                          AND p.id_payment IS NULL
                          AND (
                              r.created_at < DATE_SUB(NOW(), INTERVAL " . self::MENIT_KADALUWARSA_RESERVASI . " MINUTE)
                              OR r.reservation_date < NOW()
                          )");

        $query = "UPDATE Reservasi
                  SET status_reservation = 'Dibatalkan'
                  WHERE status_reservation = 'Menunggu Validasi'
                    AND reservation_date < NOW()";

        return $this->execute($query);
    }

    public function getAll($status = null) {
        $this->batalkanPendingLewatJamBooking();
        $query = "SELECT r.*, p.nama AS namaPelanggan, p.no_telepon AS noHpPelanggan, 
                         (SELECT GROUP_CONCAT(DISTINCT t2.nama_terapis SEPARATOR ', ') FROM Reservasi_detail rd2 JOIN Terapis t2 ON rd2.id_terapis = t2.id_terapis WHERE rd2.id_reservasi = r.id_reservasi) AS namaTerapis,
                         rg.nama_ruangan AS namaRuangan,
                         GROUP_CONCAT(DISTINCT l.nama_layanan SEPARATOR ', ') AS layananNames
                  FROM Reservasi r
                  JOIN users p ON r.id_user = p.id_user
                  LEFT JOIN ruangan rg ON r.id_ruangan = rg.id_ruangan
                  LEFT JOIN Reservasi_detail rd ON r.id_reservasi = rd.id_reservasi
                  LEFT JOIN Layanan l ON rd.id_layanan = l.id_layanan";

        $params = [];
        if ($status !== null) {
            if (is_array($status)) {
                $placeholders = [];
                foreach ($status as $i => $s) {
                    $key = ':status_' . $i;
                    $placeholders[] = $key;
                    $params[$key] = $s;
                }
                $query .= " WHERE r.status_reservation IN (" . implode(', ', $placeholders) . ")";
            } else {
                $query .= " WHERE r.status_reservation = :status";
                $params[':status'] = $status;
            }
        }

        $query .= " GROUP BY r.id_reservasi ORDER BY r.id_reservasi DESC";

        return $this->fetchAll($query, $params);
    }

    public function getById($idReservasi) {
        $this->batalkanPendingLewatJamBooking();

        $query = "SELECT r.*, p.nama AS namaPelanggan, p.no_telepon AS noHpPelanggan, p.email AS emailPelanggan, p.rating_pelanggan,
                         (SELECT GROUP_CONCAT(DISTINCT t2.nama_terapis SEPARATOR ', ') FROM Reservasi_detail rd2 JOIN Terapis t2 ON rd2.id_terapis = t2.id_terapis WHERE rd2.id_reservasi = r.id_reservasi) AS namaTerapis,
                         (SELECT MIN(rd3.id_terapis) FROM Reservasi_detail rd3 WHERE rd3.id_reservasi = r.id_reservasi) AS id_terapis,
                         rg.nama_ruangan AS namaRuangan
                  FROM Reservasi r
                  JOIN users p ON r.id_user = p.id_user
                  LEFT JOIN ruangan rg ON r.id_ruangan = rg.id_ruangan
                  WHERE r.id_reservasi = :id_reservasi 
                  LIMIT 1";

        $res = $this->fetchOne($query, [':id_reservasi' => $idReservasi]);

        if ($res) {
            $res['details'] = $this->getDetails($idReservasi);
        }

        return $res;
    }

    public function getDetails($idReservasi) {
        $query = "SELECT rd.*, l.nama_layanan, l.kategori, l.harga, l.durasi, t.nama_terapis
                  FROM Reservasi_detail rd
                  JOIN Layanan l ON rd.id_layanan = l.id_layanan
                  LEFT JOIN Terapis t ON rd.id_terapis = t.id_terapis
                  WHERE rd.id_reservasi = :id_reservasi";

        return $this->fetchAll($query, [':id_reservasi' => $idReservasi]);
    }

    public function updateStatus($idReservasi, $status) {
        $query = "UPDATE Reservasi 
                  SET status_reservation = :status 
                  WHERE id_reservasi = :id_reservasi";

        return $this->execute($query, [
            ':id_reservasi' => $idReservasi,
            ':status'      => $status
        ]);
    }

    public function assignTerapis($idReservasi, $idTerapis) {
        $query = "UPDATE Reservasi_detail 
                  SET id_terapis = :id_terapis 
                  WHERE id_reservasi = :id_reservasi";

        return $this->execute($query, [
            ':id_reservasi' => $idReservasi,
            ':id_terapis'   => empty($idTerapis) ? null : $idTerapis
        ]);
    }

    public function assignTerapisToDetail($idDetail, $idTerapis) {
        $query = "UPDATE Reservasi_detail 
                  SET id_terapis = :id_terapis 
                  WHERE id_detail = :id_detail";

        return $this->execute($query, [
            ':id_detail'  => $idDetail,
            ':id_terapis' => empty($idTerapis) ? null : $idTerapis
        ]);
    }

    public function isTherapistAvailable($idTerapis, $reservationDate, $excludeIdReservasi = null) {
        if (empty($idTerapis)) {
            return true;
        }

        $query = "SELECT COUNT(*) AS total 
                  FROM Reservasi_detail rd
                  JOIN Reservasi r ON rd.id_reservasi = r.id_reservasi
                  WHERE rd.id_terapis = :id_terapis 
                    AND r.status_reservation IN ('Diterima', 'Dikonfirmasi')
                    AND ABS(TIMESTAMPDIFF(MINUTE, r.reservation_date, :reservation_date)) < 60";

        $params = [
            ':id_terapis'      => $idTerapis,
            ':reservation_date' => $reservationDate
        ];

        if ($excludeIdReservasi) {
            $query .= " AND r.id_reservasi != :excludeIdReservasi";
            $params[':excludeIdReservasi'] = $excludeIdReservasi;
        }

        $row = $this->fetchOne($query, $params);
        return ($row && $row['total'] == 0);
    }

    public function createReservation($idUser, $idTerapis, $reservationDate, $reservationType, $statusReservation, $totalPrice, $services) {
        try {
            $this->db->beginTransaction();

            $query = "INSERT INTO Reservasi (id_user, id_ruangan, reservation_date, reservation_type, status_reservation, total_price) 
                      VALUES (:id_user, NULL, :reservation_date, :reservation_type, :status_reservation, :total_price)";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_user', $idUser, PDO::PARAM_INT);
            $stmt->bindParam(':reservation_date', $reservationDate);
            $stmt->bindParam(':reservation_type', $reservationType);
            $stmt->bindParam(':status_reservation', $statusReservation);
            $stmt->bindParam(':total_price', $totalPrice);
            $stmt->execute();

            $idReservasi = $this->db->lastInsertId();

            $detailQuery = "INSERT INTO Reservasi_detail (id_reservasi, id_layanan, id_terapis, qty, subtotal) 
                            VALUES (:id_reservasi, :id_layanan, :id_terapis, :qty, :subtotal)";
            $detailStmt = $this->db->prepare($detailQuery);

            foreach ($services as $service) {
                $detailStmt->bindParam(':id_reservasi', $idReservasi, PDO::PARAM_INT);
                $idLayanan = $service['idLayanan'] ?? $service['id_layanan'];
                $detailStmt->bindParam(':id_layanan', $idLayanan, PDO::PARAM_INT);
                
                $svcTerapis = $service['id_terapis'] ?? $service['idTerapis'] ?? $idTerapis;
                $tId = empty($svcTerapis) ? null : $svcTerapis;
                $detailStmt->bindValue(':id_terapis', $tId, PDO::PARAM_INT);
                
                $qty = $service['qty'] ?? 1;
                $detailStmt->bindParam(':qty', $qty, PDO::PARAM_INT);
                $detailStmt->bindParam(':subtotal', $service['subtotal']);
                $detailStmt->execute();
            }

            $this->db->commit();
            return $idReservasi;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function getCounts() {
        $this->batalkanPendingLewatJamBooking();

        $query = "SELECT status_reservation, COUNT(*) AS cnt FROM Reservasi GROUP BY status_reservation";
        $rows = $this->fetchAll($query);
        
        $counts = [
            'Semua' => 0,
            'Menunggu' => 0,
            'Diterima' => 0,
            'Ditolak' => 0,
            'Dibatalkan' => 0,
            'Selesai' => 0
        ];

        foreach ($rows as $row) {
            $status = $row['status_reservation'] ?? $row['statusReservation'] ?? '';
            $cnt = (int)$row['cnt'];
            $counts['Semua'] += $cnt;

            if (in_array($status, ['Menunggu', 'Menunggu Pembayaran', 'Menunggu Validasi'])) {
                $counts['Menunggu'] += $cnt;
            } elseif (in_array($status, ['Diterima', 'Dikonfirmasi'])) {
                $counts['Diterima'] += $cnt;
            } elseif ($status === 'Ditolak') {
                $counts['Ditolak'] += $cnt;
            } elseif (in_array($status, ['Dibatalkan', 'Hangus'])) {
                $counts['Dibatalkan'] += $cnt;
            } elseif ($status === 'Selesai') {
                $counts['Selesai'] += $cnt;
            }
        }

        return $counts;
    }

    public function getRuanganStatus() {
        $this->batalkanPendingLewatJamBooking();

        $query = "SELECT r.*,
                         (SELECT COUNT(*) 
                          FROM Reservasi res 
                          WHERE res.id_ruangan = r.id_ruangan 
                            AND res.status_reservation IN ('Diterima', 'Dikonfirmasi') 
                            AND NOW() >= res.reservation_date 
                            AND NOW() < DATE_ADD(res.reservation_date, INTERVAL (
                                SELECT COALESCE(SUM(l.durasi), 90) 
                                FROM Reservasi_detail rd 
                                JOIN Layanan l ON rd.id_layanan = l.id_layanan 
                                WHERE rd.id_reservasi = res.id_reservasi
                            ) MINUTE)
                         ) AS is_busy,
                         (SELECT p.nama 
                          FROM Reservasi res
                          JOIN users p ON res.id_user = p.id_user
                          WHERE res.id_ruangan = r.id_ruangan 
                            AND res.status_reservation IN ('Diterima', 'Dikonfirmasi') 
                            AND NOW() >= res.reservation_date 
                            AND NOW() < DATE_ADD(res.reservation_date, INTERVAL (
                                SELECT COALESCE(SUM(l.durasi), 90) 
                                FROM Reservasi_detail rd 
                                JOIN Layanan l ON rd.id_layanan = l.id_layanan 
                                WHERE rd.id_reservasi = res.id_reservasi
                            ) MINUTE)
                          LIMIT 1
                         ) AS nama_pelanggan
                  FROM ruangan r
                  ORDER BY LENGTH(r.nama_ruangan) ASC, r.nama_ruangan ASC";
        return $this->fetchAll($query);
    }
}
?>
