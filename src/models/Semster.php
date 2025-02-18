<?php
require_once __DIR__ . '/../config/database.php';
    class Semster{
        private $conn;
        public function __construct() {
            $database = new Database();
            $this->conn = $database->getConnection();
        }
    
        public function fetchAll() {
            $query = "SELECT * FROM tbl_semester WHERE isDeleted = 0";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    
        public function create($data) {
            $semester_name = isset($data['semester_name']) ? $data['semester_name'] : null;
    
            if ($semester_name === null) {
                return false;
            }
    
            $query = "INSERT INTO tbl_semester (semester_name) VALUES (:semester_name)";
            $stmt = $this->conn->prepare($query);
    
            $stmt->bindParam(':semester_name', $semester_name);
    
            return $stmt->execute();
        }
    
        public function update($id, $data) {
            $query = "UPDATE tbl_semester SET semester_name = :semester_name WHERE semester_id = :semester_id AND isDeleted = 0";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':semester_id', $id);
            $stmt->bindParam(':semester_name', $data['semester_name']);
            return $stmt->execute();
        }
        
        public function delete($id) {
            $query = "UPDATE tbl_semester SET isDeleted = 1 WHERE semester_id = :semster_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':semester_id', $id);
            return $stmt->execute();
        }
        
        public function fetchById($id) {
            $query = "SELECT * FROM tbl_semester WHERE semester_id = :semester_id AND isDeleted = 0";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':semester_id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
