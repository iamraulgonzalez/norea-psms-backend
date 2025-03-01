<?php
require_once __DIR__ . '/../config/database.php';
    class StudentSemesterScore {
        private $conn;
        public function __construct() {
            $database = new Database();
            $this->conn = $database->getConnection();
        }
    
        public function fetchAll() {
            $query = "SELECT * FROM tbl_semester_score WHERE isDeleted = 0";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    
        public function create($data) {
            $semester_id = isset($data['semester_id']) ? $data['semester_id'] : null;
            $year_study_id = isset($data['year_study_id']) ? $data['year_study_id'] : null;
            $score = isset($data['score']) ? $data['score'] : null;
            $class_id = isset($data['class_id']) ? $data['class_id'] : null;
            $student_id = isset($data['student_id']) ? $data['student_id'] : null;
            $grade_id = isset($data['grade_id']) ? $data['grade_id'] : null;
            $assign_semestersub_id = isset($data['assign_semestersub_id']) ? $data['assign_semestersub_id'] : null;
            $teacher_id = isset($data['teacher_id']) ? $data['teacher_id'] : null;
    
            if ($semester_id === null || $year_study_id === null || $score === null || $class_id === null || $student_id === null || $grade_id === null || $assign_semestersub_id === null || $teacher_id === null) {
                return false;
            }
    
            $query = "INSERT INTO tbl_semester_score (semester_id, year_study_id, score, class_id, student_id, grade_id, assign_semestersub_id, teacher_id) VALUES (:semester_id, :year_study_id, :score, :class_id, :student_id, :grade_id, :assign_semestersub_id, :teacher_id)";
            $stmt = $this->conn->prepare($query);
    
            $stmt->bindParam(':semester_id', $semester_id);
            $stmt->bindParam(':year_study_id', $year_study_id);
            $stmt->bindParam(':score', $score);
            $stmt->bindParam(':class_id', $class_id);
            $stmt->bindParam(':student_id', $student_id);
            $stmt->bindParam(':grade_id', $grade_id);
            $stmt->bindParam(':assign_semestersub_id', $assign_semestersub_id);
            $stmt->bindParam(':teacher_id', $teacher_id);
    
            return $stmt->execute();
        }
       
    
        public function update($id, $data) {
            $query = "UPDATE tbl_semester_score SET score = :score WHERE semester_score_id = :semester_score_id AND isDeleted = 0";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':semester_score_id', $id);
            $stmt->bindParam(':score', $data['score']);
            return $stmt->execute();
        }
        
        public function delete($id) {
            $query = "UPDATE tbl_semester_score SET isDeleted = 1 WHERE semester_score_id = :semester_score_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':semester_score_id', $id);
            return $stmt->execute();
        }
        

        public function fetchById($id) {
            $query = "SELECT * FROM tbl_semester_score WHERE semester_score_id = :semester_score_id AND isDeleted = 0";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':semester_score_id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
