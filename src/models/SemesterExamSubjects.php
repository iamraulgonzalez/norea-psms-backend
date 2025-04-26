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

    public function create($data) {
        try {
            if (!isset($data['class_id']) || !isset($data['semester_id']) ||
                !isset($data['assign_subject_grade_id']) || !isset($data['monthly_ids'])) {
                return [
                    'status' => 'error',
                    'message' => 'Missing required fields'
                ];
            }

            $class_id = $data['class_id'];
            $semester_id = $data['semester_id'];
            $monthly_ids_str = is_array($data['monthly_ids']) ? implode(',', $data['monthly_ids']) : $data['monthly_ids'];
            $assign_subject_grade_ids = is_array($data['assign_subject_grade_id']) ? $data['assign_subject_grade_id'] : explode(',', $data['assign_subject_grade_id']);

            $success_count = 0;
            $error_messages = [];

            foreach ($assign_subject_grade_ids as $assign_subject_grade_id) {
                // Check if already exists for this specific subject, class, and semester
                $checkQuery = "SELECT COUNT(*) FROM tbl_semester_exam_subjects
                                    WHERE class_id = :class_id
                                    AND semester_id = :semester_id
                                    AND assign_subject_grade_id = :assign_subject_grade_id
                                    AND isDeleted = 0";
                $stmtCheck = $this->conn->prepare($checkQuery);
                $stmtCheck->bindParam(':class_id', $class_id);
                $stmtCheck->bindParam(':semester_id', $semester_id);
                $stmtCheck->bindParam(':assign_subject_grade_id', $assign_subject_grade_id);
                $stmtCheck->execute();

                if ($stmtCheck->fetchColumn() > 0) {
                    $error_messages[] = "Semester exam subject already exists for assign_subject_grade_id: $assign_subject_grade_id in class $class_id, semester $semester_id";
                    continue; // Skip to the next subject
                }

                // Insert each subject individually with the same monthly_ids
                $insertQuery = "INSERT INTO tbl_semester_exam_subjects
                                    (class_id, semester_id, assign_subject_grade_id, monthly_ids)
                                    VALUES (:class_id, :semester_id, :assign_subject_grade_id, :monthly_ids)";
                $stmtInsert = $this->conn->prepare($insertQuery);
                $stmtInsert->bindParam(':class_id', $class_id);
                $stmtInsert->bindParam(':semester_id', $semester_id);
                $stmtInsert->bindParam(':assign_subject_grade_id', $assign_subject_grade_id);
                $stmtInsert->bindParam(':monthly_ids', $monthly_ids_str);

                if ($stmtInsert->execute()) {
                    $success_count++;
                } else {
                    $error_messages[] = "Failed to insert for assign_subject_grade_id: $assign_subject_grade_id in class $class_id, semester $semester_id";
                }
            }

            if ($success_count > 0 && empty($error_messages)) {
                return [
                    'status' => 'success',
                    'message' => 'Semester exam subjects assigned successfully'
                ];
            } elseif ($success_count > 0 && !empty($error_messages)) {
                return [
                    'status' => 'warning',
                    'message' => 'Some semester exam subjects assigned with errors',
                    'errors' => $error_messages
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Failed to assign semester exam subjects',
                    'errors' => $error_messages
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
    public function updatee($id, $data) {
        try {
            $fields = [];
            $params = [':id' => $id];
    
            if (isset($data['class_id'])) {
                $fields[] = "class_id = :class_id";
                $params[':class_id'] = $data['class_id'];
            }
            if (isset($data['semester_id'])) {
                $fields[] = "semester_id = :semester_id";
                $params[':semester_id'] = $data['semester_id'];
            }
            if (isset($data['assign_subject_grade_id'])) {
                $fields[] = "assign_subject_grade_id = :assign_subject_grade_id";
                $params[':assign_subject_grade_id'] = $data['assign_subject_grade_id'];
            }
            if (isset($data['monthly_ids'])) {
                // Convert array to comma string if necessary
                if (is_array($data['monthly_ids'])) {
                    $data['monthly_ids'] = implode(',', $data['monthly_ids']);
                }
                $fields[] = "monthly_ids = :monthly_ids";
                $params[':monthly_ids'] = $data['monthly_ids'];
            }
    
            if (empty($fields)) {
                return [
                    'status' => 'error',
                    'message' => 'No fields to update'
                ];
            }
    
            $query = "UPDATE tbl_semester_exam_subjects SET " . implode(', ', $fields) . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
    
            foreach ($params as $param => $value) {
                $stmt->bindValue($param, $value);
            }
    
            if ($stmt->execute()) {
                return [
                    'status' => 'success',
                    'message' => 'Semester exam subject updated successfully'
                ];
            }
    
            return [
                'status' => 'error',
                'message' => 'Failed to update semester exam subject'
            ];
    
        } catch (PDOException $e) {
            error_log("Error updating semester exam subject: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Database error occurred'
            ];
        }
    }    

    public function getSemesterExamSubjectsByClassId($classId) {
        try {
            // SQL query
            //count all monthly_ids
            $query = "SELECT 
                        ses.id,
                        ses.class_id,
                        c.class_name,
                        ses.monthly_ids,
                        ses.semester_id,
                        sem.semester_name,
                        ses.assign_subject_grade_id,
                        COUNT(ses.id) as subject_count,
                        CHAR_LENGTH(ses.monthly_ids) - CHAR_LENGTH(REPLACE(ses.monthly_ids, ',', '')) + 1 AS monthly_count,
                        ses.create_date,
                        GROUP_CONCAT(
                            JSON_OBJECT(
                                'semester_exam_subject_id', ses.id,
                                'class_id', c.class_id,
                                'class_name', c.class_name,
                                'semester_id', sem.semester_id,
                                'semester_name', sem.semester_name,
                                'assign_subject_grade_id', asg.assign_subject_grade_id,
                                'subject_code', s.subject_code,
                                'subject_name', s.subject_name
                            )
                        ) AS subjects
                    FROM tbl_semester_exam_subjects ses
                    JOIN tbl_classroom c ON ses.class_id = c.class_id
                    JOIN tbl_semester sem ON ses.semester_id = sem.semester_id
                    JOIN tbl_assign_subject_grade asg ON ses.assign_subject_grade_id = asg.assign_subject_grade_id
                    JOIN tbl_subject s ON asg.subject_code = s.subject_code
                    WHERE ses.class_id = :class_id
                    AND ses.isDeleted = 0
                    GROUP BY ses.class_id, ses.semester_id
                    ORDER BY ses.class_id, ses.semester_id";
    
            // Prepare the query
            $stmt = $this->conn->prepare($query);
    
            // Bind the class_id parameter
            $stmt->bindParam(':class_id', $classId, PDO::PARAM_INT);
    
            // Execute the query
            $stmt->execute();
    
            // Fetch all results
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            // Process the results to convert the JSON string to an array
            foreach ($results as &$result) {
                $result['subjects'] = json_decode('[' . $result['subjects'] . ']', true);
            }
    
            // Return the response
            return [
                'status' => 'success',
                'data' => $results
            ];
    
        } catch (PDOException $e) {
            // Log error if any exception occurs
            error_log("Error fetching semester exam subjects by class: " . $e->getMessage());
            
            // Return error response
            return [
                'status' => 'error',
                'message' => 'Failed to fetch semester exam subjects'
            ];
        }
    }
    
    public function getAvailableMonthlyScores($classId) {
        //only monthly_id, month_name
        try {
            $query = "SELECT 
                        sms.student_monthly_score_id,
                        sms.student_id,
                        sms.score,
                        csm.class_id,
                        csm.assign_subject_grade_id,
                        csm.monthly_id,
                        m.month_name,
                        sms.create_date AS score_date
                    FROM 
                        tbl_student_monthly_score sms
                    JOIN 
                        classroom_subject_monthly_score csm 
                        ON sms.classroom_subject_monthly_score_id = csm.classroom_subject_monthly_score_id
                    JOIN 
                        tbl_monthly m 
                        ON csm.monthly_id = m.monthly_id
                    WHERE 
                        sms.isDeleted = 0 AND
                        csm.isDeleted = 0 AND
                        m.isDeleted = 0 AND
                        csm.class_id = :classId";
    
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':classId', $classId, PDO::PARAM_INT);
            $stmt->execute();

            //group by monthly_id
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $groupedResult = [];
            foreach ($result as $row) {
                $monthlyId = $row['monthly_id'];
                if (!isset($groupedResult[$monthlyId])) {
                    $groupedResult[$monthlyId] = [
                        'monthly_id' => $monthlyId,
                        'month_name' => $row['month_name'],
                        'scores' => []
                    ];
                }
                $groupedResult[$monthlyId]['scores'][] = $row;
            }
    
            return [
                'status' => 'success',
                'data' => array_values($groupedResult)
            ];
        } catch (PDOException $e) {
            error_log("Error fetching available monthly scores: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Failed to fetch available monthly scores'
            ];
        }
    }

    public function getByClassSubjectSemester($class_id, $assign_subject_grade_id, $semester_id) {
        try {
            $query = "SELECT 
                csms.id,
                csms.class_id,
                c.class_name,
                g.grade_id,
                g.grade_name,
                csms.semester_id,
                sem.semester_name,
                csms.assign_subject_grade_id,
                s.subject_code,
                s.subject_name
            FROM tbl_semester_exam_subjects csms
            LEFT JOIN tbl_classroom c ON csms.class_id = c.class_id
            LEFT JOIN tbl_grade g ON c.grade_id = g.grade_id
            LEFT JOIN tbl_semester sem ON csms.semester_id = sem.semester_id
            LEFT JOIN tbl_assign_subject_grade asg ON csms.assign_subject_grade_id = asg.assign_subject_grade_id
            LEFT JOIN tbl_subject s ON asg.subject_code = s.subject_code
            WHERE csms.class_id = :class_id
            AND csms.assign_subject_grade_id = :assign_subject_grade_id
            AND csms.semester_id = :semester_id
            AND csms.isDeleted = 0";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':class_id', $class_id);
            $stmt->bindParam(':assign_subject_grade_id', $assign_subject_grade_id);
            $stmt->bindParam(':semester_id', $semester_id);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                return [
                    'status' => 'success',
                    'data' => $result
                ];
            }

            return [
                'status' => 'error',
                'message' => 'Semester exam subject not found'
            ];

        } catch (PDOException $e) {
            error_log("Error in getByClassSubjectSemester: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Database error occurred'
            ];
        }
    }
    
    /**
     * Update all semester exam subjects for a given class and semester at once
     * This is useful for updating monthly_ids consistently across all subjects
     */
    public function updateAllForClassAndSemester($class_id, $semester_id, $data) {
        try {
            // Start a transaction to ensure consistency
            $this->conn->beginTransaction();
            
            $updateFields = [];
            $params = [
                ':class_id' => $class_id,
                ':semester_id' => $semester_id
            ];
            
            // Only allow updating certain fields in batch mode
            if (isset($data['monthly_ids'])) {
                // Ensure monthly_ids is a comma-separated string
                $monthly_ids = is_array($data['monthly_ids']) 
                    ? implode(',', $data['monthly_ids']) 
                    : $data['monthly_ids'];
                
                $updateFields[] = "monthly_ids = :monthly_ids";
                $params[':monthly_ids'] = $monthly_ids;
            }
            
            if (empty($updateFields)) {
                return [
                    'status' => 'error',
                    'message' => 'No fields to update'
                ];
            }
            
            // Update all records with the given class_id and semester_id
            $query = "UPDATE tbl_semester_exam_subjects 
                      SET " . implode(', ', $updateFields) . "
                      WHERE class_id = :class_id 
                      AND semester_id = :semester_id 
                      AND isDeleted = 0";
            
            $stmt = $this->conn->prepare($query);
            
            foreach ($params as $param => $value) {
                $stmt->bindValue($param, $value);
            }
            
            $stmt->execute();
            
            // Get the number of rows affected
            $rowCount = $stmt->rowCount();
            
            if ($rowCount > 0) {
                $this->conn->commit();
                return [
                    'status' => 'success',
                    'message' => 'Updated ' . $rowCount . ' semester exam subject records successfully',
                    'rows_affected' => $rowCount
                ];
            } else {
                $this->conn->rollBack();
                return [
                    'status' => 'error',
                    'message' => 'No records found to update'
                ];
            }
            
        } catch (PDOException $e) {
            // Roll back the transaction on error
            $this->conn->rollBack();
            error_log("Error updating semester exam subjects: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Database error occurred: ' . $e->getMessage()
            ];
        }
    }
}


