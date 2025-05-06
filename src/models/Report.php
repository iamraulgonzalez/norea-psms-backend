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
            $query = "SELECT * FROM view_student_monthly_score_report WHERE 1=1";
            $params = [];

            // Add filters if provided
            if (!empty($class_id)) {
                $query .= " AND class_id = ?";
                $params[] = $class_id;
            }

            if (!empty($monthly_id)) {
                $query .= " AND monthly_id = ?";
                $params[] = $monthly_id;
            }

            if (!empty($year_study_id)) {
                $query .= " AND year_study_id = ?";
                $params[] = $year_study_id;
            }

            // Add ordering
            $query .= " ORDER BY student_name, monthly_id, subject_name";

            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($results)) {
                return [
                    'status' => 'error',
                    'message' => 'No data found for the specified parameters'
                ];
            }
            
            // Group results by student
            $groupedResults = [];
            foreach ($results as $row) {
                $studentId = $row['student_id'];
                
                if (!isset($groupedResults[$studentId])) {
                    $groupedResults[$studentId] = [
                        'student_id' => $studentId,
                        'student_name' => $row['student_name'],
                        'gender' => $row['gender'],
                        'class_id' => $row['class_id'],
                        'class_name' => $row['class_name'],
                        'year_study_id' => $row['year_study_id'],
                        'year_study' => $row['year_study'],
                        'subjects' => []
                    ];
                }

                // Add subject data
                $subjectData = [
                    'monthly_id' => $row['monthly_id'],
                    'month_name' => $row['month_name'],
                    'subject_code' => $row['subject_code'],
                    'subject_name' => $row['subject_name'],
                    'score' => $row['score']
                ];

                // Group subjects by month
                if (!isset($groupedResults[$studentId]['subjects'][$row['monthly_id']])) {
                    $groupedResults[$studentId]['subjects'][$row['monthly_id']] = [
                        'monthly_id' => $row['monthly_id'],
                        'month_name' => $row['month_name'],
                        'subjects' => []
                    ];
                }

                $groupedResults[$studentId]['subjects'][$row['monthly_id']]['subjects'][] = $subjectData;
            }

            // Convert associative arrays to indexed arrays
            foreach ($groupedResults as &$student) {
                $student['subjects'] = array_values($student['subjects']);
                foreach ($student['subjects'] as &$month) {
                    $month['subjects'] = array_values($month['subjects']);
                }
            }

            return [
                'status' => 'success',
                'data' => array_values($groupedResults)
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
            $query = "SELECT * FROM view_final_semester_averages WHERE 1=1";
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
                    'message' => 'No data found for the specified parameters'
                ];
            }

            // Group results by student
            $groupedResults = [];
            foreach ($results as $row) {
                $studentId = $row['student_id'];

                if (!isset($groupedResults[$studentId])) {
                    $groupedResults[$studentId] = [
                        'student_id' => $studentId,
                        'student_name' => $row['student_name'],
                        'gender' => $row['gender'],
                        'class_id' => $row['class_id'],
                        'class_name' => $row['class_name'],
                        'year_study_id' => $row['year_study_id'],
                        'year_study' => $row['year_study'],
                        'subjects' => []    
                    ];
                }

                $subjectData = [
                    'semester_id' => $row['semester_id'],
                    'semester_name' => $row['semester_name'],
                    'subject_code' => $row['subject_code'],
                    'subject_name' => $row['subject_name'],
                    'score' => $row['subject_score']
                ];

                $groupedResults[$studentId]['subjects'][$row['semester_id']]['subjects'][] = $subjectData;
            }

            // Convert associative arrays to indexed arrays
            foreach ($groupedResults as &$student) {
                $student['subjects'] = array_values($student['subjects']);
                foreach ($student['subjects'] as &$semester) {
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
}


