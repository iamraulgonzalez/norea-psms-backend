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
                csms.classroom_subject_monthly_score_id,
                sub.subject_code,
                sub.subject_name
            FROM tbl_student_info s
            INNER JOIN tbl_study st ON s.student_id = st.student_id AND st.status = 'active' AND st.isDeleted = 0
            INNER JOIN tbl_classroom c ON st.class_id = c.class_id
            INNER JOIN tbl_grade g ON c.grade_id = g.grade_id
            LEFT JOIN tbl_student_monthly_score sms ON s.student_id = sms.student_id
            LEFT JOIN classroom_subject_monthly_score csms ON sms.classroom_subject_monthly_score_id = csms.classroom_subject_monthly_score_id
            LEFT JOIN tbl_assign_subject_grade asg ON csms.assign_subject_grade_id = asg.assign_subject_grade_id
            LEFT JOIN tbl_subject sub ON asg.subject_code = sub.subject_code
            LEFT JOIN tbl_monthly m ON csms.monthly_id = m.monthly_id
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
            error_log("Creating score with data: " . json_encode($data));

            // Validate required fields
            $requiredFields = ['student_id', 'classroom_subject_monthly_score_id', 'monthly_id', 'score'];
            foreach ($requiredFields as $field) {
                if (!isset($data[$field])) {
                    error_log("Missing required field: {$field}");
                    return [
                        'status' => 'error',
                        'message' => "Missing required field: {$field}"
                    ];
                }
            }

            // Validate student
            $studentQuery = "SELECT student_id 
                           FROM tbl_student_info 
                           WHERE student_id = :student_id 
                           AND isDeleted = 0";
            
            $stmt = $this->conn->prepare($studentQuery);
            $stmt->bindParam(':student_id', $data['student_id']);
            $stmt->execute();

            if (!$stmt->fetch()) {
                error_log("Student not found with ID: " . $data['student_id']);
                return [
                    'status' => 'error',
                    'message' => 'Student not found'
                ];
            }

            // Validate classroom_subject_monthly_score exists
            $csmsQuery = "SELECT classroom_subject_monthly_score_id 
                         FROM classroom_subject_monthly_score 
                         WHERE classroom_subject_monthly_score_id = :classroom_subject_monthly_score_id 
                         AND isDeleted = 0";
            
            $stmt = $this->conn->prepare($csmsQuery);
            $stmt->bindParam(':classroom_subject_monthly_score_id', $data['classroom_subject_monthly_score_id']);
            $stmt->execute();

            if (!$stmt->fetch()) {
                error_log("Classroom subject monthly score not found with ID: " . $data['classroom_subject_monthly_score_id']);
                return [
                    'status' => 'error',
                    'message' => 'Classroom subject monthly score not found'
                ];
            }

            // Check for duplicate score
            $checkQuery = "SELECT sms.student_monthly_score_id 
                         FROM tbl_student_monthly_score sms
                         INNER JOIN classroom_subject_monthly_score csms 
                             ON sms.classroom_subject_monthly_score_id = csms.classroom_subject_monthly_score_id
                         WHERE sms.student_id = :student_id 
                         AND sms.classroom_subject_monthly_score_id = :classroom_subject_monthly_score_id 
                         AND csms.monthly_id = :monthly_id 
                         AND sms.isDeleted = 0";
            
            $stmt = $this->conn->prepare($checkQuery);
            $stmt->bindParam(':student_id', $data['student_id']);
            $stmt->bindParam(':classroom_subject_monthly_score_id', $data['classroom_subject_monthly_score_id']);
            $stmt->bindParam(':monthly_id', $data['monthly_id']);
            $stmt->execute();

            if ($stmt->fetch()) {
                error_log("Duplicate score found for student_id: " . $data['student_id'] . 
                         ", classroom_subject_monthly_score_id: " . $data['classroom_subject_monthly_score_id'] . 
                         ", monthly_id: " . $data['monthly_id']);
                return [
                    'status' => 'error',
                    'message' => 'Score already exists for this student'
                ];
            }

            // Validate score
            if (!isset($data['score']) || $data['score'] < 0 || $data['score'] > 100) {
                error_log("Invalid score value: " . ($data['score'] ?? 'null'));
                return [
                    'status' => 'error',
                    'message' => 'Score must be between 0 and 100'
                ];
            }

            // Insert score
            $insertQuery = "INSERT INTO tbl_student_monthly_score 
                          (student_id, classroom_subject_monthly_score_id, score) 
                          VALUES (:student_id, :classroom_subject_monthly_score_id, :score)";
            
            $stmt = $this->conn->prepare($insertQuery);
            $stmt->bindParam(':student_id', $data['student_id']);
            $stmt->bindParam(':classroom_subject_monthly_score_id', $data['classroom_subject_monthly_score_id']);
            $stmt->bindParam(':score', $data['score']);

            if ($stmt->execute()) {
                error_log("Score inserted successfully with ID: " . $this->conn->lastInsertId());
                return [
                    'status' => 'success',
                    'message' => 'Score added successfully',
                    'data' => [
                        'id' => $this->conn->lastInsertId()
                    ]
                ];
            }

            error_log("Failed to insert score");
            return [
                'status' => 'error',
                'message' => 'Failed to add score'
            ];

        } catch (PDOException $e) {
            error_log("Database error in create score: " . $e->getMessage());
            error_log("Error code: " . $e->getCode());
            error_log("SQL state: " . $e->errorInfo[0]);
            error_log("Driver error code: " . $e->errorInfo[1]);
            error_log("Driver error message: " . $e->errorInfo[2]);
            return [
                'status' => 'error',
                'message' => 'Database error occurred: ' . $e->getMessage()
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
                    LEFT JOIN tbl_study st ON s.student_id = st.student_id AND st.status = 'active' AND st.isDeleted = 0
                    LEFT JOIN tbl_classroom c ON st.class_id = c.class_id
                    LEFT JOIN tbl_monthly m ON sms.monthly_id = m.monthly_id
                    LEFT JOIN classroom_subject_monthly_score csms ON sms.classroom_subject_monthly_score_id = csms.classroom_subject_monthly_score_id
                    LEFT JOIN tbl_assign_subject_grade asg ON csms.assign_subject_grade_id = asg.assign_subject_grade_id
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
            INNER JOIN tbl_study st ON s.student_id = st.student_id AND st.status = 'active' AND st.isDeleted = 0
            INNER JOIN tbl_classroom c ON st.class_id = c.class_id
            INNER JOIN tbl_grade g ON c.grade_id = g.grade_id
            INNER JOIN classroom_subject_monthly_score csms ON sms.classroom_subject_monthly_score_id = csms.classroom_subject_monthly_score_id
            INNER JOIN tbl_assign_subject_grade asg ON csms.assign_subject_grade_id = asg.assign_subject_grade_id
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
            INNER JOIN tbl_study st ON s.student_id = st.student_id AND st.status = 'active' AND st.isDeleted = 0
            INNER JOIN tbl_classroom c ON st.class_id = c.class_id
            INNER JOIN tbl_grade g ON c.grade_id = g.grade_id
            INNER JOIN classroom_subject_monthly_score csms ON sms.classroom_subject_monthly_score_id = csms.classroom_subject_monthly_score_id
            INNER JOIN tbl_assign_subject_grade asg ON csms.assign_subject_grade_id = asg.assign_subject_grade_id
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
                    LEFT JOIN tbl_study st ON s.student_id = st.student_id AND st.status = 'active' AND st.isDeleted = 0
                    LEFT JOIN tbl_classroom c ON st.class_id = c.class_id
                    LEFT JOIN tbl_monthly m ON sms.monthly_id = m.monthly_id
                    LEFT JOIN classroom_subject_monthly_score csms ON sms.classroom_subject_monthly_score_id = csms.classroom_subject_monthly_score_id
                    LEFT JOIN tbl_assign_subject_grade asg ON csms.assign_subject_grade_id = asg.assign_subject_grade_id
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
            JOIN classroom_subject_monthly_score csms ON sms.classroom_subject_monthly_score_id = csms.classroom_subject_monthly_score_id
            JOIN tbl_assign_subject_grade asg ON csms.assign_subject_grade_id = asg.assign_subject_grade_id
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
            JOIN classroom_subject_monthly_score csms ON sms.classroom_subject_monthly_score_id = csms.classroom_subject_monthly_score_id
            JOIN tbl_assign_subject_grade asg ON csms.assign_subject_grade_id = asg.assign_subject_grade_id
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

    public function getStudentScoresByClass($class_id) {
        try {
            // First get all students in the class with their basic info
            $query = "SELECT 
                s.student_id,
                s.student_name,
                c.class_name,
                g.grade_name,
                csms.classroom_subject_monthly_score_id,
                sub.subject_name,
                m.month_name,
                COALESCE(sms.student_monthly_score_id, NULL) as student_monthly_score_id,
                COALESCE(sms.score, NULL) as score,
                sms.create_date
            FROM tbl_student_info s
            INNER JOIN tbl_study st ON s.student_id = st.student_id AND st.status = 'active' AND st.isDeleted = 0
            INNER JOIN tbl_classroom c ON st.class_id = c.class_id
            INNER JOIN tbl_grade g ON c.grade_id = g.grade_id
            INNER JOIN classroom_subject_monthly_score csms ON c.class_id = csms.class_id
            INNER JOIN tbl_assign_subject_grade asg ON csms.assign_subject_grade_id = asg.assign_subject_grade_id
            INNER JOIN tbl_subject sub ON asg.subject_code = sub.subject_code
            INNER JOIN tbl_monthly m ON csms.monthly_id = m.monthly_id
            LEFT JOIN tbl_student_monthly_score sms ON s.student_id = sms.student_id 
                AND sms.classroom_subject_monthly_score_id = csms.classroom_subject_monthly_score_id
                AND sms.isDeleted = 0
            WHERE c.class_id = :class_id
            AND s.isDeleted = 0
            AND csms.isDeleted = 0
            ORDER BY s.student_name, sub.subject_name";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':class_id', $class_id);
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
                        'class_name' => $row['class_name'],
                        'grade_name' => $row['grade_name'],
                        'scores' => []
                    ];
                }

                // Include all subjects, even those without scores
                $scoreData = [
                    'student_monthly_score_id' => $row['student_monthly_score_id'],
                    'classroom_subject_monthly_score_id' => $row['classroom_subject_monthly_score_id'],
                    'score' => $row['score'],
                    'create_date' => $row['create_date'],
                    'month_name' => $row['month_name'],
                    'subject_name' => $row['subject_name']
                ];
                
                $groupedResults[$studentId]['scores'][] = $scoreData;
            }

            return [
                'status' => 'success',
                'data' => array_values($groupedResults)
            ];

        } catch (PDOException $e) {
            error_log("Error in getStudentScoresByClass: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Database error occurred'
            ];
        }
    }

    public function getStudentScoresByClassSubjectMonthlyScore($classroom_subject_monthly_score_id) {
        try {
            $query = "SELECT 
                s.student_id,
                s.student_name,
                c.class_name,
                g.grade_name,
                m.monthly_id,
                m.month_name,
                sub.subject_name,
                csms.classroom_subject_monthly_score_id,
                COALESCE(sms.student_monthly_score_id, NULL) as student_monthly_score_id,
                COALESCE(sms.score, NULL) as score,
                sms.create_date
            FROM classroom_subject_monthly_score csms
            INNER JOIN tbl_classroom c ON csms.class_id = c.class_id
            INNER JOIN tbl_grade g ON c.grade_id = g.grade_id
            INNER JOIN tbl_assign_subject_grade asg ON csms.assign_subject_grade_id = asg.assign_subject_grade_id
            INNER JOIN tbl_subject sub ON asg.subject_code = sub.subject_code
            INNER JOIN tbl_monthly m ON csms.monthly_id = m.monthly_id
            INNER JOIN tbl_study st ON st.class_id = c.class_id AND st.status = 'active' AND st.isDeleted = 0
            INNER JOIN tbl_student_info s ON s.student_id = st.student_id
            LEFT JOIN tbl_student_monthly_score sms ON (
                s.student_id = sms.student_id 
                AND sms.classroom_subject_monthly_score_id = csms.classroom_subject_monthly_score_id
                AND sms.isDeleted = 0
            )
            WHERE csms.classroom_subject_monthly_score_id = :classroom_subject_monthly_score_id
            AND s.isDeleted = 0
            AND csms.isDeleted = 0
            ORDER BY s.student_name";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':classroom_subject_monthly_score_id', $classroom_subject_monthly_score_id);
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
                        'class_name' => $row['class_name'],
                        'grade_name' => $row['grade_name'],
                        $row['month_name'] => [
                            'monthly_id' => $row['monthly_id'],
                            'scores' => []
                        ]
                    ];
                }

                // Add score data under the month
                $scoreData = [
                    'student_monthly_score_id' => $row['student_monthly_score_id'],
                    'classroom_subject_monthly_score_id' => $row['classroom_subject_monthly_score_id'],
                    'subject_name' => $row['subject_name'],
                    'score' => $row['score'],
                    'create_date' => $row['create_date']
                ];
                
                $groupedResults[$studentId][$row['month_name']]['scores'][] = $scoreData;
            }

            return [
                'status' => 'success',
                'data' => array_values($groupedResults)
            ];

        } catch (PDOException $e) {
            error_log("Error in getStudentScoresByClassSubjectMonthlyScore: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Database error occurred'
            ];
        }
    }

    public function getScoresGroupedBy($filters = [], $groupBy = 'month') {
        try {
            $query = "SELECT 
                s.student_id,
                s.student_name,
                c.class_name,
                g.grade_name,
                m.monthly_id,
                m.month_name,
                sub.subject_code,
                sub.subject_name,
                csms.classroom_subject_monthly_score_id,
                COALESCE(sms.student_monthly_score_id, NULL) as student_monthly_score_id,
                COALESCE(sms.score, NULL) as score,
                sms.create_date
            FROM classroom_subject_monthly_score csms
            INNER JOIN tbl_classroom c ON csms.class_id = c.class_id
            INNER JOIN tbl_grade g ON c.grade_id = g.grade_id
            INNER JOIN tbl_assign_subject_grade asg ON csms.assign_subject_grade_id = asg.assign_subject_grade_id
            INNER JOIN tbl_subject sub ON asg.subject_code = sub.subject_code
            INNER JOIN tbl_monthly m ON csms.monthly_id = m.monthly_id
            INNER JOIN tbl_study st ON st.class_id = c.class_id AND st.status = 'active' AND st.isDeleted = 0
            INNER JOIN tbl_student_info s ON s.student_id = st.student_id
            LEFT JOIN tbl_student_monthly_score sms ON (
                s.student_id = sms.student_id 
                AND sms.classroom_subject_monthly_score_id = csms.classroom_subject_monthly_score_id
                AND sms.isDeleted = 0
            )
            WHERE s.isDeleted = 0 AND csms.isDeleted = 0";

            $params = [];
            
            // Add filters
            if (!empty($filters['class_id'])) {
                $query .= " AND c.class_id = :class_id";
                $params[':class_id'] = $filters['class_id'];
            }
            if (!empty($filters['monthly_id'])) {
                $query .= " AND m.monthly_id = :monthly_id";
                $params[':monthly_id'] = $filters['monthly_id'];
            }
            if (!empty($filters['student_id'])) {
                $query .= " AND s.student_id = :student_id";
                $params[':student_id'] = $filters['student_id'];
            }
            if (!empty($filters['subject_code'])) {
                $query .= " AND sub.subject_code = :subject_code";
                $params[':subject_code'] = $filters['subject_code'];
            }

            // Add ordering
            $query .= " ORDER BY s.student_name, m.monthly_id, sub.subject_name";

            $stmt = $this->conn->prepare($query);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();

            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Group results based on groupBy parameter
            $groupedResults = [];
            foreach ($results as $row) {
                $studentId = $row['student_id'];
                
                if (!isset($groupedResults[$studentId])) {
                    $groupedResults[$studentId] = [
                        'student_id' => $studentId,
                        'student_name' => $row['student_name'],
                        'class_name' => $row['class_name'],
                        'grade_name' => $row['grade_name']
                    ];

                    if ($groupBy === 'month') {
                        $groupedResults[$studentId]['months'] = [];
                    } else if ($groupBy === 'subject') {
                        $groupedResults[$studentId]['subjects'] = [];
                    } else {
                        $groupedResults[$studentId]['months'] = [];
                    }
                }

                if ($groupBy === 'month') {
                    if (!isset($groupedResults[$studentId]['months'][$row['month_name']])) {
                        $groupedResults[$studentId]['months'][$row['month_name']] = [
                            'monthly_id' => $row['monthly_id'],
                            'subjects' => []
                        ];
                    }
                    
                    $groupedResults[$studentId]['months'][$row['month_name']]['subjects'][] = [
                        'subject_code' => $row['subject_code'],
                        'subject_name' => $row['subject_name'],
                        'score' => $row['score'],
                        'student_monthly_score_id' => $row['student_monthly_score_id'],
                        'classroom_subject_monthly_score_id' => $row['classroom_subject_monthly_score_id'],
                        'create_date' => $row['create_date']
                    ];
                } else if ($groupBy === 'subject') {
                    if (!isset($groupedResults[$studentId]['subjects'][$row['subject_name']])) {
                        $groupedResults[$studentId]['subjects'][$row['subject_name']] = [
                            'subject_code' => $row['subject_code'],
                            'months' => []
                        ];
                    }
                    
                    $groupedResults[$studentId]['subjects'][$row['subject_name']]['months'][] = [
                        'month_name' => $row['month_name'],
                        'monthly_id' => $row['monthly_id'],
                        'score' => $row['score'],
                        'student_monthly_score_id' => $row['student_monthly_score_id'],
                        'classroom_subject_monthly_score_id' => $row['classroom_subject_monthly_score_id'],
                        'create_date' => $row['create_date']
                    ];
                }
            }

            // Convert months and subjects from associative arrays to indexed arrays
            foreach ($groupedResults as &$student) {
                if (isset($student['months'])) {
                    $student['months'] = array_values($student['months']);
                }
                if (isset($student['subjects'])) {
                    $student['subjects'] = array_values($student['subjects']);
                }
            }

            return [
                'status' => 'success',
                'data' => array_values($groupedResults)
            ];

        } catch (PDOException $e) {
            error_log("Error in getScoresGroupedBy: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Database error occurred'
            ];
        }
    }

    public function getStudentScoresByClassAndMonth($class_id, $monthly_id) {
        try {
            $query = "SELECT 
                s.student_id,
                s.student_name,
                c.class_name,
                g.grade_name,
                m.monthly_id,
                m.month_name,
                sub.subject_name,
                asg.assign_subject_grade_id,
                csms.classroom_subject_monthly_score_id,
                COALESCE(sms.student_monthly_score_id, NULL) as student_monthly_score_id,
                COALESCE(sms.score, NULL) as score,
                sms.create_date
            FROM tbl_student_info s
            INNER JOIN tbl_study st ON s.student_id = st.student_id AND st.status = 'active' AND st.isDeleted = 0
            INNER JOIN tbl_classroom c ON st.class_id = c.class_id
            INNER JOIN tbl_grade g ON c.grade_id = g.grade_id
            INNER JOIN classroom_subject_monthly_score csms ON c.class_id = csms.class_id
            INNER JOIN tbl_assign_subject_grade asg ON csms.assign_subject_grade_id = asg.assign_subject_grade_id
            INNER JOIN tbl_subject sub ON asg.subject_code = sub.subject_code
            INNER JOIN tbl_monthly m ON csms.monthly_id = m.monthly_id
            LEFT JOIN tbl_student_monthly_score sms ON (
                s.student_id = sms.student_id 
                AND sms.classroom_subject_monthly_score_id = csms.classroom_subject_monthly_score_id
                AND sms.isDeleted = 0
            )
            WHERE c.class_id = :class_id
            AND csms.monthly_id = :monthly_id
            AND s.isDeleted = 0
            AND csms.isDeleted = 0
            ORDER BY s.student_name, sub.subject_name";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':class_id', $class_id);
            $stmt->bindParam(':monthly_id', $monthly_id);
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
                        'class_name' => $row['class_name'],
                        'grade_name' => $row['grade_name'],
                        'scores' => []
                    ];
                }

                $scoreData = [
                    'student_monthly_score_id' => $row['student_monthly_score_id'],
                    'classroom_subject_monthly_score_id' => $row['classroom_subject_monthly_score_id'],
                    'assign_subject_grade_id' => $row['assign_subject_grade_id'],
                    'subject_name' => $row['subject_name'],
                    'score' => $row['score'],
                    'create_date' => $row['create_date']
                ];
                
                $groupedResults[$studentId]['scores'][] = $scoreData;
            }

            return [
                'status' => 'success',
                'data' => array_values($groupedResults)
            ];

        } catch (PDOException $e) {
            error_log("Error in getStudentScoresByClassAndMonth: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Database error occurred'
            ];
        }
    }

    public function getStudentMonthlyRankings($filters = []) {
        try {
            $query = "SELECT 
                r.student_id,
                r.student_name,
                r.class_id,
                r.class_name, 
                r.monthly_id,
                r.month_name,
                r.subjects_count,
                r.total_score,
                r.average_score,
                r.rank_in_class,
                r.class_size
            FROM view_student_monthly_rankings r
            WHERE 1=1";

            $params = [];
            
            // Add filters
            if (!empty($filters['class_id'])) {
                $query .= " AND r.class_id = :class_id";
                $params[':class_id'] = $filters['class_id'];
            }
            if (!empty($filters['monthly_id'])) {
                $query .= " AND r.monthly_id = :monthly_id";
                $params[':monthly_id'] = $filters['monthly_id'];
            }
            if (!empty($filters['student_id'])) {
                $query .= " AND r.student_id = :student_id";
                $params[':student_id'] = $filters['student_id'];
            }

            // Add ordering
            $query .= " ORDER BY r.class_id, r.monthly_id, r.rank_in_class";

            $stmt = $this->conn->prepare($query);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();

            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'status' => 'success',
                'data' => $results
            ];

        } catch (PDOException $e) {
            error_log("Error in getStudentMonthlyRankings: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Database error occurred'
            ];
        }
    }

    public function getStudentMonthlyScoreSummary($filters = []) {
        try {
            $query = "SELECT 
                s.student_id,
                s.student_name,
                s.class_id,
                s.class_name, 
                s.monthly_id,
                s.month_name,
                s.subjects_count,
                s.total_score,
                s.avg_score,
                s.rank_in_class
            FROM view_student_monthly_score_summary s
            WHERE 1=1";

            $params = [];
            
            // Add filters
            if (!empty($filters['class_id'])) {
                $query .= " AND s.class_id = :class_id";
                $params[':class_id'] = $filters['class_id'];
            }
            if (!empty($filters['monthly_id'])) {
                $query .= " AND s.monthly_id = :monthly_id";
                $params[':monthly_id'] = $filters['monthly_id'];
            }
            if (!empty($filters['student_id'])) {
                $query .= " AND s.student_id = :student_id";
                $params[':student_id'] = $filters['student_id'];
            }

            // Add ordering
            $query .= " ORDER BY s.class_id, s.monthly_id, s.rank_in_class";

            $stmt = $this->conn->prepare($query);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->execute();

            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'status' => 'success',
                'data' => $results
            ];

        } catch (PDOException $e) {
            error_log("Error in getStudentMonthlyScoreSummary: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Database error occurred'
            ];
        }
    }
}