<?php
require_once __DIR__ . '/../config/database.php';

class ClassroomSubject {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function addSubjectsToClass($classId, $subCodes) {
        try {
            $this->conn->beginTransaction();


            // Validate class exists
            $classQuery = "SELECT COUNT(*) FROM tbl_classroom WHERE class_id = ? AND isDeleted = 0";
            $stmt = $this->conn->prepare($classQuery);
            $stmt->execute([$classId]);
            if ($stmt->fetchColumn() == 0) {
                return [
                    'status' => 'error',
                    'message' => 'Class does not exist'
                ];
            }

            // Validate all sub_codes exist
            $placeholders = str_repeat('?,', count($subCodes) - 1) . '?';
            $subjectQuery = "SELECT sub_code FROM tbl_sub_subject 
                           WHERE sub_code IN ($placeholders) 
                           AND isDeleted = 0";
            $stmt = $this->conn->prepare($subjectQuery);
            $stmt->execute($subCodes);
            $validSubjects = $stmt->fetchAll(PDO::FETCH_COLUMN);

            if (count($validSubjects) !== count($subCodes)) {
                $this->conn->rollBack();
                return [
                    'status' => 'error',
                    'message' => 'One or more invalid subject codes'
                ];
            }

            // Insert assignments
            $insertQuery = "INSERT INTO tbl_classroom_subject (class_id, sub_code) 
                          VALUES (?, ?)";
            $stmt = $this->conn->prepare($insertQuery);

            foreach ($subCodes as $subCode) {
                try {
                    $stmt->execute([$classId, $subCode]);
                } catch (PDOException $e) {
                    // Skip if duplicate entry
                    if ($e->getCode() != '23000') { // 23000 is duplicate entry error
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

    public function getSubjectsByClassId($classId) {
        try {
            // First get classroom details
            $classQuery = "SELECT 
                c.class_id,
                c.class_name,
                c.grade_id,
                c.num_students_in_class,
                g.grade_name
            FROM tbl_classroom c
            JOIN tbl_grade g ON c.grade_id = g.grade_id
            WHERE c.class_id = :class_id 
            AND c.isDeleted = 0";

            $stmt = $this->conn->prepare($classQuery);
            $stmt->bindParam(':class_id', $classId);
            $stmt->execute();
            
            $classData = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$classData) {
                return [
                    'status' => 'error',
                    'message' => 'Class not found'
                ];
            }

            // Debug: Print total sub-subjects per subject
            $debugQuery1 = "SELECT 
                s.subject_code, 
                s.subject_name, 
                COUNT(ss.sub_code) as total_subs
            FROM tbl_subject s
            LEFT JOIN tbl_sub_subject ss ON s.subject_code = ss.subject_code
            WHERE s.isDeleted = 0
            GROUP BY s.subject_code";
            $stmt = $this->conn->prepare($debugQuery1);
            $stmt->execute();
            error_log("\n=== Total sub-subjects per subject ===");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                error_log("Subject {$row['subject_name']}: {$row['total_subs']} sub-subjects");
            }

            // Debug: Print assigned sub-subjects for this class
            $debugQuery2 = "SELECT 
                s.subject_code,
                s.subject_name,
                ss.sub_code,
                ss.sub_subject_name
            FROM tbl_classroom_subject cs
            JOIN tbl_sub_subject ss ON cs.sub_code = ss.sub_code
            JOIN tbl_subject s ON ss.subject_code = s.subject_code
            WHERE cs.class_id = :class_id 
            AND cs.isDeleted = 0
            ORDER BY s.subject_code, ss.sub_code";
            $stmt = $this->conn->prepare($debugQuery2);
            $stmt->bindParam(':class_id', $classId);
            $stmt->execute();
            error_log("\n=== Assigned sub-subjects for class $classId ===");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                error_log("Subject {$row['subject_name']}: sub-subject {$row['sub_subject_name']} (code: {$row['sub_code']})");
            }

            // Modified main query to always get sub-subjects
            $subjectsQuery = "SELECT 
                s.subject_code,
                s.subject_name,
                GROUP_CONCAT(DISTINCT ss.sub_code) as sub_codes,
                GROUP_CONCAT(DISTINCT ss.sub_subject_name) as sub_names
            FROM tbl_classroom_subject cs
            JOIN tbl_sub_subject ss ON cs.sub_code = ss.sub_code
            JOIN tbl_subject s ON ss.subject_code = s.subject_code
            WHERE cs.class_id = :class_id 
            AND cs.isDeleted = 0
            GROUP BY s.subject_code, s.subject_name";

            $stmt = $this->conn->prepare($subjectsQuery);
            $stmt->bindParam(':class_id', $classId);
            $stmt->execute();
            
            $subjects = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $subCodes = explode(',', $row['sub_codes']);
                $subNames = explode(',', $row['sub_names']);
                
                $subSubjects = [];
                for ($i = 0; $i < count($subCodes); $i++) {
                    $subSubjects[] = [
                        'sub_code' => (int)$subCodes[$i],
                        'sub_subject_name' => $subNames[$i]
                    ];
                }
                
                $subjects[] = [
                    'subject_id' => (int)$row['subject_code'],
                    'subject_name' => $row['subject_name'],
                    'sub_subjects' => $subSubjects
                ];
            }

            $result = [
                'status' => 'success',
                'data' => [
                    'class_id' => (int)$classData['class_id'],
                    'class_name' => $classData['class_name'],
                    'grade_id' => (int)$classData['grade_id'],
                    'grade_name' => $classData['grade_name'],
                    'num_students_in_class' => (int)$classData['num_students_in_class'],
                    'subjects' => $subjects
                ]
            ];
            
            error_log("\n=== Final result ===");
            error_log(print_r($result, true));
            
            return $result;

        } catch (PDOException $e) {
            error_log("Error fetching class subjects: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Failed to fetch class and subjects: ' . $e->getMessage()
            ];
        }
    }

    public function deleteSubjectFromClass($classId, $subCode) {
        try {
            $query = "UPDATE tbl_classroom_subject 
                     SET isDeleted = 1 
                     WHERE class_id = :class_id 
                     AND sub_code = :sub_code";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':class_id', $classId);
            $stmt->bindParam(':sub_code', $subCode);

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
} 