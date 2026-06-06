<?php

require_once __DIR__ . '/BaseModel.php';

class Pelanggan extends BaseModel {

    public function getAll() {
        $query = "SELECT id_user, nama, email, no_telepon, role, created_at 
                  FROM users 
                  WHERE role = 'pelanggan' 
                  ORDER BY nama ASC";

        return $this->fetchAll($query);
    }

    public function getById($idUser) {
        $query = "SELECT id_user, nama, email, no_telepon, role, created_at 
                  FROM users 
                  WHERE id_user = :id_user 
                    AND role = 'pelanggan' 
                  LIMIT 1";

        return $this->fetchOne($query, [':id_user' => $idUser]);
    }

    public function create($nama, $email, $noTelepon, $password = 'customer123') {
        $query = "INSERT INTO users (nama, email, no_telepon, password, role) 
                  VALUES (:nama, :email, :no_telepon, :password, 'pelanggan')";

        $result = $this->execute($query, [
            ':nama' => $nama,
            ':email' => $email,
            ':no_telepon' => $noTelepon,
            ':password' => $password
        ]);

        if ($result) {
            return $this->lastInsertId();
        }

        return false;
    }

    public function updateRating($idUser, $rating) {
        $query = "UPDATE users SET rating_pelanggan = :rating WHERE id_user = :id_user AND role = 'pelanggan'";
        return $this->execute($query, [
            ':rating' => $rating,
            ':id_user' => $idUser
        ]);
    }
}
?>
