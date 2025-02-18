<?php
require_once __DIR__ . '/../config/database.php';
class SchoolSession {
    private $conn;
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function fetchAll() {
        $query = "SELECT * FROM tbl_school_session WHERE isDeleted = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    public function fetchById($id) {
        $query = "SELECT * FROM tbl_school_session WHERE session_id = :session_id AND isDeleted = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':session_id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

}
