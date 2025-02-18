<?php
require_once __DIR__ . '/../config/database.php';
    class Grade{
        private $conn;
        public function __construct() {
            $database = new Database();
            $this->conn = $database->getConnection();
        }
    
        public function fetchAll() {
            try {
                $query = "SELECT 
                            grade_id,
                            grade_name,
                            level,
                            create_date 
                        FROM tbl_grade 
                        WHERE isDeleted = 0 
                        ORDER BY grade_id";
                        
                $stmt = $this->conn->prepare($query);
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                error_log("Database Error in fetchAll: " . $e->getMessage());
                throw $e;
            }
        }
    
        public function create($data) {
            $grade_name = isset($data['grade_name']) ? $data['grade_name'] : null;
    
            if ($grade_name === null) {
                return false;
            }
    
            $query = "INSERT INTO tbl_grade (grade_name) VALUES (:grade_name)";
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(':grade_name', $grade_name);
    
            return $stmt->execute();
        }
       
    
        public function update($id, $data) {
            $query = "UPDATE tbl_grade SET grade_name = :grade_name WHERE grade_id = :grade_id AND isDeleted = 0";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':grade_id', $id);
            $stmt->bindParam(':grade_name', $data['grade_name']);
            return $stmt->execute();
        }
        
        public function delete($id) {
            $query = "UPDATE tbl_grade SET isDeleted = 1 WHERE grade_id = :grade_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':grade_id', $id);
            return $stmt->execute();
        }
        
        public function fetchById($id) {
            $query = "SELECT * FROM tbl_grade WHERE grade_id = :grade_id AND isDeleted = 0";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':grade_id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        public function fetchByLevel($level) {
            try {
                $query = "SELECT 
                            grade_id,
                            grade_name,
                            level,
                            create_date 
                        FROM tbl_grade 
                        WHERE level = :level 
                        AND isDeleted = 0 
                        ORDER BY grade_id";
                        
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':level', $level);
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                error_log("Database Error in fetchByLevel: " . $e->getMessage());
                throw $e;
            }
        }
    }
