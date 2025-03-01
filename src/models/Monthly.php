<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../utils/response.php';

class Monthly {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();  // Get the PDO connection
    }

    public function fetchAll() {
        $query = "SELECT * FROM tbl_monthly WHERE isDeleted = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $query = "INSERT INTO tbl_monthly (monthly_name) VALUES (:monthly_name)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':monthly_name', $data['monthly_name']);
        $stmt->execute();
    }

    public function update($id, $data) {
        $query = "UPDATE tbl_monthly SET monthly_name = :monthly_name WHERE monthly_id = :monthly_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':monthly_name', $data['monthly_name']);
        $stmt->bindParam(':monthly_id', $id);
        $stmt->execute();
    }

    public function delete($id) {
        $query = "UPDATE tbl_monthly SET isDeleted = 1 WHERE monthly_id = :monthly_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':monthly_id', $id);
        $stmt->execute();
    }

    public function fetchById($id) {
        $query = "SELECT * FROM tbl_monthly WHERE monthly_id = :monthly_id AND isDeleted = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':monthly_id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

