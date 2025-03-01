<?php
require_once __DIR__ . '/../config/database.php';

// Silence linter warning about error_log
if (!function_exists('error_log')) {
    function error_log($message) {
        // fallback to echo if error_log doesn't exist
        echo $message . "\n";
    }
}

class StudentMonthlyScore {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function fetchAll() {
        try {
            $query = "SELECT 
                s.student_id,
                s.student_name,
                c.class_id,
                c.class_name,
                g.grade_id,
                g.grade_name,
                m.monthly_id,
                m.month_name,
                sms.student_monthly_score_id,
                sms.score,
                sms.create_date,
                asg.assign_subject_grade_id,
                sub.subject_code,
                sub.subject_name
            FROM tbl_student_info s
            INNER JOIN tbl_classroom c ON s.class_id = c.class_id
            INNER JOIN tbl_grade g ON c.grade_id = g.grade_id
            LEFT JOIN tbl_student_monthly_score sms ON s.student_id = sms.student_id
            LEFT JOIN tbl_assign_subject_grade asg ON sms.assign_subject_grade_id = asg.assign_subject_grade_id
            LEFT JOIN tbl_subject sub ON asg.subject_code = sub.subject_code
            LEFT JOIN tbl_monthly m ON sms.monthly_id = m.monthly_id
            WHERE s.isDeleted = 0
            ORDER BY s.student_id, sub.subject_code";

            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Group results by student
            $groupedResults = [];
            foreach ($results as $row) {
                $studentId = $row['student_id'];
                
                if (!isset($groupedResults[$studentId])) {
                    $groupedResults[$studentId] = [
                        'student_id' => $studentId,
                        'student_name' => $row['student_name'],
                        'class_id' => $row['class_id'],
                        'class_name' => $row['class_name'],
                        'grade_name' => $row['grade_name'],
                        'scores' => []
                    ];
                }

                if ($row['subject_code'] && $row['score'] !== null) {
                    $groupedResults[$studentId]['scores'][] = [
                        'subject_code' => $row['subject_code'],
                        'subject_name' => $row['subject_name'],
                        'score' => $row['score'],
                        'month_name' => $row['month_name'],
                        'monthly_id' => $row['monthly_id'],
                        'create_date' => $row['create_date']
                    ];
                }
            }

            return [
                'status' => 'success',
                'data' => array_values($groupedResults)
            ];

        } catch (Exception $e) {
            error_log("Error in fetchAll: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Failed to fetch student monthly scores'
            ];
        }
    }

    public function create($data) {
        try {
            // Validate student
            $studentQuery = "SELECT student_id 
                           FROM tbl_student_info 
                           WHERE student_id = :student_id 
                           AND isDeleted = 0";
            
            $stmt = $this->conn->prepare($studentQuery);
            $stmt->bindParam(':student_id', $data['student_id']);
            $stmt->execute();

            if (!$stmt->fetch()) {
                return [
                    'status' => 'error',
                    'message' => 'Student not found'
                ];
            }

            // Check for duplicate score
            $checkQuery = "SELECT student_monthly_score_id 
                         FROM tbl_student_monthly_score 
                         WHERE student_id = :student_id 
                         AND assign_subject_grade_id = :assign_subject_grade_id 
                         AND monthly_id = :monthly_id 
                         AND isDeleted = 0";
            
            $stmt = $this->conn->prepare($checkQuery);
            $stmt->bindParam(':student_id', $data['student_id']);
            $stmt->bindParam(':assign_subject_grade_id', $data['assign_subject_grade_id']);
            $stmt->bindParam(':monthly_id', $data['monthly_id']);
            $stmt->execute();

            if ($stmt->fetch()) {
                return [
                    'status' => 'error',
                    'message' => 'Score already exists for this student'
                ];
            }

            // Validate score
            if (!isset($data['score']) || $data['score'] < 0 || $data['score'] > 100) {
                return [
                    'status' => 'error',
                    'message' => 'Score must be between 0 and 100'
                ];
            }

            // Insert score
            $insertQuery = "INSERT INTO tbl_student_monthly_score 
                          (student_id, assign_subject_grade_id, monthly_id, score) 
                          VALUES (:student_id, :assign_subject_grade_id, :monthly_id, :score)";
            
            $stmt = $this->conn->prepare($insertQuery);
            $stmt->bindParam(':student_id', $data['student_id']);
            $stmt->bindParam(':assign_subject_grade_id', $data['assign_subject_grade_id']);
            $stmt->bindParam(':monthly_id', $data['monthly_id']);
            $stmt->bindParam(':score', $data['score']);

            if ($stmt->execute()) {
                return [
                    'status' => 'success',
                    'message' => 'Score added successfully',
                    'data' => [
                        'id' => $this->conn->lastInsertId()
                    ]
                ];
            }

            return [
                'status' => 'error',
                'message' => 'Failed to add score'
            ];

        } catch (PDOException $e) {
            error_log("Error creating score: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Database error occurred'
            ];
        }
    }

    public function update($id, $data) {
        try {
            // Validate score
            if (!isset($data['score']) || $data['score'] < 0 || $data['score'] > 100) {
                return [
                    'status' => 'error',
                    'message' => 'Score must be between 0 and 100'
                ];
            }

            // Update score
            $query = "UPDATE tbl_student_monthly_score 
                     SET score = :score 
                     WHERE student_monthly_score_id = :id 
                     AND isDeleted = 0";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':score', $data['score']);
            $stmt->bindParam(':id', $id);

            if ($stmt->execute()) {
                return [
                    'status' => 'success',
                    'message' => 'Score updated successfully'
                ];
            }

            return [
                'status' => 'error',
                'message' => 'Failed to update score'
            ];

        } catch (PDOException $e) {
            error_log("Error updating score: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Database error occurred'
            ];
        }
    }

    public function delete($id) {
        try {
            $this->conn->beginTransaction();

            // Get student_id and monthly_id from the main score record
            $getInfoQuery = "SELECT student_id, monthly_id 
                           FROM tbl_student_monthly_score 
                           WHERE student_monthly_score_id = :id";
            
            $stmt = $this->conn->prepare($getInfoQuery);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $scoreInfo = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($scoreInfo) {
                // Soft delete main subject score
                $query = "UPDATE tbl_student_monthly_score 
                         SET isDeleted = 1 
                         WHERE student_monthly_score_id = :id";
                
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':id', $id);

                if (!$stmt->execute()) {
                    $this->conn->rollBack();
                    return [
                        'status' => 'error',
                        'message' => 'Failed to delete main subject score'
                    ];
                }

                // Soft delete related sub-subject scores
                $deleteSubScores = "UPDATE tbl_student_sub_score 
                                  SET isDeleted = 1 
                                  WHERE student_id = :student_id 
                                  AND monthly_id = :monthly_id";
                
                $stmt = $this->conn->prepare($deleteSubScores);
                $stmt->bindParam(':student_id', $scoreInfo['student_id']);
                $stmt->bindParam(':monthly_id', $scoreInfo['monthly_id']);
                $stmt->execute();
            }

            $this->conn->commit();
            return [
                'status' => 'success',
                'message' => 'All scores deleted successfully'
            ];

        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Error deleting scores: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Database error occurred'
            ];
        }
    }

    public function fetchById($id) {
        try {
            $query = "SELECT 
                        sms.*,
                        s.student_name,
                        t.teacher_name,
                        c.class_name,
                        m.month_name,
                        sub.subject_name
                    FROM tbl_student_monthly_score sms
                    LEFT JOIN tbl_student_info s ON sms.student_id = s.student_id
                    LEFT JOIN tbl_teacher t ON sms.teacher_id = t.teacher_id
                    LEFT JOIN tbl_classroom c ON s.class_id = c.class_id
                    LEFT JOIN tbl_monthly m ON sms.monthly_id = m.monthly_id
                    LEFT JOIN tbl_assign_subject_grade asg ON sms.assign_subject_grade_id = asg.assign_subject_grade_id
                    LEFT JOIN tbl_subject sub ON asg.subject_code = sub.subject_code
                    WHERE sms.student_monthly_score_id = :student_monthly_score_id 
                    AND sms.isDeleted = 0";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':student_monthly_score_id', $id);
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
                'message' => 'Monthly score not found'
            ];

        } catch (PDOException $e) {
            error_log("Error fetching monthly score: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Database error occurred'
            ];
        }
    }

    public function getStudentMonthlyScores($student_id, $monthly_id, $class_id) {
        try {
            $query = "SELECT 
                s.student_id,
                s.student_name,
                c.class_name,
                g.grade_name,
                m.month_name,
                sub.subject_name,
                sms.score,
                sms.create_date
            FROM tbl_student_monthly_score sms
            INNER JOIN tbl_student_info s ON sms.student_id = s.student_id
            INNER JOIN tbl_classroom c ON s.class_id = c.class_id
            INNER JOIN tbl_grade g ON c.grade_id = g.grade_id
            INNER JOIN tbl_assign_subject_grade asg ON sms.assign_subject_grade_id = asg.assign_subject_grade_id
            INNER JOIN tbl_subject sub ON asg.subject_code = sub.subject_code
            INNER JOIN tbl_monthly m ON sms.monthly_id = m.monthly_id
            WHERE sms.student_id = :student_id
            AND sms.monthly_id = :monthly_id
            AND c.class_id = :class_id
            AND sms.isDeleted = 0
            ORDER BY sub.subject_name";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':student_id', $student_id);
            $stmt->bindParam(':monthly_id', $monthly_id);
            $stmt->bindParam(':class_id', $class_id);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($result) {
                return [
                    'status' => 'success',
                    'data' => $result
                ];
            }

            return [
                'status' => 'error',
                'message' => 'No scores found'
            ];

        } catch (PDOException $e) {
            error_log("Error in getStudentMonthlyScores: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Database error occurred'
            ];
        }
    }

    public function getClassMonthlyScores($class_id, $monthly_id) {
        try {
            $query = "SELECT 
                s.student_id,
                s.student_name,
                c.class_name,
                g.grade_name,
                m.month_name,
                sub.subject_name,
                sms.score,
                sms.create_date
            FROM tbl_student_monthly_score sms
            INNER JOIN tbl_student_info s ON sms.student_id = s.student_id
            INNER JOIN tbl_classroom c ON s.class_id = c.class_id
            INNER JOIN tbl_grade g ON c.grade_id = g.grade_id
            INNER JOIN tbl_assign_subject_grade asg ON sms.assign_subject_grade_id = asg.assign_subject_grade_id
            INNER JOIN tbl_subject sub ON asg.subject_code = sub.subject_code
            INNER JOIN tbl_monthly m ON sms.monthly_id = m.monthly_id
            WHERE c.class_id = :class_id 
            AND sms.monthly_id = :monthly_id
            AND sms.isDeleted = 0
            ORDER BY s.student_name, sub.subject_name";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':class_id', $class_id);
            $stmt->bindParam(':monthly_id', $monthly_id);
            $stmt->execute();

            return [
                'status' => 'success',
                'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ];

        } catch (PDOException $e) {
            error_log("Error in getClassMonthlyScores: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Database error occurred'
            ];
        }
    }

    public function getMonthlyScoresByFilters($filters) {
        try {
            $query = "SELECT 
                        sms.student_monthly_score_id,
                        sms.score,
                        s.student_name,
                        t.teacher_name,
                        c.class_name,
                        m.month_name,
                        sub.subject_name
                    FROM tbl_student_monthly_score sms
                    LEFT JOIN tbl_student_info s ON sms.student_id = s.student_id
                    LEFT JOIN tbl_teacher t ON sms.teacher_id = t.teacher_id
                    LEFT JOIN tbl_classroom c ON s.class_id = c.class_id
                    LEFT JOIN tbl_monthly m ON sms.monthly_id = m.monthly_id
                    LEFT JOIN tbl_assign_subject_grade asg ON sms.assign_subject_grade_id = asg.assign_subject_grade_id
                    LEFT JOIN tbl_subject sub ON asg.subject_code = sub.subject_code
                    WHERE sms.isDeleted = 0";

            $params = [];
            
            if (!empty($filters['class_id'])) {
                $query .= " AND c.class_id = :class_id";
                $params[':class_id'] = $filters['class_id'];
            }
            if (!empty($filters['monthly_id'])) {
                $query .= " AND sms.monthly_id = :monthly_id";
                $params[':monthly_id'] = $filters['monthly_id'];
            }
            if (!empty($filters['student_id'])) {
                $query .= " AND sms.student_id = :student_id";
                $params[':student_id'] = $filters['student_id'];
            }

            $stmt = $this->conn->prepare($query);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();

            return [
                'status' => 'success',
                'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ];

        } catch (PDOException $e) {
            error_log("Error in getMonthlyScoresByFilters: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Database error occurred'
            ];
        }
    }

    public function getAllScoresGrouped() {
        try {
            $query = "SELECT 
                s.subject_name,
                GROUP_CONCAT(
                    CONCAT(s.subject_name, ': ', sms.score)
                    ORDER BY s.subject_name
                ) AS subject_scores,
                si.student_name,
                c.class_name,
                m.month_name
            FROM tbl_student_monthly_score sms
            JOIN tbl_student_info si ON sms.student_id = si.student_id
            JOIN tbl_classroom c ON si.class_id = c.class_id
            JOIN tbl_monthly m ON sms.monthly_id = m.monthly_id
            JOIN tbl_assign_subject_grade asg ON sms.assign_subject_grade_id = asg.assign_subject_grade_id
            JOIN tbl_subject s ON asg.subject_code = s.subject_code
            WHERE sms.isDeleted = 0
            GROUP BY s.subject_name, si.student_name, c.class_name, m.month_name
            ORDER BY s.subject_name";

            $stmt = $this->conn->prepare($query);
            $stmt->execute();

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($result) {
                return [
                    'status' => 'success',
                    'data' => $result
                ];
            }

            return [
                'status' => 'error',
                'message' => 'No scores found'
            ];

        } catch (PDOException $e) {
            error_log("Error in getAllScoresGrouped: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Database error occurred'
            ];
        }
    }

    public function getStudentScoresOrdered($student_id) {
        try {
            // Check if the student exists
            $studentQuery = "SELECT student_id, student_name FROM tbl_student_info 
                           WHERE student_id = :student_id AND isDeleted = 0";
            $stmt = $this->conn->prepare($studentQuery);
            $stmt->bindParam(':student_id', $student_id);
            $stmt->execute();
            $studentResult = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$studentResult) {
                return [
                    'status' => 'error',
                    'message' => 'Student not found'
                ];
            }

            $query = "SELECT 
                si.student_id,
                si.student_name,
                c.class_name,
                m.month_name,
                s.subject_name,
                sms.score,
                sms.create_date
            FROM tbl_student_monthly_score sms
            JOIN tbl_student_info si ON sms.student_id = si.student_id
            JOIN tbl_classroom c ON si.class_id = c.class_id
            JOIN tbl_monthly m ON sms.monthly_id = m.monthly_id
            JOIN tbl_assign_subject_grade asg ON sms.assign_subject_grade_id = asg.assign_subject_grade_id
            JOIN tbl_subject s ON asg.subject_code = s.subject_code
            WHERE sms.student_id = :student_id 
            AND sms.isDeleted = 0
            ORDER BY m.month_name ASC, s.subject_name ASC";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':student_id', $student_id);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($result) {
                return [
                    'status' => 'success',
                    'data' => $result
                ];
            }

            return [
                'status' => 'error',
                'message' => 'No scores found for this student'
            ];

        } catch (PDOException $e) {
            error_log("Error in getStudentScoresOrdered: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Database error occurred'
            ];
        }
    }
}
