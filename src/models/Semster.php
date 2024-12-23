<?php
require_once __DIR__ . '/../config/database.php';
    class Semster{
        private $conn;
        public function __construct() {
            $database = new Database();
            $this->conn = $database->getConnection();
        }
    
        public function fetchAll() {
            $query = "SELECT * FROM tbl_samester";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
    // Classroom.php (Model)
    
        public function create($data) {
            $samester_name = isset($data['samester_name']) ? $data['samester_name'] : null;
    
            if ($samester_name === null) {
                return false;
            }
    
            // Prepare the SQL query to insert a new classroom
            $query = "INSERT INTO tbl_samester (samester_name) VALUES (:samester_name)";
            $stmt = $this->conn->prepare($query);
    
            // Bind the class_name parameter
            $stmt->bindParam(':samester_name', $samester_name);
    
            // Execute the query
            return $stmt->execute();
        }
       
    
        public function updateSamester($id, $data) {
            $query = "UPDATE tbl_samester SET samester_name = :samster_name WHERE samester_id = :samester_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':samester_id', $id);
            $stmt->bindParam(':samester_name', $data['samester_name']);
            return $stmt->execute();
        }
        
        public function deleteSamester($id) {
            $query = "DELETE FROM tbl_samester WHERE samester_id = :samster_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':samester_id', $id);
            return $stmt->execute();
        }
        
        public function fetchSamesterById($id) {
            $query = "SELECT * FROM tbl_samester WHERE samester_id = :samester_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':samester_id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
