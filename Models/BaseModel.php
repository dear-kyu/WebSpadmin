<?php

require_once __DIR__ . '/../config/database.php';

class BaseModel {
    protected $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    protected function fetchAll($query, $params = []) {
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as &$row) {
            $row = $this->normalizeRowKeys($row);
        }
        return $rows;
    }

    protected function fetchOne($query, $params = []) {
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $row = $this->normalizeRowKeys($row);
        }
        return $row;
    }

    protected function execute($query, $params = []) {
        $stmt = $this->db->prepare($query);
        return $stmt->execute($params);
    }

    protected function lastInsertId() {
        return $this->db->lastInsertId();
    }

    private function normalizeRowKeys($row) {
        if (!is_array($row)) return $row;
        $newRow = $row;
        foreach ($row as $key => $value) {
            // camelCase to snake_case
            $snake = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $key));
            if ($snake !== $key && !array_key_exists($snake, $newRow)) {
                $newRow[$snake] = $value;
            }
            // snake_case to camelCase
            $camel = lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $key))));
            if ($camel !== $key && !array_key_exists($camel, $newRow)) {
                $newRow[$camel] = $value;
            }
        }
        return $newRow;
    }
}
?>
