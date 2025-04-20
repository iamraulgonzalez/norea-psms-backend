<?php
require_once dirname(__DIR__) . '/config/database.php';

class StudyModel {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    public function getAllStudies() {
        $query = "SELECT s.*, si.student_name, c.class_name, y.year_study 
                  FROM tbl_study s
                  JOIN tbl_student_info si ON s.student_id = si.student_id
                  JOIN tbl_classroom c ON s.class_id = c.class_id
                  JOIN tbl_year_study y ON s.year_study_id = y.year_study_id
                  WHERE s.isDeleted = 0";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }
    
    public function getStudiesByStudentId($studentId) {
        $query = "SELECT s.*, si.student_name, c.class_name, y.year_study 
                  FROM tbl_study s
                  JOIN tbl_student_info si ON s.student_id = si.student_id
                  JOIN tbl_classroom c ON s.class_id = c.class_id
                  JOIN tbl_year_study y ON s.year_study_id = y.year_study_id
                  WHERE s.student_id = ? AND s.isDeleted = 0";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $studentId);
        $stmt->execute();
        
        return $stmt;
    }
    
    public function getStudiesByClassId($classId) {
        $query = "SELECT s.*, si.student_name, dob,c.class_name, y.year_study
                  FROM tbl_study s
                  JOIN tbl_student_info si ON s.student_id = si.student_id
                  JOIN tbl_classroom c ON s.class_id = c.class_id
                  JOIN tbl_year_study y ON s.year_study_id = y.year_study_id
                  WHERE s.class_id = ? AND s.isDeleted = 0";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $classId);
        $stmt->execute();
        
        return $stmt;
    }
    
    public function getStudiesByYearId($yearId) {
        $query = "SELECT s.*, si.student_name, c.class_name, y.year_study 
                  FROM tbl_study s
                  JOIN tbl_student_info si ON s.student_id = si.student_id
                  JOIN tbl_classroom c ON s.class_id = c.class_id
                  JOIN tbl_year_study y ON s.year_study_id = y.year_study_id
                  WHERE s.year_study_id = ? AND s.isDeleted = 0";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $yearId);
        $stmt->execute();
        
        return $stmt;
    }
    
    public function addStudy($data) {
        // First check if student exists
        $checkQuery = "SELECT student_id FROM tbl_student_info WHERE student_id = ? AND isDeleted = 0";
        $checkStmt = $this->conn->prepare($checkQuery);
        $checkStmt->bindParam(1, $data['student_id']);
        $checkStmt->execute();
        
        if (!$checkStmt->fetch(PDO::FETCH_ASSOC)) {
            throw new Exception("Student not found");
        }
        
        $query = "INSERT INTO tbl_study 
                  (student_id, class_id, year_study_id, enrollment_date, status) 
                  VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        
        // Set default enrollment date to today if not provided
        $enrollmentDate = !empty($data['enrollment_date']) ? $data['enrollment_date'] : date('Y-m-d');
        $status = !empty($data['status']) ? $data['status'] : 'active';
        
        $stmt->bindParam(1, $data['student_id']);
        $stmt->bindParam(2, $data['class_id']);
        $stmt->bindParam(3, $data['year_study_id']);
        $stmt->bindParam(4, $enrollmentDate);
        $stmt->bindParam(5, $status);
        
        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        
        return false;
    }
    
    public function updateStudy($id, $data) {
        $query = "UPDATE tbl_study 
                  SET student_id = ?, class_id = ?, year_study_id = ?, 
                      enrollment_date = ?, status = ? 
                  WHERE study_id = ?";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(1, $data['student_id']);
        $stmt->bindParam(2, $data['class_id']);
        $stmt->bindParam(3, $data['year_study_id']);
        $stmt->bindParam(4, $data['enrollment_date']);
        $stmt->bindParam(5, $data['status']);
        $stmt->bindParam(6, $id);
        
        return $stmt->execute();
    }
    
    public function deleteStudy($id) {
        $query = "UPDATE tbl_study SET isDeleted = 1 WHERE study_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        
        return $stmt->execute();
    }
    
    public function getStudyById($id) {
        $query = "SELECT s.*, si.student_name, c.class_name, y.year_study 
                  FROM tbl_study s
                  JOIN tbl_student_info si ON s.student_id = si.student_id
                  JOIN tbl_classroom c ON s.class_id = c.class_id
                  JOIN tbl_year_study y ON s.year_study_id = y.year_study_id
                  WHERE s.study_id = ? AND s.isDeleted = 0";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        
        return $stmt;
    }
    
    public function getCurrentClassForStudent($studentId) {
        $query = "SELECT s.*, c.class_name, c.grade_id, g.grade_name
                  FROM tbl_study s
                  JOIN tbl_classroom c ON s.class_id = c.class_id
                  JOIN tbl_grade g ON c.grade_id = g.grade_id
                  WHERE s.student_id = ? AND s.status = 'active' AND s.isDeleted = 0
                  ORDER BY s.create_date DESC LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $studentId);
        $stmt->execute();
        
        return $stmt;
    }
    
    public function getStudiesByClassAndYear($classId, $yearStudyId, $status = null) {
        $query = "SELECT s.*, si.student_name 
                  FROM tbl_study s
                  JOIN tbl_student_info si ON s.student_id = si.student_id
                  WHERE s.class_id = ? AND s.year_study_id = ? AND s.isDeleted = 0";
        
        if ($status) {
            $query .= " AND s.status = ?";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $classId);
        $stmt->bindParam(2, $yearStudyId);
        
        if ($status) {
            $stmt->bindParam(3, $status);
        }
        
        $stmt->execute();
        
        return $stmt;
    }
    
    public function promoteStudent($studentId, $currentClassId, $newClassId, $yearStudyId) {
        try {
            // Begin transaction
            $this->conn->beginTransaction();
            
            // First check if student is in the current class
            $checkStmt = $this->conn->prepare(
                "SELECT study_id FROM tbl_study 
                 WHERE student_id = ? AND class_id = ? AND year_study_id = ? 
                 AND status = 'active' AND isDeleted = 0"
            );
            $checkStmt->bindParam(1, $studentId);
            $checkStmt->bindParam(2, $currentClassId);
            $checkStmt->bindParam(3, $yearStudyId);
            $checkStmt->execute();
            
            $currentStudy = $checkStmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$currentStudy) {
                // If student is not in current class, no need to update anything
                $this->conn->rollBack();
                return false;
            }
            
            // Check if student is already in the target class
            $targetCheckStmt = $this->conn->prepare(
                "SELECT study_id FROM tbl_study 
                 WHERE student_id = ? AND class_id = ? AND year_study_id = ? 
                 AND status = 'active' AND isDeleted = 0"
            );
            $targetCheckStmt->bindParam(1, $studentId);
            $targetCheckStmt->bindParam(2, $newClassId);
            $targetCheckStmt->bindParam(3, $yearStudyId);
            $targetCheckStmt->execute();
            
            if ($targetCheckStmt->fetch(PDO::FETCH_ASSOC)) {
                // Student is already in target class
                $this->conn->rollBack();
                return false;
            }
            
            // Update current study to inactive
            $updateStmt = $this->conn->prepare(
                "UPDATE tbl_study SET status = 'inactive' WHERE study_id = ?"
            );
            $updateStmt->bindParam(1, $currentStudy['study_id']);
            $updateSuccess = $updateStmt->execute();
            
            if (!$updateSuccess) {
                $this->conn->rollBack();
                return false;
            }
            
            // Create new study record
            $currentUser = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;
            $insertStmt = $this->conn->prepare(
                "INSERT INTO tbl_study 
                 (student_id, class_id, year_study_id, enrollment_date, status, create_date, create_by)
                 VALUES (?, ?, ?, CURRENT_DATE(), 'active', CURRENT_TIMESTAMP(), ?)"
            );
            $insertStmt->bindParam(1, $studentId);
            $insertStmt->bindParam(2, $newClassId);
            $insertStmt->bindParam(3, $yearStudyId);
            $insertStmt->bindParam(4, $currentUser);
            $insertSuccess = $insertStmt->execute();
            
            if (!$insertSuccess) {
                $this->conn->rollBack();
                return false;
            }
            
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            error_log("Model Error - promoteStudent: " . $e->getMessage());
            return false;
        }
    }
    
    public function getCurrentEnrollment($classId) {
        $query = "SELECT s.*, si.student_name, si.gender, si.dob, si.father_name, si.mother_name, si.pob_address,
                  c.class_name, c.grade_id, y.year_study 
                  FROM tbl_study s
                  JOIN tbl_student_info si ON s.student_id = si.student_id
                  JOIN tbl_classroom c ON s.class_id = c.class_id
                  JOIN tbl_year_study y ON s.year_study_id = y.year_study_id
                  WHERE s.class_id = :class_id 
                  AND s.status = 'active' 
                  AND s.isDeleted = 0
                  ORDER BY si.student_name";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':class_id', $classId);
        $stmt->execute();
        
        return $stmt;
    }
    
    public function addMultipleStudies($data) {
        try {
            // Begin transaction
            $this->conn->beginTransaction();
            
            $successCount = 0;
            $failedStudents = [];
            
            foreach ($data['student_ids'] as $studentId) {
                try {
                    // Check if student exists
                    $checkQuery = "SELECT student_id FROM tbl_student_info WHERE student_id = ? AND isDeleted = 0";
                    $checkStmt = $this->conn->prepare($checkQuery);
                    $checkStmt->bindParam(1, $studentId);
                    $checkStmt->execute();
                    
                    if (!$checkStmt->fetch(PDO::FETCH_ASSOC)) {
                        $failedStudents[] = $studentId;
                        continue;
                    }
                    
                    // Insert study record
                    $query = "INSERT INTO tbl_study 
                              (student_id, class_id, year_study_id, enrollment_date, status) 
                              VALUES (?, ?, ?, ?, ?)";
                    
                    $stmt = $this->conn->prepare($query);
                    
                    $enrollmentDate = !empty($data['enrollment_date']) ? $data['enrollment_date'] : date('Y-m-d');
                    $status = !empty($data['status']) ? $data['status'] : 'active';
                    
                    $stmt->bindParam(1, $studentId);
                    $stmt->bindParam(2, $data['class_id']);
                    $stmt->bindParam(3, $data['year_study_id']);
                    $stmt->bindParam(4, $enrollmentDate);
                    $stmt->bindParam(5, $status);
                    
                    if ($stmt->execute()) {
                        $successCount++;
                    } else {
                        $failedStudents[] = $studentId;
                    }
                } catch (Exception $e) {
                    $failedStudents[] = $studentId;
                    error_log("Error enrolling student $studentId: " . $e->getMessage());
                }
            }
            
            if ($successCount > 0) {
                $this->conn->commit();
                return [
                    'success' => true,
                    'success_count' => $successCount,
                    'failed_students' => $failedStudents,
                    'message' => 'បានចុះឈ្មោះសិស្សដោយជោគជ័យ'
                ];
            } else {
                $this->conn->rollBack();
                return [
                    'success' => false,
                    'message' => 'មិនអាចចុះឈ្មោះសិស្សបានទេ'
                ];
            }
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Error in addMultipleStudies: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'មានបញ្ហាក្នុងការចុះឈ្មោះសិស្ស'
            ];
        }
    }
}
