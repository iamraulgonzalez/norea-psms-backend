<?php
require_once __DIR__ . '/../config/database.php';

class Rankings {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getMonthlyRankings($class_id = null, $monthly_id = null) {
        try {
            $conditions = [];
            $params = [];
            $query = "SELECT * FROM view_monthly_rankings WHERE 1=1";

            if ($class_id !== null) {
                $conditions[] = "class_id = :class_id";
                $params[':class_id'] = $class_id;
            }

            if ($monthly_id !== null) {
                $conditions[] = "monthly_id = :monthly_id";
                $params[':monthly_id'] = $monthly_id;
            }

            if (!empty($conditions)) {
                $query .= " AND " . implode(" AND ", $conditions);
            }

            $query .= " ORDER BY class_id, monthly_id, rank_in_class";

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
            error_log("Error fetching monthly rankings: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Failed to fetch rankings'
            ];
        }
    }

    public function getMonthlySubjectScores($class_id = null, $monthly_id = null, $student_id = null) {
        try {
            $conditions = [];
            $params = [];
            $query = "SELECT * FROM view_monthly_subject_scores WHERE 1=1";

            if ($class_id !== null) {
                $conditions[] = "class_id = :class_id";
                $params[':class_id'] = $class_id;
            }

            if ($monthly_id !== null) {
                $conditions[] = "monthly_id = :monthly_id";
                $params[':monthly_id'] = $monthly_id;
            }

            if ($student_id !== null) {
                $conditions[] = "student_id = :student_id";
                $params[':student_id'] = $student_id;
            }

            if (!empty($conditions)) {
                $query .= " AND " . implode(" AND ", $conditions);
            }

            $query .= " ORDER BY class_id, monthly_id, assign_subject_grade_id, subject_rank_in_class";

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
            error_log("Error fetching monthly subject scores: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Failed to fetch subject scores'
            ];
        }
    }

    public function getStudentRankingHistory($student_id) {
        try {
            $query = "SELECT * FROM view_monthly_rankings 
                     WHERE student_id = :student_id 
                     ORDER BY monthly_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':student_id', $student_id);
            $stmt->execute();

            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return [
                'status' => 'success',
                'data' => $results
            ];
        } catch (PDOException $e) {
            error_log("Error fetching student ranking history: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Failed to fetch ranking history'
            ];
        }
    }

    public function getTopStudents($class_id, $monthly_id, $limit = 5) {
        try {
            $query = "SELECT * FROM view_monthly_rankings 
                     WHERE class_id = :class_id 
                     AND monthly_id = :monthly_id 
                     AND rank_in_class <= :limit
                     ORDER BY rank_in_class";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':class_id', $class_id);
            $stmt->bindParam(':monthly_id', $monthly_id);
            $stmt->bindParam(':limit', $limit);
            $stmt->execute();

            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return [
                'status' => 'success',
                'data' => $results
            ];
        } catch (PDOException $e) {
            error_log("Error fetching top students: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Failed to fetch top students'
            ];
        }
    }
} 