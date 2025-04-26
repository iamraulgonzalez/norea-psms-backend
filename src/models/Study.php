<?php
require_once dirname(__DIR__) . '/config/database.php';

class StudyModel {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    public function getAllStudies() {
        $query = "SELECT s.*, si.student_name,si.status as student_status, c.class_name, y.year_study 
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
        $query = "SELECT s.*, si.student_name, si.status as student_status, c.class_name, y.year_study 
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
        $query = "SELECT s.*, si.student_name, si.status as student_status, dob,c.class_name, y.year_study
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
        $query = "SELECT s.*, si.student_name, si.status as student_status, c.class_name, y.year_study 
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
            throw new Exception("សិស្សមិនត្រូវបានរកឃើញ");
        }

        // Check class limit
        $classLimitQuery = "SELECT c.num_students_in_class, c.class_name,
                           (SELECT COUNT(*) FROM tbl_study s 
                            WHERE s.class_id = c.class_id AND s.isDeleted = 0 AND s.status = 'active') as current_students
                           FROM tbl_classroom c 
                           WHERE c.class_id = ?";
        $classLimitStmt = $this->conn->prepare($classLimitQuery);
        $classLimitStmt->bindParam(1, $data['class_id']);
        $classLimitStmt->execute();
        $classInfo = $classLimitStmt->fetch(PDO::FETCH_ASSOC);

        if (!$classInfo) {
            throw new Exception("ថ្នាក់មិនត្រូវបានរកឃើញ");
        }

        if ($classInfo['current_students'] >= $classInfo['num_students_in_class']) {
            throw new Exception("ថ្នាក់ " . $classInfo['class_name'] . " មានសិស្សពេញហើយ។ ចំនួនសិស្សអតិបរមា: " . $classInfo['num_students_in_class'] . " នាក់");
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
        $query = "SELECT s.*, si.student_name, si.status as student_status, c.class_name, y.year_study 
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
        $query = "SELECT s.*, si.student_name, si.status as student_status
                  FROM tbl_study s
                  JOIN tbl_student_info si ON s.student_id = si.student_id
                  WHERE s.class_id = ? AND s.year_study_id = ? AND s.isDeleted = 0";
        
        if ($status) {
            $query .= " AND LOWER(s.status) = LOWER(?)";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $classId);
        $stmt->bindParam(2, $yearStudyId);
        
        if ($status) {
            $stmt->bindParam(3, $status);
        }
        
        $stmt->execute();
        
        // Debug information
        error_log("Query: " . $query);
        error_log("Parameters: classId=$classId, yearStudyId=$yearStudyId, status=$status");
        error_log("Number of rows: " . $stmt->rowCount());
        
        // Log the actual data found
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("Found students: " . json_encode($results));
        
        // Reset the statement for reuse
        $stmt->closeCursor();
        
        // Create a new statement with the same query
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
            
            // Check class limit first
            $classLimitQuery = "SELECT c.num_students_in_class, c.class_name,
                               (SELECT COUNT(*) FROM tbl_study s 
                                WHERE s.class_id = c.class_id AND s.isDeleted = 0 AND s.status = 'active') as current_students
                               FROM tbl_classroom c 
                               WHERE c.class_id = ?";
            $classLimitStmt = $this->conn->prepare($classLimitQuery);
            $classLimitStmt->bindParam(1, $data['class_id']);
            $classLimitStmt->execute();
            $classInfo = $classLimitStmt->fetch(PDO::FETCH_ASSOC);

            if (!$classInfo) {
                throw new Exception("ថ្នាក់មិនត្រូវបានរកឃើញ");
            }

            $availableSlots = $classInfo['num_students_in_class'] - $classInfo['current_students'];
            if (count($data['student_ids']) > $availableSlots) {
                throw new Exception("ថ្នាក់ " . $classInfo['class_name'] . " សិស្សពេញហើយ");
            }
            
            $successCount = 0;
            $failedStudents = [];
            
            foreach ($data['student_ids'] as $studentId) {
                try {
                    // Check if student exists
                    $checkQuery = "SELECT student_id, student_name FROM tbl_student_info WHERE student_id = ? AND isDeleted = 0";
                    $checkStmt = $this->conn->prepare($checkQuery);
                    $checkStmt->bindParam(1, $studentId);
                    $checkStmt->execute();
                    $studentInfo = $checkStmt->fetch(PDO::FETCH_ASSOC);
                    
                    if (!$studentInfo) {
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
                        $failedStudents[] = $studentInfo['student_name'];
                    }
                } catch (Exception $e) {
                    $failedStudents[] = $studentInfo['student_name'];
                    error_log("Error enrolling student $studentId: " . $e->getMessage());
                }
            }
            
            // Commit transaction if all operations were successful
            $this->conn->commit();
            
            $message = "បានចុះឈ្មោះសិស្សដោយជោគជ័យ " . $successCount . " នាក់";
            if (!empty($failedStudents)) {
                $message .= "។ មិនអាចចុះឈ្មោះសិស្ស: " . implode(", ", $failedStudents);
            }
            
            return [
                'success_count' => $successCount,
                'failed_students' => $failedStudents,
                'message' => $message
            ];
            
        } catch (Exception $e) {
            // Rollback transaction on error
            $this->conn->rollBack();
            throw $e;
        }
    }


    //get student by grade id
    public function fetchStudentByGradeId($grade_id) {
        try {
            // First get total count
            $countStmt = $this->conn->prepare("
                SELECT COUNT(*) 
                FROM tbl_study s
                JOIN tbl_student_info si ON s.student_id = si.student_id
                JOIN tbl_classroom c ON s.class_id = c.class_id
                WHERE c.grade_id = :grade_id 
                AND s.isDeleted = 0
                AND s.status = 'active'
                AND si.status = 'active'
            ");
            
            $countStmt->bindParam(':grade_id', $grade_id);
            $countStmt->execute();
            $totalStudents = $countStmt->fetchColumn();

            // Then get student details
            $stmt = $this->conn->prepare("
                SELECT 
                    s.*,
                    si.student_id,
                    si.student_name,
                    si.gender,
                    si.dob,
                    si.status as student_status,
                    c.class_name,
                    c.grade_id,
                    g.grade_name,
                    y.year_study
                FROM tbl_study s
                JOIN tbl_student_info si ON s.student_id = si.student_id
                JOIN tbl_classroom c ON s.class_id = c.class_id
                JOIN tbl_grade g ON c.grade_id = g.grade_id
                JOIN tbl_year_study y ON s.year_study_id = y.year_study_id
                WHERE c.grade_id = :grade_id 
                AND s.isDeleted = 0
                AND s.status = 'active'
                AND si.status = 'active'
                ORDER BY si.student_name ASC
            ");
            
            $stmt->bindParam(':grade_id', $grade_id);
            $stmt->execute();
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($results)) {
                return [
                    'status' => 'success',
                    'total_students' => $totalStudents,
                    'data' => [],
                    'message' => 'No active students found in this grade'
                ];
            }
            
            return [
                'status' => 'success',
                'total_students' => $totalStudents,
                'data' => $results
            ];
            
        } catch (PDOException $e) {
            error_log("Error in fetchStudentByGradeId: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Failed to fetch students by grade: ' . $e->getMessage()
            ];
        }
    }
    public function fetchStudentRankingAndAverages($grade_id) {
        try {
            $stmt = $this->conn->prepare("
                WITH monthly_scores AS (
                    SELECT 
                        sms.student_id,
                        si.student_name,
                        si.gender,
                        c.class_name,
                        g.grade_id,
                        m.month_name,
                        ROUND(AVG(sms.score), 2) AS monthly_average,
                        COUNT(DISTINCT csms.monthly_id) AS months_counted
                    FROM tbl_student_monthly_score sms
                    JOIN tbl_student_info si ON sms.student_id = si.student_id
                    JOIN classroom_subject_monthly_score csms ON sms.classroom_subject_monthly_score_id = csms.classroom_subject_monthly_score_id
                    JOIN tbl_classroom c ON csms.class_id = c.class_id
                    JOIN tbl_grade g ON c.grade_id = g.grade_id
                    JOIN tbl_monthly m ON csms.monthly_id = m.monthly_id
                    WHERE sms.isDeleted = 0
                      AND g.grade_id = :grade_id
                    GROUP BY sms.student_id, si.student_name, c.class_name, g.grade_id, m.month_name
                ),
                semester_scores AS (
                    SELECT 
                        student_id,
                        ROUND(AVG(score), 2) AS semester_exam_score
                    FROM tbl_student_semester_score
                    WHERE isDeleted = 0
                    GROUP BY student_id
                )
                SELECT 
                    ms.student_id,
                    ms.student_name,
                    ms.gender,
                    ms.class_name,
                    ROUND(AVG(ms.monthly_average), 2) AS monthly_average,
                    ROUND(ss.semester_exam_score, 2) AS semester_exam_score,
                    ROUND((ROUND(AVG(ms.monthly_average), 2) + ROUND(ss.semester_exam_score, 2)) / 2, 2) AS final_average,
                    RANK() OVER (
                        PARTITION BY ms.class_name 
                        ORDER BY ROUND((ROUND(AVG(ms.monthly_average), 2) + ROUND(ss.semester_exam_score, 2)) / 2, 2) DESC
                    ) AS class_rank
                FROM monthly_scores ms
                LEFT JOIN semester_scores ss ON ms.student_id = ss.student_id
                GROUP BY ms.student_id, ms.student_name, ms.class_name, ss.semester_exam_score
                ORDER BY ms.class_name, final_average DESC
            ");
    
            $stmt->bindParam(':grade_id', $grade_id, PDO::PARAM_INT);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            return [
                'status' => 'success',
                'data' => $results
            ];
        } catch (PDOException $e) {
            error_log("Error in fetchStudentRankingAndAverages: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Failed to fetch student rankings: ' . $e->getMessage()
            ];
        }
    }
    
    public function getCurrentYearStudy() {
        // For now, return year_study_id=1 since that's where the students are enrolled
        return ['year_study_id' => 1];
        
        // Original code commented out for reference
        // $query = "SELECT year_study_id FROM tbl_year_study WHERE isDeleted = 0 ORDER BY year_study_id DESC LIMIT 1";
        // $stmt = $this->conn->prepare($query);
        // $stmt->execute();
        // return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getStudentSemesterScore($studentId) {
        // First check if there's an active semester
        $semesterQuery = "SELECT semester_id, semester_name 
                         FROM tbl_semester 
                         WHERE isDeleted = 0 
                         ORDER BY semester_id DESC 
                         LIMIT 1";
        $semesterStmt = $this->conn->prepare($semesterQuery);
        $semesterStmt->execute();
        $activeSemester = $semesterStmt->fetch(PDO::FETCH_ASSOC);

        if (!$activeSemester) {
            error_log("No active semester found");
            return null;
        }

        // Get student's score for the active semester
        $query = "SELECT sss.score as final_semester_average, ses.semester_id
                 FROM tbl_student_semester_score sss
                 JOIN tbl_semester_exam_subjects ses ON sss.semester_exam_subject_id = ses.id
                 WHERE sss.student_id = ? 
                 AND ses.semester_id = ?
                 AND sss.isDeleted = 0
                 ORDER BY sss.create_date DESC 
                 LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $studentId);
        $stmt->bindParam(2, $activeSemester['semester_id']);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            error_log("Student $studentId score: " . json_encode($result));
        } else {
            error_log("No score found for student $studentId in semester " . $activeSemester['semester_id']);
        }

        return $result;
    }

    public function updateStudyStatus($studyId, $status) {
        try {
            $query = "UPDATE tbl_study SET status = :status WHERE study_id = :study_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':study_id', $studyId);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error updating study status: " . $e->getMessage());
            return false;
        }
    }

    public function checkClassesExist($classIds) {
        $placeholders = str_repeat('?,', count($classIds) - 1) . '?';
        $query = "SELECT class_id, class_name FROM tbl_classroom WHERE class_id IN ($placeholders) AND isDeleted = 0";
        $stmt = $this->conn->prepare($query);
        
        foreach ($classIds as $index => $classId) {
            $stmt->bindValue($index + 1, $classId);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
