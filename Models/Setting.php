<?php

require_once __DIR__ . '/BaseModel.php';

class Setting extends BaseModel {

    public function getAll() {
        $query = "SELECT * FROM pengaturan_halaman";
        $rows = $this->fetchAll($query);
        $settings = [];
        foreach ($rows as $row) {
            $settings[$row['kunci']] = $row['nilai'];
        }
        return $settings;
    }

    public function getByKey($key) {
        $query = "SELECT nilai FROM pengaturan_halaman WHERE kunci = :kunci LIMIT 1";
        $row = $this->fetchOne($query, [':kunci' => $key]);
        return $row ? $row['nilai'] : null;
    }

    public function save($key, $value) {
        $query = "INSERT INTO pengaturan_halaman (kunci, nilai) 
                  VALUES (:kunci, :nilai) 
                  ON DUPLICATE KEY UPDATE nilai = :nilai_update";
        return $this->execute($query, [
            ':kunci' => $key,
            ':nilai' => $value,
            ':nilai_update' => $value
        ]);
    }
}
?>
