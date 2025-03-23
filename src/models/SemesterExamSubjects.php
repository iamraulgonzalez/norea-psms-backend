<?php
require_once __DIR__ . '/../config/database.php';

class SemesterExamSubjects {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function fetchAll() {
        try {
            $query = "SELECT 
                ses.id,
                ses.class_id,
                c.class_name,
                ses.semester_id,
                sem.semester_name,
                ses.assign_subject_grade_id,
                s.subject_name,
                ses.create_date
            FROM tbl_semester_exam_subjects ses
            JOIN tbl_classroom c ON ses.class_id = c.class_id
            JOIN tbl_semester sem ON ses.semester_id = sem.semester_id
            JOIN tbl_assign_subject_grade asg ON ses.assign_subject_grade_id = asg.assign_subject_grade_id
            JOIN tbl_subject s ON asg.subject_code = s.subject_code
            WHERE ses.isDeleted = 0
            ORDER BY ses.class_id, ses.semester_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return [
                'status' => 'success',
                'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ];
        } catch (PDOException $e) {
            error_log("Error fetching semester exam subjects: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Failed to fetch semester exam subjects'
            ];
        }
    }

    public function getByClassAndSemester($class_id, $semester_id) {
        try {
            $query = "SELECT 
                ses.id,
                ses.class_id,
                c.class_name,
                ses.semester_id,
                sem.semester_name,
                ses.assign_subject_grade_id,
                asg.subject_code,
                s.subject_name,
                ses.create_date
            FROM tbl_semester_exam_subjects ses
            JOIN tbl_classroom c ON ses.class_id = c.class_id
            JOIN tbl_semester sem ON ses.semester_id = sem.semester_id
            JOIN tbl_assign_subject_grade asg ON ses.assign_subject_grade_id = asg.assign_subject_grade_id
            JOIN tbl_subject s ON asg.subject_code = s.subject_code
            WHERE ses.class_id = :class_id 
            AND ses.semester_id = :semester_id
            AND ses.isDeleted = 0
            ORDER BY s.subject_name";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':class_id', $class_id);
            $stmt->bindParam(':semester_id', $semester_id);
            $stmt->execute();
            
            return [
                'status' => 'success',
                'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ];
        } catch (PDOException $e) {
            error_log("Error fetching semester exam subjects: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Failed to fetch semester exam subjects'
            ];
        }
    }

    public function create($data) {
        try {
            if (!isset($data['class_id']) || !isset($data['semester_id']) || !isset($data['assign_subject_grade_id'])) {
                return [
                    'status' => 'error',
                    'message' => 'Missing required fields'
                ];
            }

            // Check if already exists
            $checkQuery = "SELECT COUNT(*) FROM tbl_semester_exam_subjects 
                         WHERE class_id = :class_id 
                         AND semester_id = :semester_id 
                         AND assign_subject_grade_id = :assign_subject_grade_id
                         AND isDeleted = 0";
                         
            $stmt = $this->conn->prepare($checkQuery);
            $stmt->bindParam(':class_id', $data['class_id']);
            $stmt->bindParam(':semester_id', $data['semester_id']);
            $stmt->bindParam(':assign_subject_grade_id', $data['assign_subject_grade_id']);
            $stmt->execute();
            
            if ($stmt->fetchColumn() > 0) {
                return [
                    'status' => 'error',
                    'message' => 'This subject is already assigned for this class and semester'
                ];
            }

            $query = "INSERT INTO tbl_semester_exam_subjects 
                    (class_id, semester_id, assign_subject_grade_id) 
                    VALUES (:class_id, :semester_id, :assign_subject_grade_id)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':class_id', $data['class_id']);
            $stmt->bindParam(':semester_id', $data['semester_id']);
            $stmt->bindParam(':assign_subject_grade_id', $data['assign_subject_grade_id']);
            
            if ($stmt->execute()) {
                return [
                    'status' => 'success',
                    'message' => 'Semester exam subject assigned successfully',
                    'id' => $this->conn->lastInsertId()
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Failed to assign semester exam subject'
                ];
            }
        } catch (PDOException $e) {
            error_log("Error creating semester exam subject: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Database error occurred'
            ];
        }
    }

    public function delete($id) {
        try {
            $query = "UPDATE tbl_semester_exam_subjects SET isDeleted = 1 WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            
            if ($stmt->execute()) {
                return [
                    'status' => 'success',
                    'message' => 'Semester exam subject removed successfully'
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Failed to remove semester exam subject'
                ];
            }
        } catch (PDOException $e) {
            error_log("Error deleting semester exam subject: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Database error occurred'
            ];
        }
    }

    public function getById($id) {
        try {
            $query = "SELECT 
                ses.id,
                ses.class_id,
                c.class_name,
                ses.semester_id,
                sem.semester_name,
                ses.assign_subject_grade_id,
                asg.subject_code,
                s.subject_name,
                ses.create_date
            FROM tbl_semester_exam_subjects ses
            JOIN tbl_classroom c ON ses.class_id = c.class_id
            JOIN tbl_semester sem ON ses.semester_id = sem.semester_id
            JOIN tbl_assign_subject_grade asg ON ses.assign_subject_grade_id = asg.assign_subject_grade_id
            JOIN tbl_subject s ON asg.subject_code = s.subject_code
            WHERE ses.id = :id 
            AND ses.isDeleted = 0";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                return [
                    'status' => 'success',
                    'data' => $result
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Semester exam subject not found'
                ];
            }
        } catch (PDOException $e) {
            error_log("Error fetching semester exam subject: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Failed to fetch semester exam subject'
            ];
        }
    }

    public function update($id, $data) {
        try {
            if (!isset($data['class_id']) || !isset($data['semester_id']) || !isset($data['assign_subject_grade_id'])) {
                return [
                    'status' => 'error',
                    'message' => 'Missing required fields'
                ];
            }

            // Check if already exists (excluding the current record)
            $checkQuery = "SELECT COUNT(*) FROM tbl_semester_exam_subjects 
                         WHERE class_id = :class_id 
                         AND semester_id = :semester_id 
                         AND assign_subject_grade_id = :assign_subject_grade_id
                         AND id != :id
                         AND isDeleted = 0";
                         
            $stmt = $this->conn->prepare($checkQuery);
            $stmt->bindParam(':class_id', $data['class_id']);
            $stmt->bindParam(':semester_id', $data['semester_id']);
            $stmt->bindParam(':assign_subject_grade_id', $data['assign_subject_grade_id']);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            if ($stmt->fetchColumn() > 0) {
                return [
                    'status' => 'error',
                    'message' => 'This subject is already assigned for this class and semester'
                ];
            }

            $query = "UPDATE tbl_semester_exam_subjects SET 
                    class_id = :class_id, 
                    semester_id = :semester_id, 
                    assign_subject_grade_id = :assign_subject_grade_id
                    WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':class_id', $data['class_id']);
            $stmt->bindParam(':semester_id', $data['semester_id']);
            $stmt->bindParam(':assign_subject_grade_id', $data['assign_subject_grade_id']);
            $stmt->bindParam(':id', $id);
            
            if ($stmt->execute()) {
                return [
                    'status' => 'success',
                    'message' => 'Semester exam subject updated successfully'
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Failed to update semester exam subject'
                ];
            }
        } catch (PDOException $e) {
            error_log("Error updating semester exam subject: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Database error occurred'
            ];
        }
    }
}
