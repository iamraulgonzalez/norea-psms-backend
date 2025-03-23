<?php
require_once __DIR__ . '/../config/database.php';

class StudentSemesterExamScores {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getStudentExamScores($student_id, $class_id, $semester_id) {
        try {
            $query = "SELECT 
                sses.id,
                sses.student_id,
                si.student_name,
                sses.class_id,
                c.class_name,
                sses.semester_id,
                sem.semester_name,
                sses.semester_exam_subject_id,
                ses.assign_subject_grade_id,
                asg.subject_code,
                s.subject_name,
                sses.score,
                sses.create_date
            FROM tbl_student_semester_exam_scores sses
            JOIN tbl_student_info si ON sses.student_id = si.student_id
            JOIN tbl_classroom c ON sses.class_id = c.class_id
            JOIN tbl_semester sem ON sses.semester_id = sem.semester_id
            JOIN tbl_semester_exam_subjects ses ON sses.semester_exam_subject_id = ses.id
            JOIN tbl_assign_subject_grade asg ON ses.assign_subject_grade_id = asg.assign_subject_grade_id
            JOIN tbl_subject s ON asg.subject_code = s.subject_code
            WHERE sses.student_id = :student_id
            AND sses.class_id = :class_id
            AND sses.semester_id = :semester_id
            AND sses.isDeleted = 0
            ORDER BY s.subject_name";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':student_id', $student_id);
            $stmt->bindParam(':class_id', $class_id);
            $stmt->bindParam(':semester_id', $semester_id);
            $stmt->execute();
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Calculate average if there are scores
            $average = null;
            $subjectCount = count($results);
            
            if ($subjectCount > 0) {
                $totalScore = 0;
                foreach ($results as $score) {
                    $totalScore += $score['score'];
                }
                $average = $totalScore / $subjectCount;
            }
            
            return [
                'status' => 'success',
                'data' => [
                    'scores' => $results,
                    'average' => $average,
                    'subject_count' => $subjectCount
                ]
            ];
        } catch (PDOException $e) {
            error_log("Error fetching student semester exam scores: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Failed to fetch semester exam scores'
            ];
        }
    }

    public function getClassExamScores($class_id, $semester_id) {
        try {
            $query = "SELECT 
                sses.student_id,
                si.student_name,
                sses.class_id,
                c.class_name,
                sses.semester_id,
                sem.semester_name,
                GROUP_CONCAT(CONCAT(s.subject_name, ':', sses.score) SEPARATOR ', ') as subject_scores,
                AVG(sses.score) as average_score,
                COUNT(sses.id) as subject_count,
                GROUP_CONCAT(sses.months_calculated SEPARATOR '|') as months_calculated_all,
                GROUP_CONCAT(sses.monthly_average SEPARATOR '|') as monthly_averages
            FROM tbl_student_semester_exam_scores sses
            JOIN tbl_student_info si ON sses.student_id = si.student_id
            JOIN tbl_classroom c ON sses.class_id = c.class_id
            JOIN tbl_semester sem ON sses.semester_id = sem.semester_id
            JOIN tbl_semester_exam_subjects ses ON sses.semester_exam_subject_id = ses.id
            JOIN tbl_assign_subject_grade asg ON ses.assign_subject_grade_id = asg.assign_subject_grade_id
            JOIN tbl_subject s ON asg.subject_code = s.subject_code
            WHERE sses.class_id = :class_id
            AND sses.semester_id = :semester_id
            AND sses.isDeleted = 0
            GROUP BY sses.student_id, si.student_name
            ORDER BY average_score DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':class_id', $class_id);
            $stmt->bindParam(':semester_id', $semester_id);
            $stmt->execute();
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get information about available months
            $monthsQuery = "SELECT monthly_id, month_name FROM tbl_monthly WHERE isDeleted = 0 ORDER BY monthly_id";
            $monthsStmt = $this->conn->prepare($monthsQuery);
            $monthsStmt->execute();
            $availableMonths = $monthsStmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Process the results to format the months_calculated data
            foreach ($results as &$result) {
                // Process months calculated for each subject
                if (!empty($result['months_calculated_all'])) {
                    $monthsCalculatedList = explode('|', $result['months_calculated_all']);
                    
                    // Convert all month IDs to a unique list
                    $allMonthIds = [];
                    foreach ($monthsCalculatedList as $monthsStr) {
                        if (!empty($monthsStr)) {
                            $subjectMonths = explode(',', $monthsStr);
                            foreach ($subjectMonths as $month) {
                                if (!empty($month) && is_numeric($month)) {
                                    $allMonthIds[] = (int)$month;
                                }
                            }
                        }
                    }
                    $allMonthIds = array_unique($allMonthIds);
                    
                    // Get month names for the IDs
                    $monthNames = [];
                    foreach ($allMonthIds as $monthId) {
                        foreach ($availableMonths as $month) {
                            if ((int)$month['monthly_id'] === $monthId) {
                                $monthNames[] = $month['month_name'];
                                break;
                            }
                        }
                    }
                    
                    $result['months_used'] = $monthNames;
                    $result['month_ids_used'] = $allMonthIds;
                } else {
                    $result['months_used'] = [];
                    $result['month_ids_used'] = [];
                }
                
                // Clean up the raw data fields
                unset($result['months_calculated_all']);
                unset($result['monthly_averages']);
            }
            
            return [
                'status' => 'success',
                'data' => $results,
                'available_months' => $availableMonths
            ];
        } catch (PDOException $e) {
            error_log("Error fetching class semester exam scores: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Failed to fetch class semester exam scores'
            ];
        }
    }

    public function create($data) {
        try {
            // Validate required fields
            if (!isset($data['student_id']) || !isset($data['class_id']) || !isset($data['semester_id']) || 
                !isset($data['semester_exam_subject_id']) || !isset($data['score'])) {
                return [
                    'status' => 'error',
                    'message' => 'Missing required fields: student_id, class_id, semester_id, semester_exam_subject_id and score are required'
                ];
            }

            // Validate score range
            if ($data['score'] < 0 || $data['score'] > 10) {
                return [
                    'status' => 'error',
                    'message' => 'Score must be between 0 and 10'
                ];
            }

            // Check if the semester_exam_subject exists and get its assign_subject_grade_id
            $checkQuery = "SELECT ses.id, ses.assign_subject_grade_id
                          FROM tbl_semester_exam_subjects ses 
                          WHERE ses.id = :semester_exam_subject_id
                          AND ses.class_id = :class_id 
                          AND ses.semester_id = :semester_id
                          AND ses.isDeleted = 0";
              
            $stmt = $this->conn->prepare($checkQuery);
            $stmt->bindParam(':semester_exam_subject_id', $data['semester_exam_subject_id']);
            $stmt->bindParam(':class_id', $data['class_id']);
            $stmt->bindParam(':semester_id', $data['semester_id']);
            $stmt->execute();
              
            $examSubject = $stmt->fetch(PDO::FETCH_ASSOC);
              
            if (!$examSubject) {
                return [
                    'status' => 'error',
                    'message' => 'This subject is not assigned for exam in this class and semester'
                ];
            }
            
            $assign_subject_grade_id = $examSubject['assign_subject_grade_id'];

            // Check if score already exists
            $checkScoreQuery = "SELECT id FROM tbl_student_semester_exam_scores 
                               WHERE student_id = :student_id 
                               AND semester_exam_subject_id = :semester_exam_subject_id
                               AND isDeleted = 0";
              
            $stmt = $this->conn->prepare($checkScoreQuery);
            $stmt->bindParam(':student_id', $data['student_id']);
            $stmt->bindParam(':semester_exam_subject_id', $data['semester_exam_subject_id']);
            $stmt->execute();
              
            if ($stmt->fetch(PDO::FETCH_ASSOC)) {
                return [
                    'status' => 'error',
                    'message' => 'Score for this student and subject already exists'
                ];
            }

            // Process monthly data if available
            $monthly_average = null;
            $months_calculated = null;
            $months_count = 0;

            if (isset($data['monthly_ids']) && is_array($data['monthly_ids']) && !empty($data['monthly_ids'])) {
                // Convert array to comma-separated string
                $months_calculated = implode(',', $data['monthly_ids']);
                $months_count = count($data['monthly_ids']);
                
                // Calculate monthly average if months are selected
                if ($months_count > 0) {
                    $placeholders = str_repeat('?,', $months_count - 1) . '?';
                    $avg_query = "SELECT AVG(sms.score) as avg_score
                                 FROM tbl_student_monthly_score sms
                                 JOIN classroom_subject_monthly_score csms ON sms.classroom_subject_monthly_score_id = csms.classroom_subject_monthly_score_id
                                 WHERE sms.student_id = ?
                                 AND csms.class_id = ?
                                 AND csms.assign_subject_grade_id = ?
                                 AND csms.monthly_id IN ($placeholders)
                                 AND sms.isDeleted = 0
                                 AND csms.isDeleted = 0";
                    
                    $params = array_merge(
                        [$data['student_id'], $data['class_id'], $assign_subject_grade_id],
                        $data['monthly_ids']
                    );
                    
                    $avg_stmt = $this->conn->prepare($avg_query);
                    $avg_stmt->execute($params);
                    $result = $avg_stmt->fetch(PDO::FETCH_ASSOC);
                    $monthly_average = $result['avg_score'] ? floatval($result['avg_score']) : null;
                }
            }

            // Inside the create method, add debugging:
            if (isset($data['monthly_ids'])) {
                error_log("monthly_ids: " . json_encode($data['monthly_ids']));
                error_log("monthly_ids type: " . gettype($data['monthly_ids']));
            }

            // Insert the score
            $query = "INSERT INTO tbl_student_semester_exam_scores 
                     (student_id, class_id, semester_id, semester_exam_subject_id, assign_subject_grade_id, score, create_date,
                      monthly_average, months_calculated, months_count) 
                     VALUES 
                     (:student_id, :class_id, :semester_id, :semester_exam_subject_id, :assign_subject_grade_id, :score, NOW(),
                      :monthly_average, :months_calculated, :months_count)";
              
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':student_id', $data['student_id']);
            $stmt->bindParam(':class_id', $data['class_id']);
            $stmt->bindParam(':semester_id', $data['semester_id']);
            $stmt->bindParam(':semester_exam_subject_id', $data['semester_exam_subject_id']);
            $stmt->bindParam(':assign_subject_grade_id', $assign_subject_grade_id);
            $stmt->bindParam(':score', $data['score']);
            $stmt->bindParam(':monthly_average', $monthly_average);
            $stmt->bindParam(':months_calculated', $months_calculated);
            $stmt->bindParam(':months_count', $months_count);
              
            if ($stmt->execute()) {
                // Recalculate semester scores
                $this->recalculateSemesterScores();
                
                return [
                    'status' => 'success',
                    'message' => 'Score added successfully',
                    'id' => $this->conn->lastInsertId()
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Failed to add score'
                ];
            }
        } catch (PDOException $e) {
            error_log("Error adding semester exam score: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Database error occurred'
            ];
        }
    }

    public function update($id, $data) {
        try {
            // Get the existing record first to make sure it exists
            $checkQuery = "SELECT id, student_id, class_id, semester_id, semester_exam_subject_id, assign_subject_grade_id 
                           FROM tbl_student_semester_exam_scores 
                           WHERE id = :id AND isDeleted = 0";
            
            $stmt = $this->conn->prepare($checkQuery);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            $record = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$record) {
                return [
                    'status' => 'error',
                    'message' => 'Score record not found'
                ];
            }
            
            // Validate score if provided
            if (isset($data['score'])) {
                // Validate score range
                if ($data['score'] < 0 || $data['score'] > 10) {
                    return [
                        'status' => 'error',
                        'message' => 'Score must be between 0 and 10'
                    ];
                }
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Score is required'
                ];
            }
            
            // Process monthly data if available
            $updateMonthly = false;
            $monthly_average = null;
            $months_calculated = null;
            $months_count = 0;

            if (isset($data['monthly_ids']) && is_array($data['monthly_ids'])) {
                $updateMonthly = true;
                
                if (!empty($data['monthly_ids'])) {
                    // Convert array to comma-separated string
                    $months_calculated = implode(',', $data['monthly_ids']);
                    $months_count = count($data['monthly_ids']);
                    
                    // Calculate monthly average if months are selected
                    if ($months_count > 0) {
                        $placeholders = str_repeat('?,', $months_count - 1) . '?';
                        $avg_query = "SELECT AVG(sms.score) as avg_score
                                     FROM tbl_student_monthly_score sms
                                     JOIN classroom_subject_monthly_score csms ON sms.classroom_subject_monthly_score_id = csms.classroom_subject_monthly_score_id
                                     WHERE sms.student_id = ?
                                     AND csms.class_id = ?
                                     AND csms.assign_subject_grade_id = ?
                                     AND csms.monthly_id IN ($placeholders)
                                     AND sms.isDeleted = 0
                                     AND csms.isDeleted = 0";
                        
                        $params = array_merge(
                            [$record['student_id'], $record['class_id'], $record['assign_subject_grade_id']],
                            $data['monthly_ids']
                        );
                        
                        $avg_stmt = $this->conn->prepare($avg_query);
                        $avg_stmt->execute($params);
                        $result = $avg_stmt->fetch(PDO::FETCH_ASSOC);
                        $monthly_average = $result['avg_score'] ? floatval($result['avg_score']) : null;
                    }
                }
            }
            
            // Build the update query based on what should be updated
            $updateFields = ['score = :score'];
            $params = [':score' => $data['score'], ':id' => $id];
            
            if ($updateMonthly) {
                $updateFields[] = 'monthly_average = :monthly_average';
                $updateFields[] = 'months_calculated = :months_calculated';
                $updateFields[] = 'months_count = :months_count';
                
                $params[':monthly_average'] = $monthly_average;
                $params[':months_calculated'] = $months_calculated;
                $params[':months_count'] = $months_count;
            }
            
            $updateFieldsStr = implode(', ', $updateFields);
            $query = "UPDATE tbl_student_semester_exam_scores SET $updateFieldsStr WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            
            foreach ($params as $key => &$value) {
                $stmt->bindParam($key, $value);
            }
            
            if ($stmt->execute()) {
                // Recalculate semester scores
                $this->recalculateSemesterScores();
                
                return [
                    'status' => 'success',
                    'message' => 'Score updated successfully'
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Failed to update score'
                ];
            }
        } catch (PDOException $e) {
            error_log("Error updating semester exam score: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Database error occurred'
            ];
        }
    }

    public function delete($id) {
        try {
            $query = "UPDATE tbl_student_semester_exam_scores SET isDeleted = 1 WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            
            if ($stmt->execute()) {
                // Call the stored procedure to recalculate semester scores
                $this->recalculateSemesterScores();
                
                return [
                    'status' => 'success',
                    'message' => 'Semester exam score deleted successfully'
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Failed to delete semester exam score'
                ];
            }
        } catch (PDOException $e) {
            error_log("Error deleting semester exam score: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Database error occurred'
            ];
        }
    }

    public function recalculateSemesterScores() {
        try {
            // Delete all previous semester scores
            $deleteQuery = "DELETE FROM tbl_student_semester_score";
            $this->conn->exec($deleteQuery);
            
            // Get all students with semester exam scores
            $studentsQuery = "SELECT DISTINCT 
                               sses.student_id, 
                               sses.class_id, 
                               sses.semester_id
                              FROM tbl_student_semester_exam_scores sses
                              WHERE sses.isDeleted = 0";
            $studentsStmt = $this->conn->prepare($studentsQuery);
            $studentsStmt->execute();
            $students = $studentsStmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($students as $student) {
                $studentId = $student['student_id'];
                $classId = $student['class_id'];
                $semesterId = $student['semester_id'];
                
                // Get all exam subjects for this student, semester and class
                $scoresQuery = "SELECT 
                                 AVG(sses.score) as exam_average,
                                 COUNT(sses.id) as subjects_count,
                                 AVG(sses.monthly_average) as monthly_average,
                                 GROUP_CONCAT(DISTINCT sses.months_calculated) as all_months,
                                 SUM(sses.months_count) as total_months_count
                                FROM tbl_student_semester_exam_scores sses
                                WHERE sses.student_id = :student_id
                                AND sses.class_id = :class_id
                                AND sses.semester_id = :semester_id
                                AND sses.isDeleted = 0";
                
                $scoresStmt = $this->conn->prepare($scoresQuery);
                $scoresStmt->bindParam(':student_id', $studentId);
                $scoresStmt->bindParam(':class_id', $classId);
                $scoresStmt->bindParam(':semester_id', $semesterId);
                $scoresStmt->execute();
                
                $scoreData = $scoresStmt->fetch(PDO::FETCH_ASSOC);
                
                if (!$scoreData || !$scoreData['exam_average']) {
                    continue; // Skip if no scores found
                }
                
                $examAverage = $scoreData['exam_average'];
                $subjectsCount = $scoreData['subjects_count'];
                $monthlyAverage = $scoreData['monthly_average'];
                $monthsCalculated = $scoreData['all_months'];
                $monthsCount = $scoreData['total_months_count'] > 0 ? $scoreData['total_months_count'] / $subjectsCount : 0;
                
                // Calculate final score
                $finalScore = null;
                $calculationNote = null;
                
                if ($monthlyAverage && $monthlyAverage > 0) {
                    // If we have monthly averages, use average of monthly + exam
                    $finalScore = ($monthlyAverage + $examAverage) / 2;
                    $calculationNote = sprintf(
                        "ពិន្ទុសរុប = (ពិន្ទុប្រចាំខែ + ពិន្ទុប្រឡងឆមាស) / 2 = (%.2f + %.2f) / 2 = %.2f",
                        $monthlyAverage,
                        $examAverage,
                        $finalScore
                    );
                } else {
                    // If no monthly averages, just use exam average
                    $finalScore = $examAverage;
                    $calculationNote = sprintf("ពិន្ទុសរុប = ពិន្ទុប្រឡង (%.2f)", $examAverage);
                }
                
                // Insert into tbl_student_semester_score
                $insertQuery = "INSERT INTO tbl_student_semester_score (
                                 student_id, 
                                 class_id,
                                 semester_id, 
                                 monthly_average,
                                 months_counted,
                                 semester_exam_score,
                                 exam_subjects_count,
                                 final_score,
                                 calculation_note
                               ) VALUES (
                                 :student_id,
                                 :class_id,
                                 :semester_id,
                                 :monthly_average,
                                 :months_counted,
                                 :semester_exam_score,
                                 :exam_subjects_count,
                                 :final_score,
                                 :calculation_note
                               )";
                
                $insertStmt = $this->conn->prepare($insertQuery);
                $insertStmt->bindParam(':student_id', $studentId);
                $insertStmt->bindParam(':class_id', $classId);
                $insertStmt->bindParam(':semester_id', $semesterId);
                $insertStmt->bindParam(':monthly_average', $monthlyAverage);
                $insertStmt->bindParam(':months_counted', $monthsCount);
                $insertStmt->bindParam(':semester_exam_score', $examAverage);
                $insertStmt->bindParam(':exam_subjects_count', $subjectsCount);
                $insertStmt->bindParam(':final_score', $finalScore);
                $insertStmt->bindParam(':calculation_note', $calculationNote);
                
                $insertStmt->execute();
            }
            
            return true;
        } catch (PDOException $e) {
            error_log("Error recalculating semester scores: " . $e->getMessage());
            return false;
        }
    }

    public function getAvailableMonthsForClass($class_id) {
        try {
            // ទាញយកខែដែលមានពិន្ទុរួចហើយសម្រាប់ថ្នាក់ជាក់លាក់
            $query = "SELECT DISTINCT 
                        m.monthly_id,
                        m.month_name
                    FROM tbl_monthly m
                    JOIN classroom_subject_monthly_score csms ON m.monthly_id = csms.monthly_id
                    JOIN tbl_student_monthly_score sms ON csms.classroom_subject_monthly_score_id = sms.classroom_subject_monthly_score_id
                    WHERE csms.class_id = :class_id
                    AND csms.isDeleted = 0
                    AND sms.isDeleted = 0
                    ORDER BY m.monthly_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':class_id', $class_id);
            $stmt->execute();
            
            $months = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // បន្ថែមពត៌មានចំនួនសិស្សបានដាក់ពិន្ទុសម្រាប់ខែនីមួយៗ
            foreach ($months as &$month) {
                $countQuery = "SELECT COUNT(DISTINCT sms.student_id) as student_count
                              FROM tbl_student_monthly_score sms
                              JOIN classroom_subject_monthly_score csms ON csms.classroom_subject_monthly_score_id = sms.classroom_subject_monthly_score_id
                              WHERE csms.monthly_id = :monthly_id
                              AND csms.class_id = :class_id
                              AND sms.isDeleted = 0
                              AND csms.isDeleted = 0";
                
                $countStmt = $this->conn->prepare($countQuery);
                $countStmt->bindParam(':monthly_id', $month['monthly_id']);
                $countStmt->bindParam(':class_id', $class_id);
                $countStmt->execute();
                
                $result = $countStmt->fetch(PDO::FETCH_ASSOC);
                $month['student_count'] = $result['student_count'];
                
                // បន្ថែមពត៌មានចំនួនមុខវិជ្ជា
                $subjectQuery = "SELECT COUNT(DISTINCT csms.assign_subject_grade_id) as subject_count
                               FROM classroom_subject_monthly_score csms
                               WHERE csms.monthly_id = :monthly_id
                               AND csms.class_id = :class_id
                               AND csms.isDeleted = 0";
                
                $subjectStmt = $this->conn->prepare($subjectQuery);
                $subjectStmt->bindParam(':monthly_id', $month['monthly_id']);
                $subjectStmt->bindParam(':class_id', $class_id);
                $subjectStmt->execute();
                
                $result = $subjectStmt->fetch(PDO::FETCH_ASSOC);
                $month['subject_count'] = $result['subject_count'];
            }
            
            return [
                'status' => 'success',
                'data' => $months
            ];
        } catch (PDOException $e) {
            error_log("Error fetching available months: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Failed to fetch available months'
            ];
        }
    }

    /**
     * ទាញយកពិន្ទុប្រចាំខែសម្រាប់សិស្សម្នាក់និងមុខវិជ្ជាជាក់លាក់
     * 
     * @param int $student_id អត្តលេខសិស្ស
     * @param int $class_id អត្តលេខថ្នាក់
     * @param int $assign_subject_grade_id អត្តលេខមុខវិជ្ជាដែលកំណត់ឱ្យថ្នាក់
     * @return array ព័ត៌មានលម្អិតអំពីពិន្ទុប្រចាំខែសម្រាប់សិស្សនិងមុខវិជ្ជានេះ
     */
    public function getMonthlyScoresForSubject($student_id, $class_id, $assign_subject_grade_id) {
        try {
            $query = "SELECT 
                        m.monthly_id,
                        m.month_name,
                        sms.score,
                        csms.classroom_subject_monthly_score_id
                      FROM tbl_monthly m
                      JOIN classroom_subject_monthly_score csms ON m.monthly_id = csms.monthly_id
                      JOIN tbl_student_monthly_score sms ON csms.classroom_subject_monthly_score_id = sms.classroom_subject_monthly_score_id
                      WHERE sms.student_id = :student_id
                      AND csms.class_id = :class_id
                      AND csms.assign_subject_grade_id = :assign_subject_grade_id
                      AND csms.isDeleted = 0
                      AND sms.isDeleted = 0
                      ORDER BY m.monthly_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':student_id', $student_id);
            $stmt->bindParam(':class_id', $class_id);
            $stmt->bindParam(':assign_subject_grade_id', $assign_subject_grade_id);
            $stmt->execute();
            
            $scores = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // បើមានពិន្ទុដែលបានរកឃើញ គណនាមធ្យមភាគ
            $average = null;
            if (count($scores) > 0) {
                $totalScore = 0;
                foreach ($scores as $score) {
                    $totalScore += $score['score'];
                }
                $average = $totalScore / count($scores);
            }
            
            return [
                'status' => 'success',
                'data' => [
                    'scores' => $scores,
                    'average' => $average,
                    'count' => count($scores)
                ]
            ];
        } catch (PDOException $e) {
            error_log("Error fetching monthly scores for subject: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Failed to fetch monthly scores'
            ];
        }
    }

    /**
     * Get all semester exam scores with monthly average calculations for a specific class
     * 
     * @param int $class_id The class ID
     * @param int $semester_id The semester ID
     * @return array Result data with scores and monthly averages
     */
    public function getClassSemesterExamScoresWithMonthly($class_id, $semester_id) {
        try {
            // First get all semester exam scores
            $query = "SELECT 
                sses.id,
                sses.student_id,
                si.student_name,
                sses.class_id,
                sses.semester_id,
                sses.semester_exam_subject_id,
                sses.assign_subject_grade_id,
                s.subject_name,
                sses.score as exam_score,
                sses.monthly_average,
                sses.months_calculated,
                sses.months_count
            FROM tbl_student_semester_exam_scores sses
            JOIN tbl_student_info si ON sses.student_id = si.student_id
            JOIN tbl_semester_exam_subjects ses ON sses.semester_exam_subject_id = ses.id
            JOIN tbl_assign_subject_grade asg ON ses.assign_subject_grade_id = asg.assign_subject_grade_id
            JOIN tbl_subject s ON asg.subject_code = s.subject_code
            WHERE sses.class_id = :class_id
            AND sses.semester_id = :semester_id
            AND sses.isDeleted = 0";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':class_id', $class_id);
            $stmt->bindParam(':semester_id', $semester_id);
            $stmt->execute();
            
            $scores = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // For each score, calculate monthly average if months are selected but no average exists
            foreach ($scores as &$score) {
                if (!empty($score['months_calculated']) && empty($score['monthly_average'])) {
                    $monthIds = explode(',', $score['months_calculated']);
                    
                    if (!empty($monthIds)) {
                        $placeholders = str_repeat('?,', count($monthIds) - 1) . '?';
                        
                        $avgQuery = "SELECT AVG(sms.score) as avg_score
                                    FROM tbl_student_monthly_score sms
                                    JOIN classroom_subject_monthly_score csms ON sms.classroom_subject_monthly_score_id = csms.classroom_subject_monthly_score_id
                                    WHERE sms.student_id = ?
                                    AND csms.class_id = ?
                                    AND csms.assign_subject_grade_id = ?
                                    AND csms.monthly_id IN ($placeholders)
                                    AND sms.isDeleted = 0";
                        
                        $params = array_merge(
                            [$score['student_id'], $score['class_id'], $score['assign_subject_grade_id']],
                            $monthIds
                        );
                        
                        $avgStmt = $this->conn->prepare($avgQuery);
                        $avgStmt->execute($params);
                        $result = $avgStmt->fetch(PDO::FETCH_ASSOC);
                        
                        $score['monthly_average'] = $result['avg_score'] ? floatval($result['avg_score']) : null;
                        
                        // Update the database with the calculated monthly average
                        $updateQuery = "UPDATE tbl_student_semester_exam_scores 
                                       SET monthly_average = ? 
                                       WHERE id = ?";
                        $updateStmt = $this->conn->prepare($updateQuery);
                        $updateStmt->execute([$score['monthly_average'], $score['id']]);
                    }
                }
            }
            
            return [
                'status' => 'success',
                'data' => $scores
            ];
            
        } catch (PDOException $e) {
            error_log("Error in getClassSemesterExamScoresWithMonthly: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }
}
