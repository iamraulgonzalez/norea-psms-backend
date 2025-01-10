<?php
require_once __DIR__ . '/../config/database.php';
    class MonthlyAttendance {
        private $conn;
        public function __construct() {
            $database = new Database();
            $this->conn = $database->getConnection();
        }
    
        public function fetchAll() {
            $query = "SELECT * FROM tbl_month_att";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
    // Classroom.php (Model)
    
        public function create($data) {
            $class_id = isset($data['class_id']) ? $data['class_id'] : null;
            $student_id = isset($data['student_id']) ? $data['student_id'] : null;
            $grade_id = isset($data['grade_id']) ? $data['grade_id'] : null;
            $monthly_id = isset($data['monthly_id']) ? $data['monthly_id'] : null;
            $Absent_asked_per = isset($data['Absent_asked_per']) ? $data['Absent_asked_per'] : null;
            $year_study_id = isset($data['year_study_id']) ? $data['year_study_id'] : null;
            $teacher_id = isset($data['teacher_id']) ? $data['teacher_id'] : null;
    
            if ($class_id === null || $student_id === null || $grade_id === null || $monthly_id === null || $Absent_asked_per === null || $year_study_id === null || $teacher_id === null) {
                return false;
            }
    
            // Prepare the SQL query to insert a new classroom
           $query = "INSERT INTO tbl_month_att (class_id, student_id, grade_id, monthly_id, Absent_asked_per, year_study_id, teacher_id) VALUES (:class_id, :student_id, :grade_id, :monthly_id, :Absent_asked_per, :year_study_id, :teacher_id)";
           $stmt = $this->conn->prepare($query);

           $stmt->bindParam(':class_id', $class_id);
           $stmt->bindParam(':student_id', $student_id);
           $stmt->bindParam(':grade_id', $grade_id);
           $stmt->bindParam(':monthly_id', $monthly_id);
           $stmt->bindParam(':Absent_asked_per', $Absent_asked_per);
           $stmt->bindParam(':year_study_id', $year_study_id);
           $stmt->bindParam(':teacher_id', $teacher_id);
    
            // Execute the query
            return $stmt->execute();
        }
    
        public function update($id, $data) {
            $query = "UPDATE tbl_month_att SET Absent_asked_per = :Absent_asked_per WHERE month_att_id = :month_att_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':month_att_id', $id);
            $stmt->bindParam(':Absent_asked_per', $data['Absent_asked_per']);
            return $stmt->execute();
        }

        public function delete($id) {
            $query = "DELETE FROM tbl_month_att WHERE month_att_id = :month_att_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':month_att_id', $id);
            return $stmt->execute();
        }
        
        public function fetchById($id) {
            $query = "SELECT * FROM tbl_month_att WHERE month_att_id = :month_att_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':month_att_id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
