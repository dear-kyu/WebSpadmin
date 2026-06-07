<?php

require_once __DIR__ . '/BaseModel.php';

class Ruangan extends BaseModel {

    public function getAll() {
        $query = "SELECT * FROM ruangan ORDER BY LENGTH(nama_ruangan) ASC, nama_ruangan ASC";
        return $this->fetchAll($query);
    }

    public function getById($idRuangan) {
        $query = "SELECT * FROM ruangan WHERE id_ruangan = :id_ruangan LIMIT 1";
        return $this->fetchOne($query, [':id_ruangan' => $idRuangan]);
    }

    public function update($idRuangan, $namaRuangan, $status) {
        $query = "UPDATE ruangan 
                  SET nama_ruangan = :nama_ruangan, 
                       status = :status 
                  WHERE id_ruangan = :id_ruangan";
        return $this->execute($query, [
            ':id_ruangan'   => $idRuangan,
            ':nama_ruangan' => $namaRuangan,
            ':status'       => $status
        ]);
    }

    public function toggleStatus($idRuangan) {
        $r = $this->getById($idRuangan);
        if (!$r) return false;
        $newStatus = $r['status'] === 'aktif' ? 'tidak aktif' : 'aktif';
        $query = "UPDATE ruangan SET status = :status WHERE id_ruangan = :id_ruangan";
        return $this->execute($query, [
            ':id_ruangan' => $idRuangan,
            ':status' => $newStatus
        ]);
    }

    public function isRoomNameExists($namaRuangan, $excludeId = 0) {
        $query = "SELECT COUNT(*) AS total FROM ruangan 
                  WHERE LOWER(TRIM(nama_ruangan)) = LOWER(TRIM(:nama_ruangan)) 
                    AND id_ruangan != :exclude_id";
        $res = $this->fetchOne($query, [
            ':nama_ruangan' => $namaRuangan,
            ':exclude_id'   => $excludeId
        ]);
        return $res ? (intval($res['total']) > 0) : false;
    }

    public function hasActiveBooking($idRuangan) {
        $query = "SELECT COUNT(*) AS total FROM reservasi 
                  WHERE id_ruangan = :id_ruangan 
                    AND status_reservation IN ('Diterima', 'Dikonfirmasi')";
        $res = $this->fetchOne($query, [':id_ruangan' => $idRuangan]);
        return $res ? (intval($res['total']) > 0) : false;
    }
}
?>
