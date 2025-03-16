<?php
require_once __DIR__ . '/../config/database.php';

class ClassroomSubjectMonthlyScore {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAllClassroomSubjectMonthlyScores() {
        try {
            $query = "SELECT 
                csms.classroom_subject_monthly_score_id,
                csms.class_id,
                c.class_name,
                g.grade_id,
                g.grade_name,
                csms.monthly_id,
                m.month_name,
                csms.assign_subject_grade_id,
                s.subject_code,
                s.subject_name
            FROM classroom_subject_monthly_score csms
            LEFT JOIN tbl_classroom c ON csms.class_id = c.class_id
            LEFT JOIN tbl_grade g ON c.grade_id = g.grade_id
            LEFT JOIN tbl_monthly m ON csms.monthly_id = m.monthly_id
            LEFT JOIN tbl_assign_subject_grade asg ON csms.assign_subject_grade_id = asg.assign_subject_grade_id
            LEFT JOIN tbl_subject s ON asg.subject_code = s.subject_code
            WHERE csms.isDeleted = 0 
            ORDER BY c.class_name, m.month_name, s.subject_name";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($results)) {
                return [
                    'status' => 'success',
                    'data' => [],
                    'message' => 'No classroom subject monthly scores found'
                ];
            }

            return [
                'status' => 'success',
                'data' => $results
            ];

        } catch (PDOException $e) {
            error_log("Error fetching classroom subject monthly scores: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Failed to fetch classroom subject monthly scores',
                'error' => $e->getMessage()
            ];
        }
    }

    public function getClassroomSubjectMonthlyScoreById($id) {
        try {
            $query = "SELECT 
                csms.classroom_subject_monthly_score_id,
                c.class_id,
                c.class_name,
                g.grade_id,
                g.grade_name,
                m.monthly_id,
                m.month_name,
                asg.assign_subject_grade_id,
                s.subject_code,
                s.subject_name
            FROM classroom_subject_monthly_score csms
            JOIN tbl_classroom c ON csms.class_id = c.class_id
            JOIN tbl_grade g ON c.grade_id = g.grade_id
            JOIN tbl_monthly m ON csms.monthly_id = m.monthly_id
            JOIN tbl_assign_subject_grade asg ON csms.assign_subject_grade_id = asg.assign_subject_grade_id
            JOIN tbl_subject s ON asg.subject_code = s.subject_code
            WHERE csms.classroom_subject_monthly_score_id = :id 
            AND csms.isDeleted = 0";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$result) {
                return [
                    'status' => 'error',
                    'message' => 'Classroom subject monthly score not found'
                ];
            }

            return [
                'status' => 'success',
                'data' => $result
            ];

        } catch (PDOException $e) {
            error_log("Error fetching classroom subject monthly score: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Failed to fetch classroom subject monthly score'
            ];
        }
    }

    public function getClassroomSubjectMonthlyScoresByClassId($classId) {
        try {
            $query = "SELECT 
                m.monthly_id,
                m.month_name,
                COUNT(csms.classroom_subject_monthly_score_id) as subject_count,
                GROUP_CONCAT(
                    JSON_OBJECT(
                        'classroom_subject_monthly_score_id', csms.classroom_subject_monthly_score_id,
                        'class_id', c.class_id,
                        'class_name', c.class_name,
                        'grade_id', g.grade_id,
                        'grade_name', g.grade_name,
                        'assign_subject_grade_id', asg.assign_subject_grade_id,
                        'subject_code', s.subject_code,
                        'subject_name', s.subject_name
                    )
                ) as subjects
            FROM classroom_subject_monthly_score csms
            JOIN tbl_classroom c ON csms.class_id = c.class_id
            JOIN tbl_grade g ON c.grade_id = g.grade_id
            JOIN tbl_monthly m ON csms.monthly_id = m.monthly_id
            JOIN tbl_assign_subject_grade asg ON csms.assign_subject_grade_id = asg.assign_subject_grade_id
            JOIN tbl_subject s ON asg.subject_code = s.subject_code
            WHERE csms.class_id = :class_id 
            AND csms.isDeleted = 0
            GROUP BY m.monthly_id, m.month_name
            ORDER BY m.monthly_id";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':class_id', $classId);
            $stmt->execute();
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Process the results to convert the JSON string to array
            foreach ($results as &$result) {
                $result['subjects'] = json_decode('[' . $result['subjects'] . ']', true);
            }
            
            return [
                'status' => 'success',
                'data' => $results
            ];

        } catch (PDOException $e) {
            error_log("Error fetching classroom subject monthly scores by class: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Failed to fetch classroom subject monthly scores'
            ];
        }
    }

    public function getClassroomSubjectMonthlyScoresbyMonthlyIdandClassId($monthlyId, $classId) {
        try {
            $query = "SELECT 
                csms.classroom_subject_monthly_score_id,
                c.class_id,
                c.class_name,
                g.grade_id,
                g.grade_name,
                m.monthly_id,
                m.month_name,
                asg.assign_subject_grade_id,
                s.subject_code,
                s.subject_name
            FROM classroom_subject_monthly_score csms
            JOIN tbl_classroom c ON csms.class_id = c.class_id
            JOIN tbl_grade g ON c.grade_id = g.grade_id
            JOIN tbl_monthly m ON csms.monthly_id = m.monthly_id
            JOIN tbl_assign_subject_grade asg ON csms.assign_subject_grade_id = asg.assign_subject_grade_id
            JOIN tbl_subject s ON asg.subject_code = s.subject_code
            WHERE csms.monthly_id = :monthly_id 
            AND csms.class_id = :class_id
            AND csms.isDeleted = 0";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':monthly_id', $monthlyId);
            $stmt->bindParam(':class_id', $classId);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($results)) {
                return [
                    'status' => 'success',
                    'data' => [],
                    'message' => 'No classroom subject monthly scores found'
                ];
            }

            return [
                'status' => 'success',
                'data' => $results
            ];
        } catch (PDOException $e) {
            error_log("Error fetching classroom subject monthly scores by monthly id: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Failed to fetch classroom subject monthly scores'
            ];
        }
    }

    public function addClassroomSubjectMonthlyScore($data) {
        try {
            $this->conn->beginTransaction();

            // Validate classroom exists
            $classroomQuery = "SELECT COUNT(*) FROM tbl_classroom WHERE class_id = ? AND isDeleted = 0";
            $stmt = $this->conn->prepare($classroomQuery);
            $stmt->execute([$data['class_id']]);
            if ($stmt->fetchColumn() == 0) {
                return [
                    'status' => 'error',
                    'message' => 'Classroom does not exist'
                ];
            }

            // Validate assign_subject_grade exists
            $subjectQuery = "SELECT COUNT(*) FROM tbl_assign_subject_grade WHERE assign_subject_grade_id = ? AND isDeleted = 0";
            $stmt = $this->conn->prepare($subjectQuery);
            $stmt->execute([$data['assign_subject_grade_id']]);
            if ($stmt->fetchColumn() == 0) {
                return [
                    'status' => 'error',
                    'message' => 'Subject grade assignment does not exist'
                ];
            }

            // Validate monthly exists
            $monthlyQuery = "SELECT COUNT(*) FROM tbl_monthly WHERE monthly_id = ? AND isDeleted = 0";
            $stmt = $this->conn->prepare($monthlyQuery);
            $stmt->execute([$data['monthly_id']]);
            if ($stmt->fetchColumn() == 0) {
                return [
                    'status' => 'error',
                    'message' => 'Monthly period does not exist'
                ];
            }

            // Check for duplicate entry
            $duplicateQuery = "SELECT COUNT(*) FROM classroom_subject_monthly_score 
                             WHERE class_id = ? AND assign_subject_grade_id = ? AND monthly_id = ? AND isDeleted = 0";
            $stmt = $this->conn->prepare($duplicateQuery);
            $stmt->execute([$data['class_id'], $data['assign_subject_grade_id'], $data['monthly_id']]);
            if ($stmt->fetchColumn() > 0) {
                return [
                    'status' => 'error',
                    'message' => 'This subject is already assigned to this class for this month'
                ];
            }

            // Insert new record
            $query = "INSERT INTO classroom_subject_monthly_score 
                     (class_id, assign_subject_grade_id, monthly_id) 
                     VALUES (:class_id, :assign_subject_grade_id, :monthly_id)";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':class_id', $data['class_id']);
            $stmt->bindParam(':assign_subject_grade_id', $data['assign_subject_grade_id']);
            $stmt->bindParam(':monthly_id', $data['monthly_id']);

            if ($stmt->execute()) {
                $this->conn->commit();
                return [
                    'status' => 'success',
                    'message' => 'Classroom subject monthly score added successfully',
                    'id' => $this->conn->lastInsertId()
                ];
            }

            $this->conn->rollBack();
            return [
                'status' => 'error',
                'message' => 'Failed to add classroom subject monthly score'
            ];

        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Error adding classroom subject monthly score: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Database error occurred'
            ];
        }
    }

    public function updateClassroomSubjectMonthlyScore($id, $data) {
        try {
            $query = "UPDATE classroom_subject_monthly_score 
                     SET class_id = :class_id, 
                         assign_subject_grade_id = :assign_subject_grade_id,
                         monthly_id = :monthly_id
                     WHERE classroom_subject_monthly_score_id = :id";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':class_id', $data['class_id']);
            $stmt->bindParam(':assign_subject_grade_id', $data['assign_subject_grade_id']);
            $stmt->bindParam(':monthly_id', $data['monthly_id']);

            if ($stmt->execute()) {
                return [
                    'status' => 'success',
                    'message' => 'Classroom subject monthly score updated successfully'
                ];
            }

            return [
                'status' => 'error',
                'message' => 'Failed to update classroom subject monthly score'
            ];

        } catch (PDOException $e) {
            error_log("Error updating classroom subject monthly score: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Database error occurred'
            ];
        }
    }

    public function deleteClassroomSubjectMonthlyScore($id) {
        try {
            $query = "UPDATE classroom_subject_monthly_score 
                     SET isDeleted = 1 
                     WHERE classroom_subject_monthly_score_id = :id";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);

            if ($stmt->execute()) {
                return [
                    'status' => 'success',
                    'message' => 'Classroom subject monthly score deleted successfully'
                ];
            }

            return [
                'status' => 'error',
                'message' => 'Failed to delete classroom subject monthly score'
            ];

        } catch (PDOException $e) {
            error_log("Error deleting classroom subject monthly score: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Database error occurred'
            ];
        }
    }

    public function getClassroomSubjectMonthlyScoresByClassAndMonth($classId, $monthlyId) {
        try {
            $query = "SELECT 
                csms.classroom_subject_monthly_score_id,
                c.class_id,
                c.class_name,
                g.grade_id,
                g.grade_name,
                m.monthly_id,
                m.month_name,
                asg.assign_subject_grade_id,
                s.subject_code,
                s.subject_name
            FROM classroom_subject_monthly_score csms
            JOIN tbl_classroom c ON csms.class_id = c.class_id
            JOIN tbl_grade g ON c.grade_id = g.grade_id
            JOIN tbl_monthly m ON csms.monthly_id = m.monthly_id
            JOIN tbl_assign_subject_grade asg ON csms.assign_subject_grade_id = asg.assign_subject_grade_id
            JOIN tbl_subject s ON asg.subject_code = s.subject_code
            WHERE csms.class_id = :class_id 
            AND csms.monthly_id = :monthly_id
            AND csms.isDeleted = 0
            ORDER BY s.subject_name";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':class_id', $classId);
            $stmt->bindParam(':monthly_id', $monthlyId);
            $stmt->execute();
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'status' => 'success',
                'data' => $results
            ];

        } catch (PDOException $e) {
            error_log("Error fetching classroom subject monthly scores by class and month: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Failed to fetch classroom subject monthly scores'
            ];
        }
    }

    public function getByClassSubjectMonthly($class_id, $assign_subject_grade_id, $monthly_id) {
        try {
            $query = "SELECT 
                csms.classroom_subject_monthly_score_id,
                csms.class_id,
                c.class_name,
                g.grade_id,
                g.grade_name,
                csms.monthly_id,
                m.month_name,
                csms.assign_subject_grade_id,
                s.subject_code,
                s.subject_name
            FROM classroom_subject_monthly_score csms
            LEFT JOIN tbl_classroom c ON csms.class_id = c.class_id
            LEFT JOIN tbl_grade g ON c.grade_id = g.grade_id
            LEFT JOIN tbl_monthly m ON csms.monthly_id = m.monthly_id
            LEFT JOIN tbl_assign_subject_grade asg ON csms.assign_subject_grade_id = asg.assign_subject_grade_id
            LEFT JOIN tbl_subject s ON asg.subject_code = s.subject_code
            WHERE csms.class_id = :class_id
            AND csms.assign_subject_grade_id = :assign_subject_grade_id
            AND csms.monthly_id = :monthly_id
            AND csms.isDeleted = 0";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':class_id', $class_id);
            $stmt->bindParam(':assign_subject_grade_id', $assign_subject_grade_id);
            $stmt->bindParam(':monthly_id', $monthly_id);
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
                'message' => 'Classroom subject monthly score not found'
            ];

        } catch (PDOException $e) {
            error_log("Error in getByClassSubjectMonthly: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Database error occurred'
            ];
        }
    }
} 