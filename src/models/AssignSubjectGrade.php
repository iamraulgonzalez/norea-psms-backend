<?php
require_once __DIR__ . '/../config/database.php';

class AssignSubjectGrade {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    public function getAllAssignSubjectGrades() {
        try {
            $query = "SELECT 
                asg.assign_subject_grade_id,
                asg.grade_id,
                g.grade_name,
                s.subject_code,
                s.subject_name
            FROM tbl_assign_subject_grade asg
            JOIN tbl_grade g ON asg.grade_id = g.grade_id
            JOIN tbl_subject s ON asg.subject_code = s.subject_code
            WHERE asg.isDeleted = 0 
            AND s.isDeleted = 0 
            ORDER BY g.grade_name, s.subject_name";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Group by grade
            $gradeSubjects = [];
            foreach ($results as $row) {
                $gradeId = $row['grade_id'];
                
                // Initialize grade if not exists
                if (!isset($gradeSubjects[$gradeId])) {
                    $gradeSubjects[$gradeId] = [
                        'grade_id' => (int)$gradeId,
                        'grade_name' => $row['grade_name'],
                        'subjects' => []
                    ];
                }

                // Add subject
                $gradeSubjects[$gradeId]['subjects'][] = [
                    'assign_subject_grade_id' => (int)$row['assign_subject_grade_id'],
                    'subject_code' => (int)$row['subject_code'],
                    'subject_name' => $row['subject_name']
                ];
            }

            return [
                'status' => 'success',
                'data' => array_values($gradeSubjects)
            ];

        } catch (PDOException $e) {
            error_log("Error fetching all grade subjects: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Failed to fetch grades and subjects'
            ];
        }
    }
    
    public function getSubjectsByGradeId($gradeId) {
        try {
            $query = "SELECT 
                asg.assign_subject_grade_id,
                g.grade_id,
                g.grade_name,
                s.subject_code,
                s.subject_name
            FROM tbl_grade g
            LEFT JOIN tbl_assign_subject_grade asg ON g.grade_id = asg.grade_id AND asg.isDeleted = 0
            LEFT JOIN tbl_subject s ON asg.subject_code = s.subject_code AND s.isDeleted = 0
            WHERE g.grade_id = :grade_id 
            AND g.isDeleted = 0
            ORDER BY s.subject_name";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':grade_id', $gradeId);
            $stmt->execute();
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($results)) {
                return [
                    'status' => 'error',
                    'message' => 'Grade not found or no subjects assigned'
                ];
            }

            // Structure the response
            $gradeSubjects = [
                'grade_id' => (int)$results[0]['grade_id'],
                'grade_name' => $results[0]['grade_name'],
                'subjects' => []
            ];

            foreach ($results as $row) {
                if ($row['subject_code']) {
                    $gradeSubjects['subjects'][] = [
                        'assign_subject_grade_id' => (int)$row['assign_subject_grade_id'],
                        'subject_code' => (int)$row['subject_code'],
                        'subject_name' => $row['subject_name']
                    ];
                }
            }

            return [
                'status' => 'success',
                'data' => $gradeSubjects
            ];

        } catch (PDOException $e) {
            error_log("Error fetching grade subjects: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Failed to fetch grade and subjects'
            ];
        }
    }

    public function addSubjectsToGrade($gradeId, $codes) {
        try {
            $this->conn->beginTransaction();

            // Validate grade exists
            $gradeQuery = "SELECT COUNT(*) FROM tbl_grade WHERE grade_id = ? AND isDeleted = 0";
            $stmt = $this->conn->prepare($gradeQuery);
            $stmt->execute([$gradeId]);
            if ($stmt->fetchColumn() == 0) {
                return [
                    'status' => 'error',
                    'message' => 'Grade does not exist'
                ];
            }

            // Validate subject codes exist
            $placeholders = str_repeat('?,', count($codes) - 1) . '?';
            $validationQuery = "SELECT subject_code FROM tbl_subject 
                              WHERE subject_code IN ($placeholders) 
                              AND isDeleted = 0";
            
            $stmt = $this->conn->prepare($validationQuery);
            $stmt->execute($codes);
            $validCodes = $stmt->fetchAll(PDO::FETCH_COLUMN);

            if (count($validCodes) !== count($codes)) {
                $this->conn->rollBack();
                return [
                    'status' => 'error',
                    'message' => 'One or more invalid subject codes'
                ];
            }

            // Insert subjects
            $insertQuery = "INSERT INTO tbl_assign_subject_grade 
                          (grade_id, subject_code) 
                          VALUES (?, ?)";
            $stmt = $this->conn->prepare($insertQuery);

            foreach ($codes as $code) {
                try {
                    $stmt->execute([$gradeId, $code]);
                } catch (PDOException $e) {
                    // Skip if duplicate entry
                    if ($e->getCode() != '23000') {
                        throw $e;
                    }
                }
            }

            $this->conn->commit();
            return [
                'status' => 'success',
                'message' => 'Subjects assigned successfully'
            ];

        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Error assigning subjects: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Failed to assign subjects'
            ];
        }
    }

    public function deleteSubjectFromGrade($assignSubjectGradeId) {
        try {
            $query = "UPDATE tbl_assign_subject_grade 
                     SET isDeleted = 1 
                     WHERE assign_subject_grade_id = :assign_subject_grade_id";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':assign_subject_grade_id', $assignSubjectGradeId);

            if ($stmt->execute()) {
                return [
                    'status' => 'success',
                    'message' => 'Subject removed successfully'
                ];
            }

            return [
                'status' => 'error',
                'message' => 'Failed to remove subject'
            ];

        } catch (PDOException $e) {
            error_log("Error removing subject: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Database error occurred'
            ];
        }
    }

    public function getAllSubjectsAndSubSubjects() {
        try {
            $query = "SELECT 
                subject_code,
                subject_name
            FROM tbl_subject
            WHERE isDeleted = 0 
            ORDER BY subject_name";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $subjects = array_map(function($row) {
                return [
                    'subject_code' => (int)$row['subject_code'],
                    'subject_name' => $row['subject_name']
                ];
            }, $results);

            return [
                'status' => 'success',
                'data' => $subjects
            ];

        } catch (PDOException $e) {
            error_log("Error fetching subjects: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Failed to fetch subjects'
            ];
        }
    }
}