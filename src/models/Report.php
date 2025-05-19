<?php
require_once __DIR__ . '/../config/database.php';

class Report {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getStudentMonthlyScoreReport($class_id, $monthly_id, $year_study_id) {
        try {
            $query = "SELECT * FROM view_student_monthly_score_report_for_report_page WHERE 1=1";
            $params = [];

            // Add filters if provided
            if (!empty($class_id)) {
                $query .= " AND class_id = :class_id";
                $params[':class_id'] = $class_id;
            }

            if (!empty($monthly_id)) {
                $query .= " AND monthly_id = :monthly_id";
                $params[':monthly_id'] = $monthly_id;
            }

            if (!empty($year_study_id)) {
                $query .= " AND year_study_id = :year_study_id";
                $params[':year_study_id'] = $year_study_id;
            }

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
            error_log("Error in getStudentMonthlyScoreReport: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }

    public function getStudentSemesterScoreReport($class_id, $semester_id, $year_study_id) {
        try {
            $query = "SELECT * FROM view_student_semester_report_for_report_page WHERE 1=1";
            $params = [];

            if (!empty($class_id)) {
                $query .= " AND class_id = ?";
                $params[] = $class_id;
            }

            if (!empty($semester_id)) {	
                $query .= " AND semester_id = ?";
                $params[] = $semester_id;
            }

            if (!empty($year_study_id)) {
                $query .= " AND year_study_id = ?";
                $params[] = $year_study_id;
            }

            $query .= " ORDER BY student_name, semester_id, subject_name";

            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($results)) {
                return [
                    'status' => 'error',
                    'message' => 'មិនមានទិន្នន័យសម្រាប់បញ្ជីនេះ'
                ];
            }

            // Group results by student
            $groupedResults = [];
            foreach ($results as $row) {
                $studentId = $row['student_id'];
                $semesterId = $row['semester_id'];

                // Initialize student if not exists
                if (!isset($groupedResults[$studentId])) {
                    $groupedResults[$studentId] = [
                        'student_id' => $studentId,
                        'student_name' => $row['student_name'],
                        'gender' => $row['gender'],
                        'class_id' => $row['class_id'],
                        'class_name' => $row['class_name'],
                        'year_study_id' => $row['year_study_id'],
                        'year_study' => $row['year_study'],
                        'monthly_average' => $row['monthly_average'],
                        'semester_exam_average' => $row['semester_exam_average'],
                        'final_semester_average' => $row['final_semester_average'],
                    ];
                }

                // Initialize semester if not exists
                if (!isset($groupedResults[$studentId]['semesters'][$semesterId])) {
                    $groupedResults[$studentId]['semesters'][$semesterId] = [
                        'semester_id' => $semesterId,
                        'semester_name' => $row['semester_name'],
                        'subjects' => []
                    ];
                }

                // Add subject to the semester
                $groupedResults[$studentId]['semesters'][$semesterId]['subjects'][] = [
                    'subject_code' => $row['subject_code'],
                    'subject_name' => $row['subject_name'],
                    'score' => isset($row['subject_score']) ? $row['subject_score'] : null
                ];
            }

            // Convert associative arrays to indexed arrays for output
            foreach ($groupedResults as &$student) {
                $student['semesters'] = array_values($student['semesters']);
                foreach ($student['semesters'] as &$semester) {
                    $semester['subjects'] = array_values($semester['subjects']);
                }
            }

            return [
                'status' => 'success',
                'data' => array_values($groupedResults)
            ];

        } catch (PDOException $e) {
            error_log("Error in getStudentSemesterScoreReport: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }

    public function getAllStudentbyGrade($grade_id) {
        try {
            $query = "SELECT 
                    s.student_id,
                    s.student_name,
                    c.class_id,
                    c.class_name,
                    g.grade_id,
                    g.grade_name
                FROM 
                    tbl_student_info s
                    JOIN tbl_study st ON s.student_id = st.student_id
                    JOIN tbl_classroom c ON st.class_id = c.class_id
                    JOIN tbl_grade g ON c.grade_id = g.grade_id
                WHERE 
                    c.grade_id = ?
                    AND st.status = 'active'
                    AND s.isDeleted = 0
                    AND st.isDeleted = 0
                ORDER BY 
                    c.class_name, s.student_name";
            $params = [$grade_id];

            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);

            return [
                'status' => 'success',
                'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ];
        } catch (PDOException $e) {
            error_log("Error in getAllStudentbyGrade: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }
    
    public function getAllStudentbyYearStudy($year_study_id) {
        try {
            $query = "SELECT * FROM view_all_students_by_year_study WHERE year_study_id = ?";
            $params = [$year_study_id];

            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);

            return [
                'status' => 'success',
                'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ];
        } catch (PDOException $e) {
            error_log("Error in getAllStudentbyYearStudy: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }

    public function getAllStudentbyYearStudyGraduate($year_study_id) {
        try {
            $query = "SELECT * FROM view_all_students_by_year_study_graduate WHERE year_study_id = ?";
            $params = [$year_study_id];

            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);

            return [
                'status' => 'success',
                'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ];
        } catch (PDOException $e) {
            error_log("Error in getAllStudentbyYearStudy: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }

    public function getStudentByGrade($grade_id) {
        try {
            $query = "SELECT * FROM v_getStudentByGrade WHERE grade_id = ?";
            $params = [$grade_id];

            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);

            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'status' => 'success',
                'data' => $results
            ];
        } catch (PDOException $e) {
            error_log("Error in getStudentByGrade: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }

}


