<?php

    class AssignSemesterSubjectGrade{
        private $conn;
        public function __construct() {
            $database = new Database();
            $this->conn = $database->getConnection();
        }
        public function fetchAll() {
            $query = "SELECT * FROM tbl_assign_samestersubject_grade";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        public function create($data) {
            $grade_id = isset($data['grade_id']) ? $data['grade_id'] : null;
            $sub_code = isset($data['sub_code']) ? $data['sub_code'] : null;

            if ($grade_id === null || $sub_code === null) {
                return false;
            }
    
            $query = "INSERT INTO tbl_assign_samestersubject_grade (grade_id, sub_code) VALUES (:grade_id, :sub_code)";
            $stmt = $this->conn->prepare($query);
    
            $stmt->bindParam(':grade_id', $grade_id);
            $stmt->bindParam(':sub_code', $sub_code);
    
            // Execute the query
            return $stmt->execute();
        }
       

        public function update($id, $data) {
            $query = "UPDATE tbl_assign_samestersubject_grade SET grade_id = :grade_id, sub_code = :sub_code WHERE assign_semstersub_id = :assign_semstersub_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':grade_id', $id);
            $stmt->bindParam(':sub_code', $data['sub_code']);
            $stmt->bindParam(':assign_monthsub_id', $data['assign_monthsub_id']);
            return $stmt->execute();
        }
        
        public function delete($id) {
            $query = "DELETE FROM tbl_assign_monthlysubject_grade WHERE assign_monthsub_id = :assign_monthsub_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':assign_monthsub_id', $id);
            return $stmt->execute();
        }
        
        public function fetchById($id) {
            $query = "SELECT * FROM tbl_assign_monthlysubject_grade WHERE assign_monthsub_id = :assign_monthsub_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':assign_monthsub_id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
    