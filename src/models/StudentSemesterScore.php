<?php
require_once __DIR__ . '/../config/database.php';

    class StudentSemesterScore {
        private $conn;
    
        public function __construct() {
            $database = new Database();
            $this->conn = $database->getConnection();
        }
    
    public function fetchAllSemesterBasedScores() {
        try {
            $query = "SELECT 
                s.student_id,
                s.student_name,
                c.class_id,
                c.class_name,
                g.grade_id,
                g.grade_name,
                ses.semester_id,
                sem.semester_name,
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
            LEFT JOIN tbl_semester_exam_subjects ses ON c.class_id = ses.class_id AND ses.isDeleted = 0
            LEFT JOIN tbl_semester sem ON ses.semester_id = sem.semester_id
            WHERE s.isDeleted = 0
            ORDER BY s.student_id, ses.semester_id, sub.subject_code";
    
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
                        'grade_id' => $row['grade_id'],
                        'grade_name' => $row['grade_name'],
                        'semesters' => []
                    ];
                }
    
                if ($row['subject_code'] && $row['score'] !== null) {
                    $groupedResults[$studentId]['semesters'][] = [
                        'semester_id' => $row['semester_id'],
                        'semester_name' => $row['semester_name'],
                        'subject_code' => $row['subject_code'],
                        'subject_name' => $row['subject_name'],
                        'score' => $row['score'],
                        'create_date' => $row['create_date']
                    ];
                }
            }
    
            return [
                'status' => 'success',
                'data' => array_values($groupedResults)
            ];
    
        } catch (Exception $e) {
            error_log("Error in fetchAllSemesterBasedScores: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Failed to fetch student semester-based scores'
            ];
        }
    }    
    

    public function create($data) {
        try {
            // Validate required fields
            $requiredFields = ['student_id', 'semester_exam_subject_id', 'score'];
            foreach ($requiredFields as $field) {
                if (!isset($data[$field])) {
                    return [
                        'status' => 'error',
                        'message' => "Missing required field: {$field}"
                    ];
                }
            }

            // Validate score
            if ($data['score'] < 0 || $data['score'] > 100) {
                return [
                    'status' => 'error',
                    'message' => 'Score must be between 0 and 100'
                ];
            }

            // Check for duplicate score
            $checkQuery = "SELECT student_semester_score_id 
                         FROM tbl_student_semester_score 
                         WHERE student_id = :student_id 
                         AND semester_exam_subject_id = :semester_exam_subject_id 
                         AND isDeleted = 0";
            
            $stmt = $this->conn->prepare($checkQuery);
            $stmt->bindParam(':student_id', $data['student_id']);
            $stmt->bindParam(':semester_exam_subject_id', $data['semester_exam_subject_id']);
            $stmt->execute();

            if ($stmt->fetch()) {
                return [
                    'status' => 'error',
                    'message' => 'Score already exists for this student'
                ];
            }

            // Insert score
            $insertQuery = "INSERT INTO tbl_student_semester_score 
                          (student_id, semester_exam_subject_id, score) 
                          VALUES (:student_id, :semester_exam_subject_id, :score)";
            
            $stmt = $this->conn->prepare($insertQuery);
            $stmt->bindParam(':student_id', $data['student_id']);
            $stmt->bindParam(':semester_exam_subject_id', $data['semester_exam_subject_id']);
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
            error_log("Error in create: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Failed to add score'
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
            $query = "UPDATE tbl_student_semester_score 
                     SET score = :score 
                     WHERE student_semester_score_id = :id 
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
            $deleteQuery = "UPDATE tbl_student_semester_score 
                          SET isDeleted = 1 
                          WHERE student_semester_score_id = :id";
            
            $stmt = $this->conn->prepare($deleteQuery);
            $stmt->bindParam(':id', $id);

            if ($stmt->execute()) {
                return [
                    'status' => 'success',
                    'message' => 'Score deleted successfully'
                ];
            }

            return [
                'status' => 'error',
                'message' => 'Failed to delete score'
            ];

        } catch (PDOException $e) {
            error_log("Error in delete: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Failed to delete score'
            ];
        }
    }

    public function getStudentSemesterScores($student_id, $semester_id, $class_id) {
        try {
            $query = "CALL GetStudentSemesterScores(:student_id, :semester_id, :class_id)";
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(':student_id', $student_id);
            $stmt->bindParam(':semester_id', $semester_id);
            $stmt->bindParam(':class_id', $class_id);
            
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'status' => 'success',
                'data' => $results
            ];

        } catch (PDOException $e) {
            error_log("Error in getStudentSemesterScores: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Failed to fetch student semester scores'
            ];
        }
    }

    public function calculateFinalSemesterAverage($student_id, $semester_id, $class_id, $monthly_ids) {
        try {
            $query = "CALL CalculateFinalSemesterAverage(:student_id, :semester_id, :class_id, :monthly_ids)";
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(':student_id', $student_id);
            $stmt->bindParam(':semester_id', $semester_id);
                $stmt->bindParam(':class_id', $class_id);
            $stmt->bindParam(':monthly_ids', $monthly_ids);
            
                $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                return [
                    'status' => 'success',
                    'data' => $result
                ];

            } catch (PDOException $e) {
            error_log("Error in calculateFinalSemesterAverage: " . $e->getMessage());
                return [
                    'status' => 'error',
                'message' => 'Failed to calculate final semester average'
                ];
            }
        }
    public function getStudentScoresByClassAndSemester($class_id, $semester_id) {
            try {
            // SQL query to fetch student scores by class and semester
                $query = "SELECT 
                s.student_id,
                s.student_name,
                c.class_name,
                g.grade_name,
                sem.semester_id,
                sem.semester_name,
                sub.subject_name,
                asg.assign_subject_grade_id,
                COALESCE(sss.student_semester_score_id, NULL) AS student_semester_score_id,
                COALESCE(sss.score, NULL) AS score,
                sss.create_date
            FROM tbl_student_info s
            INNER JOIN tbl_study st 
                ON s.student_id = st.student_id 
                AND st.status = 'active' 
                AND st.isDeleted = 0
            INNER JOIN tbl_classroom c 
                ON st.class_id = c.class_id
            INNER JOIN tbl_grade g 
                ON c.grade_id = g.grade_id
            INNER JOIN tbl_semester_exam_subjects ses 
                ON c.class_id = ses.class_id 
                AND ses.isDeleted = 0
            INNER JOIN tbl_assign_subject_grade asg 
                ON ses.assign_subject_grade_id = asg.assign_subject_grade_id
            INNER JOIN tbl_subject sub 
                ON asg.subject_code = sub.subject_code
            INNER JOIN tbl_semester sem 
                ON ses.semester_id = sem.semester_id
            LEFT JOIN tbl_student_semester_score sss 
                ON s.student_id = sss.student_id 
                AND sss.semester_exam_subject_id = ses.id
                AND sss.isDeleted = 0
            WHERE c.class_id = :class_id
                AND ses.semester_id = :semester_id
                AND s.isDeleted = 0
            ORDER BY s.student_name, sub.subject_name";
    
            // Prepare the SQL statement
                $stmt = $this->conn->prepare($query);
            
            // Bind parameters
                $stmt->bindParam(':class_id', $class_id);
                $stmt->bindParam(':semester_id', $semester_id);
            
            // Execute the query
                $stmt->execute();
                
            // Fetch the results
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Group the results by student
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
                    'student_semester_score_id' => $row['student_semester_score_id'],
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
            error_log("Error in getStudentScoresByClassAndSemester: " . $e->getMessage());
                return [
                    'status' => 'error',
                'message' => 'Database error occurred'
                ];
            }
        }
    }
