<?php
require_once __DIR__ . '/../config/database.php';

class Report {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getStudentMonthlyScoreReport($filters = []) {
        try {
            $query = "SELECT * FROM vstudentmonthlyscorereportv2 WHERE 1=1";
            $params = [];

            // Add filters if provided
            if (!empty($filters['student_id'])) {
                $query .= " AND student_id = ?";
                $params[] = $filters['student_id'];
            }

            if (!empty($filters['class_id'])) {
                $query .= " AND class_id = ?";
                $params[] = $filters['class_id'];
            }

            if (!empty($filters['monthly_id'])) {
                $query .= " AND monthly_id = ?";
                $params[] = $filters['monthly_id'];
            }

            // Add ordering
            $query .= " ORDER BY student_name, monthly_id, subject_name";

            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            
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

            return array_values($groupedResults);

        } catch (PDOException $e) {
            error_log("Error in getStudentMonthlyScoreReport: " . $e->getMessage());
            return false;
        }
    }
}

