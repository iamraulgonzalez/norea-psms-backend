<?php

require_once __DIR__ . '/../config/database.php';

class SubSubject{
    private $conn;
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function fetchAll() {
        try {
            $query = "SELECT 
                        ss.sub_code,
                        ss.subject_code,
                        ss.sub_subject_name,
                        s.subject_name
                    FROM tbl_sub_subject ss
                    LEFT JOIN tbl_subject s ON ss.subject_code = s.subject_code
                    WHERE ss.isDeleted = 0
                    ORDER BY s.subject_name, ss.sub_subject_name";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Group by subject
            $groupedSubjects = [];
            foreach ($results as $row) {
                $subjectCode = $row['subject_code'];
                
                if (!isset($groupedSubjects[$subjectCode])) {
                    $groupedSubjects[$subjectCode] = [
                        'subject_code' => (int)$subjectCode,
                        'subject_name' => $row['subject_name'],
                        'sub_subjects' => []
                    ];
                }

                $groupedSubjects[$subjectCode]['sub_subjects'][] = [
                    'sub_code' => (int)$row['sub_code'],
                    'sub_subject_name' => $row['sub_subject_name']
                ];
            }

            return [
                'status' => 'success',
                'data' => array_values($groupedSubjects)
            ];

        } catch (PDOException $e) {
            error_log("Database Error in fetchAll: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Failed to fetch subjects'
            ];
        }
    }
    
    public function fetchBySubjectCode($subjectCode) {
        try {
            $query = "SELECT 
                        ss.sub_code,
                        ss.subject_code,
                        ss.sub_subject_name,
                        s.subject_name
                    FROM tbl_sub_subject ss
                    LEFT JOIN tbl_subject s ON ss.subject_code = s.subject_code
                    WHERE ss.subject_code = :subject_code 
                    AND ss.isDeleted = 0
                    ORDER BY ss.sub_subject_name";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':subject_code', $subjectCode);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database Error in fetchBySubjectCode: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function create($data) {
        try {
            $subject_code = isset($data['subject_code']) ? $data['subject_code'] : null;
            $sub_subject_name = isset($data['sub_subject_name']) ? $data['sub_subject_name'] : null;

            // Verify subject exists
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM tbl_subject WHERE subject_code = :subject_code AND isDeleted = 0");
            $stmt->bindParam(':subject_code', $subject_code);
            $stmt->execute();
            $subject_exists = $stmt->fetchColumn();

            if (!$subject_exists) {
                return ["error" => "Invalid subject_code: $subject_code does not exist."];
            }

            if ($subject_code && $sub_subject_name === null) {
                return false;
            }

            $query = "INSERT INTO tbl_sub_subject (subject_code, sub_subject_name) 
                     VALUES (:subject_code, :sub_subject_name)";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':subject_code', $subject_code);
            $stmt->bindParam(':sub_subject_name', $sub_subject_name);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Database Error in create: " . $e->getMessage());
            throw $e;
        }
    }
    public function update($id, $data) {
        try {
            // Verify subject exists
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM tbl_subject WHERE subject_code = :subject_code");
            $stmt->bindParam(':subject_code', $data['subject_code']);
            $stmt->execute();
            $subject_exists = $stmt->fetchColumn();

            if (!$subject_exists) {
                return ["error" => "Invalid subject_code: {$data['subject_code']} does not exist."];
            }

            $query = "UPDATE tbl_sub_subject 
                     SET subject_code = :subject_code, 
                         sub_subject_name = :sub_subject_name 
                     WHERE sub_code = :sub_code AND isDeleted = 0";
            $stmt = $this->conn->prepare($query);
        
            $stmt->bindParam(':sub_code', $id);
            $stmt->bindParam(':subject_code', $data['subject_code']);
            $stmt->bindParam(':sub_subject_name', $data['sub_subject_name']);
        
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Database Error in update: " . $e->getMessage());
            throw $e;
        }
    }
    
    
    public function delete($id) {
        try {
            // Using soft delete
            $query = "UPDATE tbl_sub_subject 
                     SET isDeleted = 1 
                     WHERE sub_code = :sub_code AND isDeleted = 0";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':sub_code', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Database Error in delete: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function fetchById($id) {
        try {
            $query = "SELECT 
                        ss.sub_code,
                        ss.subject_code,
                        ss.sub_subject_name,
                        s.subject_name
                    FROM tbl_sub_subject ss
                    LEFT JOIN tbl_subject s ON ss.subject_code = s.subject_code
                    WHERE ss.sub_code = :sub_code 
                    AND ss.isDeleted = 0";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':sub_code', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database Error in fetchById: " . $e->getMessage());
            throw $e;
        }
    }
}