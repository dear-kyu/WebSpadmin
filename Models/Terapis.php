<?php

require_once __DIR__ . '/BaseModel.php';

class Terapis extends BaseModel {

    public function getAll() {
        $query = "SELECT t.*,
                         (SELECT COUNT(*) 
                          FROM Reservasi r 
                          JOIN Reservasi_detail rd_check ON r.id_reservasi = rd_check.id_reservasi
                          WHERE rd_check.id_terapis = t.id_terapis 
                            AND r.status_reservation IN ('Diterima', 'Dikonfirmasi') 
                            AND NOW() >= r.reservation_date 
                            AND NOW() < DATE_ADD(r.reservation_date, INTERVAL (
                                SELECT COALESCE(SUM(l.durasi), 90) 
                                FROM Reservasi_detail rd 
                                JOIN Layanan l ON rd.id_layanan = l.id_layanan 
                                WHERE rd.id_reservasi = r.id_reservasi
                            ) MINUTE)
                         ) AS is_busy
                  FROM Terapis t
                  ORDER BY t.id_terapis DESC";

        return $this->fetchAll($query);
    }

    public function getActive() {
        $query = "SELECT t.*,
                         (SELECT COUNT(*) 
                          FROM Reservasi r 
                          JOIN Reservasi_detail rd_check ON r.id_reservasi = rd_check.id_reservasi
                          WHERE rd_check.id_terapis = t.id_terapis 
                            AND r.status_reservation IN ('Diterima', 'Dikonfirmasi') 
                            AND NOW() >= r.reservation_date 
                            AND NOW() < DATE_ADD(r.reservation_date, INTERVAL (
                                SELECT COALESCE(SUM(l.durasi), 90) 
                                FROM Reservasi_detail rd 
                                JOIN Layanan l ON rd.id_layanan = l.id_layanan 
                                WHERE rd.id_reservasi = r.id_reservasi
                            ) MINUTE)
                         ) AS is_busy
                  FROM Terapis t 
                  WHERE t.status = 'Aktif' 
                  ORDER BY t.nama_terapis ASC";

        return $this->fetchAll($query);
    }

    public function getById($idTerapis) {
        $query = "SELECT * 
                  FROM Terapis 
                  WHERE id_terapis = :id_terapis 
                  LIMIT 1";

        return $this->fetchOne($query, [':id_terapis' => $idTerapis]);
    }

    public function create($namaTerapis, $spesialisasi, $noTelp, $status, $jenisKelamin = 'Perempuan') {
        $query = "INSERT INTO Terapis (nama_terapis, spesialisasi, no_telp, status, jenis_kelamin) 
                  VALUES (:nama_terapis, :spesialisasi, :no_telp, :status, :jenis_kelamin)";

        return $this->execute($query, [
            ':nama_terapis' => $namaTerapis,
            ':spesialisasi' => $spesialisasi,
            ':no_telp' => $noTelp,
            ':status' => $status,
            ':jenis_kelamin' => $jenisKelamin
        ]);
    }

    public function update($idTerapis, $namaTerapis, $spesialisasi, $noTelp, $status, $jenisKelamin = 'Perempuan') {
        $query = "UPDATE Terapis 
                  SET nama_terapis = :nama_terapis, 
                      spesialisasi = :spesialisasi, 
                      no_telp = :no_telp, 
                      status = :status,
                      jenis_kelamin = :jenis_kelamin 
                  WHERE id_terapis = :id_terapis";

        return $this->execute($query, [
            ':id_terapis' => $idTerapis,
            ':nama_terapis' => $namaTerapis,
            ':spesialisasi' => $spesialisasi,
            ':no_telp' => $noTelp,
            ':status' => $status,
            ':jenis_kelamin' => $jenisKelamin
        ]);
    }

    public function delete($idTerapis) {
        $query = "DELETE FROM Terapis 
                  WHERE id_terapis = :id_terapis";

        return $this->execute($query, [':id_terapis' => $idTerapis]);
    }
}
?>
