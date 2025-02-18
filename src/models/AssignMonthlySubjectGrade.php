<?php
    class AssignMonthlySubjectGrade {
        private $conn;
        public function __construct() {
            $database = new Database();
            $this->conn = $database->getConnection();
        }
        public function fetchAll() {
            $query = "SELECT * FROM tbl_assign_monthlysubject_grade WHERE isDeleted = 0 ORDER BY create_date DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        public function create($data) {
            $currentDate = date('Y-m-d H:i:s');
            $query = "INSERT INTO tbl_assign_monthlysubject_grade 
                      (grade_id, sub_code, create_date, isDeleted) 
                      VALUES (:grade_id, :sub_code, :create_date, 0)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':grade_id', $data['grade_id']);
            $stmt->bindParam(':sub_code', $data['sub_code']);
            $stmt->bindParam(':create_date', $currentDate);
            
            return $stmt->execute();
        }
       

        public function update($id, $data) {
            $query = "UPDATE tbl_assign_monthlysubject_grade SET grade_id = :grade_id, sub_code = :sub_code WHERE assign_monthsub_id = :assign_monthsub_id AND isDeleted = 0";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':grade_id', $id);
            $stmt->bindParam(':sub_code', $data['sub_code']);
            $stmt->bindParam(':assign_monthsub_id', $data['assign_monthsub_id']);
            return $stmt->execute();
        }
        
        public function delete($id) {
            $query = "UPDATE tbl_assign_monthlysubject_grade SET isDeleted = 1 
                      WHERE assign_monthsub_id = :assign_monthsub_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':assign_monthsub_id', $id);
            return $stmt->execute();
        }
        
        public function fetchById($id) {
            $query = "SELECT * FROM tbl_assign_monthlysubject_grade WHERE assign_monthsub_id = :assign_monthsub_id AND isDeleted = 0";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':assign_monthsub_id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
