<?php
require_once __DIR__ . '/../config/database.php';
    class Monthly{
        private $conn;
        public function __construct() {
            $database = new Database();
            $this->conn = $database->getConnection();
        }
    
        public function fetchAll() {
            $query = "SELECT * FROM tbl_monthly";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
    // Classroom.php (Model)
    
        public function create($data) {
            $month_name = isset($data['month_name']) ? $data['month_name'] : null;
    
            if ($month_name === null) {
                return false;
            }
    
            // Prepare the SQL query to insert a new classroom
            $query = "INSERT INTO tbl_monthly (month_name) VALUES (:month_name)";
            $stmt = $this->conn->prepare($query);
    
            // Bind the class_name parameter
            $stmt->bindParam(':month_name', $month_name);
    
            // Execute the query
            return $stmt->execute();
        }
       
    
        public function updateMonthly($id, $data) {
            $query = "UPDATE tbl_monthly SET month_name = :month_name WHERE monthly_id = :monthly_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':monthly_id', $id);
            $stmt->bindParam(':month_name', $data['month_name']);
            return $stmt->execute();
        }

        public function deleteMonthly($id) {
            $query = "DELETE FROM tbl_monthly WHERE monthly_id = :monthly_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':monthly_id', $id);
            return $stmt->execute();
        }
        
        public function fetchMonthById($id) {
            $query = "SELECT * FROM tbl_monthly WHERE monthly_id = :monthly_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':monthly_id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
