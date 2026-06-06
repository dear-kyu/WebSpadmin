<?php

require_once __DIR__ . '/BaseModel.php';

class Admin extends BaseModel {

    public function login($email, $password) {
        $query = "SELECT * 
                  FROM users 
                  WHERE email = :email 
                    AND role = 'admin' 
                  LIMIT 1";

        $admin = $this->fetchOne($query, [':email' => $email]);

        if ($admin && (password_verify($password, $admin['password']) || $password === $admin['password'])) {
            return $admin;
        }

        return false;
    }

    public function getById($idUser) {
        $query = "SELECT id_user, nama, email, no_telepon, role, created_at 
                  FROM users 
                  WHERE id_user = :id_user 
                    AND role = 'admin' 
                  LIMIT 1";

        return $this->fetchOne($query, [':id_user' => $idUser]);
    }
}
?>
