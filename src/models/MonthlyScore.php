<?php
require_once __DIR__ . '/../config/database.php';

class MonthlyScore {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function fetchAll() {
        try {
            $query = "SELECT 
                        ms.monthly_score_id,
                        ms.score,
                        ms.monthly_id,
                        ms.class_sub_id,
                        ms.class_id,
                        ms.year_study_id,
                        y.year_study,
                        ms.student_id,
                        ms.teacher_id,
                        ms.create_date,
                        s.student_name,
                        t.teacher_name,
                        c.class_name,
                        m.month_name,
                        sub.subject_name
                    FROM tbl_monthly_score ms
                    LEFT JOIN tbl_student_info s ON ms.student_id = s.student_id
                    LEFT JOIN tbl_teacher t ON ms.teacher_id = t.teacher_id
                    LEFT JOIN tbl_classroom c ON ms.class_id = c.class_id
                    LEFT JOIN tbl_monthly m ON ms.monthly_id = m.monthly_id
                    LEFT JOIN tbl_classroom_subject cs ON ms.class_sub_id = cs.classroom_subject_id
                    LEFT JOIN tbl_subject sub ON cs.sub_code = sub.subject_code
                    LEFT JOIN tbl_year_study y ON ms.year_study_id = y.year_study_id
                    WHERE ms.isDeleted = 0
                    ORDER BY ms.monthly_score_id DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'status' => 'success',
                'data' => $result
            ];
            
        } catch (PDOException $e) {
            error_log("Error fetching monthly scores: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Failed to fetch monthly scores: ' . $e->getMessage()
            ];
        }
    }

    public function create($data) {
        try {
            // Validate required fields
            $required_fields = [
                'teacher_id',
                'score',
                'monthly_id',
                'class_sub_id',
                'class_id',
                'year_study_id',
                'student_id'
            ];

            foreach ($required_fields as $field) {
                if (!isset($data[$field])) {
                    return [
                        'status' => 'error',
                        'message' => "Missing required field: $field"
                    ];
                }
            }

            // Validate score range (0-100)
            if ($data['score'] < 0 || $data['score'] > 100) {
                return [
                    'status' => 'error',
                    'message' => 'Score must be between 0 and 100'
                ];
            }

            // Check if score already exists
            $checkQuery = "SELECT monthly_score_id FROM tbl_monthly_score 
                          WHERE student_id = :student_id 
                          AND monthly_id = :monthly_id 
                          AND class_sub_id = :class_sub_id 
                          AND isDeleted = 0";
            
            $stmt = $this->conn->prepare($checkQuery);
            $stmt->bindParam(':student_id', $data['student_id']);
            $stmt->bindParam(':monthly_id', $data['monthly_id']);
            $stmt->bindParam(':class_sub_id', $data['class_sub_id']);
            $stmt->execute();

            if ($stmt->fetch()) {
                return [
                    'status' => 'error',
                    'message' => 'Score already exists for this student in this month'
                ];
            }

            // Insert new score
            $query = "INSERT INTO tbl_monthly_score 
                     (teacher_id, score, monthly_id, class_sub_id, class_id, 
                      year_study_id, student_id, create_date) 
                     VALUES 
                     (:teacher_id, :score, :monthly_id, :class_sub_id, :class_id,
                      :year_study_id, :student_id, NOW())";

            $stmt = $this->conn->prepare($query);

            // Bind parameters
            $stmt->bindParam(':teacher_id', $data['teacher_id']);
            $stmt->bindParam(':score', $data['score']);
            $stmt->bindParam(':monthly_id', $data['monthly_id']);
            $stmt->bindParam(':class_sub_id', $data['class_sub_id']);
            $stmt->bindParam(':class_id', $data['class_id']);
            $stmt->bindParam(':year_study_id', $data['year_study_id']);
            $stmt->bindParam(':student_id', $data['student_id']);

            if ($stmt->execute()) {
                return [
                    'status' => 'success',
                    'message' => 'Score added successfully',
                    'id' => $this->conn->lastInsertId()
                ];
            }

            return [
                'status' => 'error',
                'message' => 'Failed to add score'
            ];

        } catch (PDOException $e) {
            error_log("Error creating monthly score: " . $e->getMessage());
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
                    'message' => 'Invalid score value'
                ];
            }

            $query = "UPDATE tbl_monthly_score 
                     SET score = :score 
                     WHERE monthly_score_id = :id 
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
            error_log("Error updating monthly score: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Database error occurred'
            ];
        }
    }

    public function delete($id) {
        try {
            $query = "UPDATE tbl_monthly_score 
                     SET isDeleted = 1 
                     WHERE monthly_score_id = :id";
                     
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error deleting monthly score: " . $e->getMessage());
            return false;
        }
    }

    public function fetchById($id) {
        try {
            $query = "SELECT 
                        ms.*,
                        s.student_name,
                        t.teacher_name,
                        c.class_name,
                        m.month_name,
                        ams.subject_code
                    FROM tbl_monthly_score ms
                    LEFT JOIN tbl_student_info s ON ms.student_id = s.student_id
                    LEFT JOIN tbl_teacher t ON ms.teacher_id = t.teacher_id
                    LEFT JOIN tbl_classroom c ON ms.class_id = c.class_id
                    LEFT JOIN tbl_monthly m ON ms.monthly_id = m.monthly_id
                    LEFT JOIN tbl_assign_monthly_subject_grade ams ON ms.assign_monthsub_id = ams.assign_monthsub_id
                    WHERE ms.monthly_score_id = :monthly_score_id 
                    AND ms.isDeleted = 0";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':monthly_score_id', $id);
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
                'message' => 'Database error while fetching monthly score'
            ];
        }
    }

    public function getStudentMonthlyScores($student_id, $monthly_id, $class_id, $year_study_id) {
        try {
            $query = "SELECT 
                        ms.monthly_score_id,
                        ms.score,
                        ms.monthly_id,
                        ms.class_sub_id,
                        ms.class_id,
                        ms.year_study_id,
                        ms.student_id,
                        ms.teacher_id,
                        ms.create_date,
                        s.student_name,
                        t.teacher_name,
                        c.class_name,
                        m.month_name,
                        sub.subject_name,
                        cs.sub_code
                    FROM tbl_monthly_score ms
                    LEFT JOIN tbl_student_info s ON ms.student_id = s.student_id
                    LEFT JOIN tbl_teacher t ON ms.teacher_id = t.teacher_id
                    LEFT JOIN tbl_classroom c ON ms.class_id = c.class_id
                    LEFT JOIN tbl_monthly m ON ms.monthly_id = m.monthly_id
                    LEFT JOIN tbl_classroom_subject cs ON ms.class_sub_id = cs.classroom_subject_id
                    LEFT JOIN tbl_subject sub ON cs.sub_code = sub.subject_code
                    WHERE ms.student_id = :student_id 
                    AND ms.monthly_id = :monthly_id
                    AND ms.class_id = :class_id
                    AND ms.year_study_id = :year_study_id
                    AND ms.isDeleted = 0
                    ORDER BY ms.monthly_score_id DESC";

            $stmt = $this->conn->prepare($query);
            
            // Bind parameters with explicit types
            $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
            $stmt->bindParam(':monthly_id', $monthly_id, PDO::PARAM_INT);
            $stmt->bindParam(':class_id', $class_id, PDO::PARAM_INT);
            $stmt->bindParam(':year_study_id', $year_study_id, PDO::PARAM_INT);
            
            $stmt->execute();
            
            $scores = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // If no scores found, return empty array with success status
            if (empty($scores)) {
                return [
                    'status' => 'success',
                    'data' => [],
                    'message' => 'No scores found for this student'
                ];
            }

            return [
                'status' => 'success',
                'data' => $scores
            ];

        } catch (PDOException $e) {
            error_log("Error in getStudentMonthlyScores: " . $e->getMessage());
            error_log("SQL Query: " . $query);
            error_log("Parameters: student_id=$student_id, monthly_id=$monthly_id, class_id=$class_id, year_study_id=$year_study_id");
            
            return [
                'status' => 'error',
                'message' => 'Database error occurred: ' . $e->getMessage()
            ];
        }
    }
}
