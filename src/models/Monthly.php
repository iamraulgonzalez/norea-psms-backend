<?php
require_once __DIR__ . '/../config/database.php';
    class Monthly{
        private $conn;
        public function __construct() {
            $database = new Database();
            $this->conn = $database->getConnection();
        }
    
        public function fetchAll() {
            $query = "SELECT * FROM tbl_monthly WHERE isDeleted = 0";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    
        public function create($data) {
            $month_name = isset($data['month_name']) ? $data['month_name'] : null;
    
            if ($month_name === null) {
                return false;
            }
    
            $query = "INSERT INTO tbl_monthly (month_name) VALUES (:month_name)";
            $stmt = $this->conn->prepare($query);
    
            $stmt->bindParam(':month_name', $month_name);
    
            return $stmt->execute();
        }
       
    
        public function update($id, $data) {
            $query = "UPDATE tbl_monthly SET month_name = :month_name WHERE monthly_id = :monthly_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':monthly_id', $id);
            $stmt->bindParam(':month_name', $data['month_name']);
            return $stmt->execute();
        }

        public function delete($id) {
            $query = "DELETE FROM tbl_monthly WHERE monthly_id = :monthly_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':monthly_id', $id);
            return $stmt->execute();
        }
        
        public function fetchById($id) {
            $query = "SELECT * FROM tbl_monthly WHERE monthly_id = :monthly_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':monthly_id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
