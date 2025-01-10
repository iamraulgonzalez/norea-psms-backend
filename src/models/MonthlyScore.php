<?php
require_once __DIR__ . '/../config/database.php';
    class MonthlyScore {
        private $conn;
        public function __construct() {
            $database = new Database();
            $this->conn = $database->getConnection();
        }
    
        public function fetchAll() {
            $query = "SELECT * FROM tbl_monthly_score";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
    // Classroom.php (Model)
    
        public function create($data) {
            $teacher_id = isset($data['teacher_id']) ? $data['teacher_id'] : null;
            $score = isset($data['score']) ? $data['score'] : null;
            $monthly_id = isset($data['monthly_id']) ? $data['monthly_id'] : null;
            $grade_id = isset($data['grade_id']) ? $data['grade_id'] : null;
            $assign_monthsub_id = isset($data['assign_monthsub_id']) ? $data['assign_monthsub_id'] : null;
            $class_id = isset($data['class_id']) ? $data['class_id'] : null;
            $year_study_id = isset($data['year_study_id']) ? $data['year_study_id'] : null;
            $student_id = isset($data['student_id']) ? $data['student_id'] : null;
    
            if ($teacher_id === null || $score === null || $monthly_id === null || $grade_id === null || $assign_monthsub_id === null || $class_id === null || $year_study_id === null || $student_id === null) {
                return false;
            }
    
            // Prepare the SQL query to insert a new classroom
           $query = "INSERT INTO tbl_monthly_score (teacher_id, score, monthly_id, grade_id, assign_monthsub_id, class_id, year_study_id, student_id) VALUES (:teacher_id, :score, :monthly_id, :grade_id, :assign_monthsub_id, :class_id, :year_study_id, :student_id)";
           $stmt = $this->conn->prepare($query);

           $stmt->bindParam(':teacher_id', $teacher_id);
           $stmt->bindParam(':score', $score);
           $stmt->bindParam(':monthly_id', $monthly_id);
           $stmt->bindParam(':grade_id', $grade_id);
           $stmt->bindParam(':assign_monthsub_id', $assign_monthsub_id);
           $stmt->bindParam(':class_id', $class_id);
           $stmt->bindParam(':year_study_id', $year_study_id);
           $stmt->bindParam(':student_id', $student_id);
    
            // Execute the query
            return $stmt->execute();
        }
    
        public function update($id, $data) {
            $query = "UPDATE tbl_monthly_score SET score = :score WHERE monthly_id = :monthly_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':monthly_score_id', $id);
            $stmt->bindParam(':score', $data['score']);
            return $stmt->execute();
        }

        public function delete($id) {
            $query = "DELETE FROM tbl_monthly_score WHERE monthly_score_id = :monthly_score_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':monthly_score_id', $id);
            return $stmt->execute();
        }
        
        public function fetchById($id) {
            $query = "SELECT * FROM tbl_monthly_score WHERE monthly_score_id = :monthly_score_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':monthly_score_id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
