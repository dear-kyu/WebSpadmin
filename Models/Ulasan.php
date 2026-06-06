<?php

require_once __DIR__ . '/BaseModel.php';

class Ulasan extends BaseModel {

    public function getAll() {
        $query = "SELECT u.*, p.nama AS namaPelanggan, p.email AS emailPelanggan, l.nama_layanan 
                  FROM ulasan u
                  JOIN users p ON u.user_id = p.id_user
                  JOIN Layanan l ON u.id_layanan = l.id_layanan
                  ORDER BY u.id_ulasan DESC";

        return $this->fetchAll($query);
    }

    public function getById($idUlasan) {
        $query = "SELECT * 
                  FROM ulasan 
                  WHERE id_ulasan = :id_ulasan 
                  LIMIT 1";

        return $this->fetchOne($query, [':id_ulasan' => $idUlasan]);
    }

    public function create($userId, $idLayanan, $rating, $ulasan) {
        $query = "INSERT INTO ulasan (user_id, id_layanan, rating, ulasan) 
                  VALUES (:user_id, :id_layanan, :rating, :ulasan)";

        return $this->execute($query, [
            ':user_id'   => $userId,
            ':id_layanan' => $idLayanan,
            ':rating'   => $rating,
            ':ulasan'   => $ulasan
        ]);
    }

    public function balas($idUlasan, $balasanAdmin) {
        $query = "UPDATE ulasan 
                  SET balasan_admin = :balasan_admin 
                  WHERE id_ulasan = :id_ulasan";

        return $this->execute($query, [
            ':id_ulasan'     => $idUlasan,
            ':balasan_admin' => $balasanAdmin
        ]);
    }

    public function delete($idUlasan) {
        $query = "DELETE FROM ulasan 
                  WHERE id_ulasan = :id_ulasan";

        return $this->execute($query, [':id_ulasan' => $idUlasan]);
    }
}
?>
