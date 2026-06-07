<?php

require_once __DIR__ . '/BaseModel.php';

class Layanan extends BaseModel {

    public function getAll() {
        $query = "SELECT * 
                  FROM layanan 
                  ORDER BY id_layanan DESC";

        return $this->fetchAll($query);
    }

    public function getById($idLayanan) {
        $query = "SELECT * 
                  FROM layanan 
                  WHERE id_layanan = :id_layanan 
                  LIMIT 1";

        return $this->fetchOne($query, [':id_layanan' => $idLayanan]);
    }

    public function create($namaLayanan, $kategori, $durasi, $harga, $deskripsi, $media = null) {
        $query = "INSERT INTO layanan (nama_layanan, kategori, durasi, harga, deskripsi, media)
                  VALUES (:nama_layanan, :kategori, :durasi, :harga, :deskripsi, :media)";

        return $this->execute($query, [
            ':nama_layanan' => $namaLayanan,
            ':kategori'     => $kategori,
            ':durasi'       => $durasi,
            ':harga'        => $harga,
            ':deskripsi'    => $deskripsi,
            ':media'        => $media
        ]);
    }

    public function update($idLayanan, $namaLayanan, $kategori, $durasi, $harga, $deskripsi, $media = null) {
        $query = "UPDATE layanan
                  SET nama_layanan = :nama_layanan,
                      kategori     = :kategori,
                      durasi       = :durasi,
                      harga        = :harga,
                      deskripsi    = :deskripsi,
                      media        = :media
                  WHERE id_layanan = :id_layanan";

        return $this->execute($query, [
            ':id_layanan'   => $idLayanan,
            ':nama_layanan' => $namaLayanan,
            ':kategori'     => $kategori,
            ':durasi'       => $durasi,
            ':harga'        => $harga,
            ':deskripsi'    => $deskripsi,
            ':media'        => $media
        ]);
    }

    public function delete($idLayanan) {
        $query = "DELETE FROM layanan 
                  WHERE id_layanan = :id_layanan";

        return $this->execute($query, [':id_layanan' => $idLayanan]);
    }
}
?>
