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
        try {
            $this->conn->beginTransaction();

            // First check if student exists
            $checkQuery = "SELECT student_id FROM tbl_student_info WHERE student_id = ? AND isDeleted = 0";
            $checkStmt = $this->conn->prepare($checkQuery);
            $checkStmt->bindParam(1, $data['student_id']);
            $checkStmt->execute();
            
            if (!$checkStmt->fetch(PDO::FETCH_ASSOC)) {
                throw new Exception("សិស្សមិនត្រូវបានរកឃើញ");
            }

            // Check class limit with a lock
            $classLimitQuery = "SELECT c.num_students_in_class, c.class_name,
                               (SELECT COUNT(*) FROM tbl_study s 
                                WHERE s.class_id = c.class_id AND s.isDeleted = 0 AND s.status = 'active') as current_students
                               FROM tbl_classroom c 
                               WHERE c.class_id = ? FOR UPDATE";
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
                $this->conn->commit();
                return $this->conn->lastInsertId();
            }
            
            $this->conn->rollBack();
            return false;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }
    
    public function updateStudy($id, $data) {
        try {
            $this->conn->beginTransaction();

            // First get the current study record
            $currentStudyQuery = "SELECT * FROM tbl_study WHERE study_id = ? AND isDeleted = 0";
            $currentStmt = $this->conn->prepare($currentStudyQuery);
            $currentStmt->bindParam(1, $id);
            $currentStmt->execute();
            $currentStudy = $currentStmt->fetch(PDO::FETCH_ASSOC);

            if (!$currentStudy) {
                throw new Exception("កំណត់ត្រាសិស្សមិនត្រូវបានរកឃើញ");
            }

            // Check if student is already enrolled in another class (excluding current class)
            $checkQuery = "SELECT * FROM tbl_study 
                          WHERE student_id = ? 
                          AND study_id != ? 
                          AND isDeleted = 0 
                          AND status = 'active'";
            $checkStmt = $this->conn->prepare($checkQuery);
            $checkStmt->bindParam(1, $data['student_id']);
            $checkStmt->bindParam(2, $id);
            $checkStmt->execute();
            $checkResult = $checkStmt->fetch(PDO::FETCH_ASSOC);

            if ($checkResult) {
                throw new Exception("សិស្សនេះកំពុងរៀននៅថ្នាក់ផ្សេង");
            }

            // Check if student has any monthly scores
            $monthlyScoreQuery = "SELECT COUNT(*) as score_count 
                                FROM tbl_student_monthly_score sms
                                INNER JOIN classroom_subject_monthly_score csms 
                                    ON sms.classroom_subject_monthly_score_id = csms.classroom_subject_monthly_score_id
                                WHERE sms.student_id = ? 
                                AND csms.class_id = ? 
                                AND sms.isDeleted = 0";
            $monthlyStmt = $this->conn->prepare($monthlyScoreQuery);
            $monthlyStmt->bindParam(1, $data['student_id']);
            $monthlyStmt->bindParam(2, $currentStudy['class_id']);
            $monthlyStmt->execute();
            $monthlyResult = $monthlyStmt->fetch(PDO::FETCH_ASSOC);

            if ($monthlyResult['score_count'] > 0) {
                throw new Exception("សិស្សនេះមានពិន្ទុប្រចាំខែរួចហើយ មិនអាចផ្លាស់ប្តូរថ្នាក់បានទេ");
            }

            // Check if student has any semester scores
            $semesterScoreQuery = "SELECT COUNT(*) as score_count 
                                 FROM tbl_student_semester_score sss
                                 INNER JOIN tbl_semester_exam_subjects ses 
                                    ON sss.semester_exam_subject_id = ses.id
                                 WHERE sss.student_id = ? 
                                 AND ses.class_id = ? 
                                 AND sss.isDeleted = 0";
            $semesterStmt = $this->conn->prepare($semesterScoreQuery);
            $semesterStmt->bindParam(1, $data['student_id']);
            $semesterStmt->bindParam(2, $currentStudy['class_id']);
            $semesterStmt->execute();
            $semesterResult = $semesterStmt->fetch(PDO::FETCH_ASSOC);

            if ($semesterResult['score_count'] > 0) {
                throw new Exception("សិស្សនេះមានពិន្ទុប្រចាំឆមាសរួចហើយ មិនអាចផ្លាស់ប្តូរថ្នាក់បានទេ");
            }
            
            // Check if class limit is reached (excluding current student if moving within same class)
            $classLimitQuery = "SELECT c.num_students_in_class, c.class_name,
                               (SELECT COUNT(*) FROM tbl_study s 
                                WHERE s.class_id = c.class_id 
                                AND s.isDeleted = 0 
                                AND s.status = 'active'
                                AND s.student_id != ?) as current_students
                               FROM tbl_classroom c 
                               WHERE c.class_id = ? FOR UPDATE";
            $classLimitStmt = $this->conn->prepare($classLimitQuery);
            $classLimitStmt->bindParam(1, $data['student_id']);
            $classLimitStmt->bindParam(2, $data['class_id']);
            $classLimitStmt->execute();
            $classInfo = $classLimitStmt->fetch(PDO::FETCH_ASSOC);

            if ($classInfo['current_students'] >= $classInfo['num_students_in_class']) {
                throw new Exception("ថ្នាក់ " . $classInfo['class_name'] . " មានសិស្សពេញហើយ។ ចំនួនសិស្សអតិបរមា: " . $classInfo['num_students_in_class'] . " នាក់");
            }

            // Update study
            $query = "UPDATE tbl_study 
                      SET student_id = ?, 
                          class_id = ?, 
                          year_study_id = ?, 
                          enrollment_date = ?, 
                          status = ? 
                      WHERE study_id = ?";
            
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(1, $data['student_id']);
            $stmt->bindParam(2, $data['class_id']);
            $stmt->bindParam(3, $data['year_study_id']);
            $stmt->bindParam(4, $data['enrollment_date']);
            $stmt->bindParam(5, $data['status']);
            $stmt->bindParam(6, $id);
            
            if ($stmt->execute()) {
                $this->conn->commit();
                return true;
            }

            $this->conn->rollBack();
            return false;

        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
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
            $this->conn->beginTransaction();
            
            // Check class limit with a lock
            $classLimitQuery = "SELECT c.num_students_in_class, c.class_name,
                               (SELECT COUNT(*) FROM tbl_study s 
                                WHERE s.class_id = c.class_id AND s.isDeleted = 0 AND s.status = 'active') as current_students
                               FROM tbl_classroom c 
                               WHERE c.class_id = ? FOR UPDATE";
            $classLimitStmt = $this->conn->prepare($classLimitQuery);
            $classLimitStmt->bindParam(1, $data['class_id']);
            $classLimitStmt->execute();
            $classInfo = $classLimitStmt->fetch(PDO::FETCH_ASSOC);

            if (!$classInfo) {
                throw new Exception("ថ្នាក់មិនត្រូវបានរកឃើញ");
            }

            $availableSlots = $classInfo['num_students_in_class'] - $classInfo['current_students'];
            if (count($data['student_ids']) > $availableSlots) {
                throw new Exception("ថ្នាក់ " . $classInfo['class_name'] . " សិស្សពេញហើយ។ ចំនួនសិស្សអតិបរមា: " . $classInfo['num_students_in_class'] . " នាក់");
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
                    $status = !empty($data['status']) ? $data['status'] : $studentInfo['status'];
                    
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

    public function getStudentSemesterScore($studentId) {
        try {
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

            // Get the student's current class
            $classQuery = "SELECT class_id FROM tbl_study 
                          WHERE student_id = ? AND status = 'active' AND isDeleted = 0 
                          ORDER BY create_date DESC LIMIT 1";
            $classStmt = $this->conn->prepare($classQuery);
            $classStmt->bindParam(1, $studentId);
            $classStmt->execute();
            $currentClass = $classStmt->fetch(PDO::FETCH_ASSOC);

            if (!$currentClass) {
                error_log("No active class found for student $studentId");
                return null;
            }

            // Calculate scores using the same approach as fetchStudentRankingAndAverages
            $query = "WITH monthly_scores AS (
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
                        AND sms.student_id = ?
                        AND csms.class_id = ?
                        GROUP BY sms.student_id, si.student_name, c.class_name, g.grade_id, m.month_name
                    ),
                    semester_scores AS (
                        SELECT 
                            student_id,
                            ROUND(AVG(score), 2) AS semester_exam_score
                        FROM tbl_student_semester_score
                        WHERE isDeleted = 0
                        AND student_id = ?
                        GROUP BY student_id
                    )
                    SELECT 
                        ms.student_id,
                        ms.student_name,
                        ms.class_name,
                        ROUND(AVG(ms.monthly_average), 2) AS monthly_average,
                        ROUND(ss.semester_exam_score, 2) AS semester_exam_score,
                        ROUND((ROUND(AVG(ms.monthly_average), 2) + ROUND(ss.semester_exam_score, 2)) / 2, 2) AS final_average
                    FROM monthly_scores ms
                    LEFT JOIN semester_scores ss ON ms.student_id = ss.student_id
                    GROUP BY ms.student_id, ms.student_name, ms.class_name, ss.semester_exam_score";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(1, $studentId);
            $stmt->bindParam(2, $currentClass['class_id']);
            $stmt->bindParam(3, $studentId);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                error_log("Student $studentId scores: " . json_encode($result));
                return [
                    'final_semester_average' => $result['final_average'],
                    'semester_id' => $activeSemester['semester_id']
                ];
            } else {
                error_log("No scores found for student $studentId");
                return null;
            }
        } catch (Exception $e) {
            error_log("Error calculating semester score for student $studentId: " . $e->getMessage());
            return null;
        }
    }

    public function updateStudyStatus($studyId, $status) {
        try {
            //make the status select from tbl_student_info
            $studentInfoQuery = "SELECT status FROM tbl_student_info WHERE student_id = :student_id";
            $stmt = $this->conn->prepare($studentInfoQuery);
            $stmt->bindParam(':student_id', $studyId);
            $stmt->execute();
            $studentInfo = $stmt->fetch(PDO::FETCH_ASSOC);
            $status = $studentInfo['status'];
            $query = "UPDATE tbl_study SET status = :status, class_id = :class_id, year_study_id = :year_study_id, WHERE study_id = :study_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':study_id', $studyId);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return [
                'status' => 'success',
                'message' => 'Study status updated successfully',
                'data' => $result
            ];

        } catch (PDOException $e) {
            error_log("Error updating study status: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Failed to update study status: ' . $e->getMessage()
            ];
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

    public function getTopFiveMonthlyStudent($classId, $monthly_id) {
        try {
            $query = "SELECT * FROM vw_top_monthly_rankings
                      WHERE class_id = :class_id
                      AND monthly_id = :monthly_id
                      LIMIT 5";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':class_id', $classId, PDO::PARAM_INT);
            $stmt->bindParam(':monthly_id', $monthly_id, PDO::PARAM_INT);
            $stmt->execute();
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'status' => 'success',
                'data' => $results
            ];
        } catch (PDOException $e) {
            error_log("Error in getTopFiveMonthlyStudent: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Failed to fetch top 5 monthly students: ' . $e->getMessage()
            ];
        }
    }

    public function getTopFiveSemesterStudent($classId, $semester_id) {
        try {
            $query = "SELECT * FROM vw_top_semester_rankings
                      WHERE class_id = :class_id
                      AND semester_id = :semester_id
                      ORDER BY semester_avg DESC
                      LIMIT 5";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':class_id', $classId, PDO::PARAM_INT);
            $stmt->bindParam(':semester_id', $semester_id, PDO::PARAM_INT);
            $stmt->execute();
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'status' => 'success',
                'data' => $results
            ];
        } catch (PDOException $e) {
            error_log("Error in getTopFiveSemesterStudent: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Failed to fetch top 5 semester students: ' . $e->getMessage()
            ];
        }
    }

    public function getTopFiveYearlyStudent($classId, $yearStudyId) {
        try {
            $query = "SELECT * FROM vw_top_yearly_rankings
                      WHERE class_id = :class_id
                      AND year_study_id = :year_study_id
                      ORDER BY yearly_avg DESC
                      LIMIT 5";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':class_id', $classId, PDO::PARAM_INT);
            $stmt->bindParam(':year_study_id', $yearStudyId, PDO::PARAM_INT);
            $stmt->execute();
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'status' => 'success',
                'data' => $results
            ];
        } catch (PDOException $e) {
            error_log("Error in getTopFiveYearlyStudent: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Failed to fetch top 5 yearly students: ' . $e->getMessage()
            ];
        }
    }

    //give me function to promote student to next class by checking the yearly average promote by 0.5
    public function promoteStudentToNextClass($studentId, $classId, $yearStudyId) {
        try {
            $this->conn->beginTransaction();

            // First check if student exists and is active
            $studentQuery = "SELECT s.student_id, s.status 
                           FROM tbl_study s 
                           WHERE s.student_id = :student_id 
                           AND s.class_id = :class_id 
                           AND s.year_study_id = :year_study_id 
                           AND s.isDeleted = 0 
                           AND s.status = 'active'";
            
            $stmt = $this->conn->prepare($studentQuery);
            $stmt->bindParam(':student_id', $studentId);
            $stmt->bindParam(':class_id', $classId);
            $stmt->bindParam(':year_study_id', $yearStudyId);
            $stmt->execute();
            
            if (!$stmt->fetch()) {
                throw new Exception("សិស្សមិនត្រូវបានរកឃើញ ឬមិនមានស្ថានភាពសកម្ម");
            }

            // Get the current class's grade
            $gradeQuery = "SELECT c.grade_id, g.grade_name 
                          FROM tbl_classroom c 
                          JOIN tbl_grade g ON c.grade_id = g.grade_id 
                          WHERE c.class_id = :class_id";
            
            $stmt = $this->conn->prepare($gradeQuery);
            $stmt->bindParam(':class_id', $classId);
            $stmt->execute();
            $currentGrade = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$currentGrade) {
                throw new Exception("ថ្នាក់មិនត្រូវបានរកឃើញ");
            }

            // Get the next grade's ID
            $nextGradeQuery = "SELECT grade_id, grade_name 
                             FROM tbl_grade
                             WHERE grade_id > :current_grade_id 
                             ORDER BY grade_id ASC 
                             LIMIT 1";
            
            $stmt = $this->conn->prepare($nextGradeQuery);
            $stmt->bindParam(':current_grade_id', $currentGrade['grade_id']);
            $stmt->execute();
            $nextGrade = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$nextGrade) {
                throw new Exception("គ្មានថ្នាក់បន្ទាប់ទេ");
            }

            // Get a class in the next grade
            $nextClassQuery = "SELECT class_id, class_name 
                             FROM tbl_classroom 
                             WHERE grade_id = :next_grade_id 
                             AND isDeleted = 0 
                             LIMIT 1";
            
            $stmt = $this->conn->prepare($nextClassQuery);
            $stmt->bindParam(':next_grade_id', $nextGrade['grade_id']);
            $stmt->execute();
            $nextClass = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$nextClass) {
                throw new Exception("គ្មានថ្នាក់នៅក្នុងថ្នាក់បន្ទាប់ទេ");
            }

            // Update the current study record to inactive
            $updateCurrentQuery = "UPDATE tbl_study 
                                 SET status = 'inactive' 
                                 WHERE student_id = :student_id 
                                 AND class_id = :class_id 
                                 AND year_study_id = :year_study_id";
            
            $stmt = $this->conn->prepare($updateCurrentQuery);
            $stmt->bindParam(':student_id', $studentId);
            $stmt->bindParam(':class_id', $classId);
            $stmt->bindParam(':year_study_id', $yearStudyId);
            
            if (!$stmt->execute()) {
                throw new Exception("មិនអាចធ្វើបច្ចុប្បន្នភាពកំណត់ត្រាបច្ចុប្បន្នបានទេ");
            }

            // Fetch student info
            $studentInfoQuery = "SELECT student_name FROM tbl_student_info WHERE student_id = :student_id";
            $stmtStudentInfo = $this->conn->prepare($studentInfoQuery);
            $stmtStudentInfo->bindParam(':student_id', $studentId);
            $stmtStudentInfo->execute();
            $studentInfo = $stmtStudentInfo->fetch(PDO::FETCH_ASSOC);

            //check if new class status = inactive dont insert to that class if other class is active insert to that class
            $checkNewClassStatus = "SELECT status FROM tbl_classroom WHERE class_id = :next_class_id";
            $stmt = $this->conn->prepare($checkNewClassStatus);
            $stmt->bindParam(':next_class_id', $nextClass['class_id']);
            $stmt->execute();
            $newClassStatus = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($newClassStatus['status'] == 'inactive') {
                throw new Exception("ថ្នាក់រៀននេះត្រូវបានផ្អាក");
            }else{
                // Create new study record with next year study ID
                $insertQuery = "INSERT INTO tbl_study
                              (student_id, class_id, year_study_id, enrollment_date, status) 
                              VALUES (:student_id, :next_class_id, :next_year_study_id, CURDATE(), 'active')";
                $stmt = $this->conn->prepare($insertQuery);
                $stmt->bindParam(':student_id', $studentId);
                $stmt->bindParam(':next_class_id', $nextClass['class_id']);
                $stmt->bindParam(':next_year_study_id', $nextGrade['year_study_id']);
                $stmt->execute();

                $promotionResults = [
                    'student_id' => $studentId,
                    'student_name' => $studentInfo['student_name'],
                    'from_class' => $currentGrade['grade_name'],
                    'to_class' => $nextGrade['grade_name'],
                    'new_class_name' => $nextClass['class_name']
                ];
            }

            $this->conn->commit();
            
            return [
                'status' => 'success',
                'message' => 'សិស្សត្រូវបានផ្លាស់ប្តូរទៅថ្នាក់បន្ទាប់ដោយជោគជ័យ',
                'data' => $promotionResults
            ];

        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Error promoting student to next class: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    public function promoteStudentsByGrade($currentGradeId, $yearStudyId) {
        try {
            $this->conn->beginTransaction();

            // 1. Get next grade
            $nextGradeQuery = "SELECT grade_id, grade_name 
                              FROM tbl_grade 
                              WHERE grade_id > :current_grade_id 
                              ORDER BY grade_id ASC 
                              LIMIT 1";
            
            $stmt = $this->conn->prepare($nextGradeQuery);
            $stmt->bindParam(':current_grade_id', $currentGradeId);
            $stmt->execute();
            $nextGrade = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$nextGrade) {
                throw new Exception("គ្មានថ្នាក់បន្ទាប់ទេ");
            }

            // 2. Get next year study
            $nextYearQuery = "SELECT year_study_id, year_study 
                             FROM tbl_year_study
                             WHERE year_study_id > :current_year_id 
                             ORDER BY year_study_id ASC 
                             LIMIT 1";
            
            $stmt = $this->conn->prepare($nextYearQuery);
            $stmt->bindParam(':current_year_id', $yearStudyId);
            $stmt->execute();
            $nextYear = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$nextYear) {
                throw new Exception("គ្មានឆ្នាំសិក្សាបន្ទាប់ទេ");
            }

            // 3. Get all classes in the next grade (only active)
            $nextClassesQuery = "SELECT class_id, class_name 
                               FROM tbl_classroom 
                               WHERE grade_id = :next_grade_id 
                               AND isDeleted = 0
                               AND status = 'active'";
            
            $stmt = $this->conn->prepare($nextClassesQuery);
            $stmt->bindParam(':next_grade_id', $nextGrade['grade_id']);
            $stmt->execute();
            $nextClasses = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($nextClasses)) {
                throw new Exception("គ្មានថ្នាក់រៀននៅក្នុងកំរិតបន្ទាប់ទេ");
            }

            // 4. Get all classes in current grade
            $currentClassesQuery = "SELECT class_id, class_name 
                                  FROM tbl_classroom 
                                  WHERE grade_id = :current_grade_id 
                                  AND isDeleted = 0";
            
            $stmt = $this->conn->prepare($currentClassesQuery);
            $stmt->bindParam(':current_grade_id', $currentGradeId);
            $stmt->execute();
            $currentClasses = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 5. Get eligible students with yearly averages
            $eligibleStudents = [];
            foreach ($currentClasses as $class) {
                // Get yearly averages for each class using the stored procedure
                $yearlyAvgQuery = "CALL GetYearlyAverageForClass(:class_id)";
                $stmt = $this->conn->prepare($yearlyAvgQuery);
                $stmt->bindParam(':class_id', $class['class_id']);
                $stmt->execute();
                $classResults = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Filter students with yearly average >= 5.0
                foreach ($classResults as $student) {
                    if ($student['yearly_avg'] >= 5.0) {
                        $eligibleStudents[] = [
                            'student_id' => $student['student_id'],
                            'student_name' => $student['student_name'],
                            'current_class_id' => $class['class_id'],
                            'current_class_name' => $class['class_name'],
                            'yearly_avg' => $student['yearly_avg']
                        ];
                    }
                }
            }

            if (empty($eligibleStudents)) {
                throw new Exception("គ្មានសិស្សដែលមានមធ្យមភាគ ≥ 5.0 ទេ");
            }

            // 6. Distribute students to new classes
            $studentsPerClass = ceil(count($eligibleStudents) / count($nextClasses));
            $classAssignments = array_fill(0, count($nextClasses), []);

            // Group students by their current class
            $studentsByOldClass = [];
            foreach ($eligibleStudents as $student) {
                $studentsByOldClass[$student['current_class_id']][] = $student;
            }

            // Assign top students from each old class to different new classes
            $currentClassIndex = 0;
            foreach ($studentsByOldClass as $oldClassStudents) {
                if (!empty($oldClassStudents)) {
                    // Sort students by yearly average
                    usort($oldClassStudents, function($a, $b) {
                        return $b['yearly_avg'] <=> $a['yearly_avg'];
                    });
                    
                    $topStudent = $oldClassStudents[0];
                    $classAssignments[$currentClassIndex][] = $topStudent;
                    $currentClassIndex = ($currentClassIndex + 1) % count($nextClasses);
                }
            }

            // Assign remaining students
            $remainingStudents = array_filter($eligibleStudents, function($student) use ($classAssignments) {
                foreach ($classAssignments as $class) {
                    foreach ($class as $assignedStudent) {
                        if ($assignedStudent['student_id'] === $student['student_id']) {
                            return false;
                        }
                    }
                }
                return true;
            });

            // Distribute remaining students while maintaining balanced class averages
            foreach ($remainingStudents as $student) {
                $minAvgClassIndex = 0;
                $minClassAvg = PHP_FLOAT_MAX;
                
                for ($i = 0; $i < count($nextClasses); $i++) {
                    if (count($classAssignments[$i]) < $studentsPerClass) {
                        $classAvg = 0;
                        if (!empty($classAssignments[$i])) {
                            $classAvg = array_sum(array_column($classAssignments[$i], 'yearly_avg')) / count($classAssignments[$i]);
                        }
                        if ($classAvg < $minClassAvg) {
                            $minClassAvg = $classAvg;
                            $minAvgClassIndex = $i;
                        }
                    }
                }
                
                $classAssignments[$minAvgClassIndex][] = $student;
            }

            // 7. Process promotions
            $promotionResults = [];
            foreach ($classAssignments as $classIndex => $students) {
                $nextClass = $nextClasses[$classIndex];
                
                foreach ($students as $student) {
                    // Update current class to inactive
                    $updateCurrentQuery = "UPDATE tbl_study 
                                         SET status = 'inactive' 
                                         WHERE student_id = :student_id 
                                         AND class_id = :current_class_id 
                                         AND year_study_id = :year_study_id";
                    
                    $stmt = $this->conn->prepare($updateCurrentQuery);
                    $stmt->bindParam(':student_id', $student['student_id']);
                    $stmt->bindParam(':current_class_id', $student['current_class_id']);
                    $stmt->bindParam(':year_study_id', $yearStudyId);
                    $stmt->execute();

                    // Fetch student info
                    $studentInfoQuery = "SELECT student_name FROM tbl_student_info WHERE student_id = :student_id";
                    $stmtStudentInfo = $this->conn->prepare($studentInfoQuery);
                    $stmtStudentInfo->bindParam(':student_id', $student['student_id']);
                    $stmtStudentInfo->execute();
                    $studentInfo = $stmtStudentInfo->fetch(PDO::FETCH_ASSOC);

                    //check if new class status = inactive dont insert to that class if other class is active insert to that class
                    $checkNewClassStatus = "SELECT status FROM tbl_classroom WHERE class_id = :next_class_id";
                    $stmt = $this->conn->prepare($checkNewClassStatus);
                    $stmt->bindParam(':next_class_id', $nextClass['class_id']);
                    $stmt->execute();
                    $newClassStatus = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($newClassStatus['status'] == 'inactive') {
                        throw new Exception("ថ្នាក់រៀននេះត្រូវបានផ្អាក");
                    }else{
                        // Create new study record with next year study ID
                        $insertQuery = "INSERT INTO tbl_study
                                      (student_id, class_id, year_study_id, enrollment_date, status) 
                                      VALUES (:student_id, :next_class_id, :next_year_study_id, CURDATE(), 'active')";
                        $stmt = $this->conn->prepare($insertQuery);
                        $stmt->bindParam(':student_id', $student['student_id']);
                        $stmt->bindParam(':next_class_id', $nextClass['class_id']);
                        $stmt->bindParam(':next_year_study_id', $nextYear['year_study_id']);
                        $stmt->execute();

                        $promotionResults[] = [
                            'student_id' => $student['student_id'],
                            'student_name' => $student['student_name'],
                            'from_class' => $student['current_class_name'],
                            'to_class' => $nextClass['class_name'],
                            'yearly_avg' => $student['yearly_avg'],
                            'from_year' => $yearStudyId,
                            'to_year' => $nextYear['year_study_id']
                        ];
                    }
                }
            }

            $this->conn->commit();
            
            return [
                'status' => 'success',
                'message' => 'សិស្សត្រូវបានដំឡើងថ្នាក់ដោយជោគជ័យ',
                'data' => [
                    'promoted_students' => $promotionResults,
                    'next_grade' => $nextGrade['grade_name'],
                    'next_year' => $nextYear['year_study'],
                    'total_promoted' => count($promotionResults)
                ]
            ];

        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Error promoting students by grade: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
}
