<?php

date_default_timezone_set('Asia/Jakarta');

define('DB_HOST', 'localhost');
define('DB_NAME', 'spadmin_rpl');
define('DB_USER', 'root');
define('DB_PASS', '');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, '', 3306);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
if (!$conn->query("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "`")) {
    die("Gagal membuat database: " . $conn->error);
}
if (!$conn->select_db(DB_NAME)) {
    die("Koneksi ke database gagal: " . $conn->error);
}
$conn->query("SET time_zone = '+07:00'");

class Database {
    private static $pdo = null;

    public function getConnection() {
        if (self::$pdo === null) {
            try {
                self::$pdo = new PDO(
                    "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                    DB_USER,
                    DB_PASS
                );
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                self::$pdo->exec("SET time_zone = '+07:00'");
            } catch (PDOException $e) {
                die("Database connection error: " . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}
?>
