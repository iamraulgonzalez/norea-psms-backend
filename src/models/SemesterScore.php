<?php
require_once __DIR__ . '/../config/database.php';
    class SemsterScore {
        private $conn;
        public function __construct() {
            $database = new Database();
            $this->conn = $database->getConnection();
        }
    
        public function fetchAll() {
            $query = "SELECT * FROM tbl_samester_score";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
    // Classroom.php (Model)
    
        public function create($data) {
            $samester_id = isset($data['samester_id']) ? $data['samester_id'] : null;
            $year_study_id = isset($data['year_study_id']) ? $data['year_study_id'] : null;
            $score = isset($data['score']) ? $data['score'] : null;
            $class_id = isset($data['class_id']) ? $data['class_id'] : null;
            $student_id = isset($data['student_id']) ? $data['student_id'] : null;
            $grade_id = isset($data['grade_id']) ? $data['grade_id'] : null;
            $assign_samestersub_id = isset($data['assign_samestersub_id']) ? $data['assign_samestersub_id'] : null;
            $teacher_id = isset($data['teacher_id']) ? $data['teacher_id'] : null;
    
            if ($samester_id === null || $year_study_id === null || $score === null || $class_id === null || $student_id === null || $grade_id === null || $assign_samestersub_id === null || $teacher_id === null) {
                return false;
            }
    
            // Prepare the SQL query to insert a new classroom
            $query = "INSERT INTO tbl_samester_score (samester_id, year_study_id, score, class_id, student_id, grade_id, assign_samestersub_id, teacher_id) VALUES (:samester_id, :year_study_id, :score, :class_id, :student_id, :grade_id, :assign_samestersub_id, :teacher_id)";
            $stmt = $this->conn->prepare($query);
    
            // Bind the class_name parameter
            $stmt->bindParam(':samester_id', $samester_id);
            $stmt->bindParam(':year_study_id', $year_study_id);
            $stmt->bindParam(':score', $score);
            $stmt->bindParam(':class_id', $class_id);
            $stmt->bindParam(':student_id', $student_id);
            $stmt->bindParam(':grade_id', $grade_id);
            $stmt->bindParam(':assign_samestersub_id', $assign_samestersub_id);
            $stmt->bindParam(':teacher_id', $teacher_id);
    
            // Execute the query
            return $stmt->execute();
        }
       
    
        public function update($id, $data) {
            $query = "UPDATE tbl_samester_score SET score = :score WHERE samester_score_id = :samester_score_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':samester_score_id', $id);
            $stmt->bindParam(':score', $data['score']);
            return $stmt->execute();
        }
        
        public function delete($id) {
            $query = "DELETE FROM tbl_samester_score WHERE samester_score_id = :samester_score_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':samester_score_id', $id);
            return $stmt->execute();
        }
        

        public function fetchById($id) {
            $query = "SELECT * FROM tbl_samester_score WHERE samester_score_id = :samester_score_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':samester_score_id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
