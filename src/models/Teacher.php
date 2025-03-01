<?php
require_once __DIR__ . '/../config/database.php';

class Teacher {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function fetchAll() {
        $query = "SELECT 
                    t.teacher_id,
                    t.teacher_name
                  FROM tbl_teacher t
                  WHERE t.isDeleted = 0";
        $stmt = $this->conn->prepare($query);

        try {
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching teachers: " . $e->getMessage());
            throw $e;
        }
    }

    public function create($data) {
        try {
            $stmt = $this->conn->prepare("SELECT teacher_id FROM tbl_teacher WHERE isDeleted = 0 ORDER BY teacher_id DESC LIMIT 1");
            $stmt->execute();
            $last_teacher_id = $stmt->fetchColumn();
            $new_teacher_id = $last_teacher_id ? (int)$last_teacher_id + 1 : 1001;
        
            $teacher_name = isset($data['teacher_name']) ? $data['teacher_name'] : null;

            if (!$teacher_name) {
                return ["error" => "Teacher name is required."];
            }

            $stmt = $this->conn->prepare("INSERT INTO tbl_teacher (teacher_id, teacher_name) 
                                        VALUES (:teacher_id, :teacher_name)");
            $stmt->bindParam(':teacher_id', $new_teacher_id);
            $stmt->bindParam(':teacher_name', $teacher_name);
        
            if ($stmt->execute()) {
                return ["message" => "Teacher created successfully"];
            } else {
                return ["error" => "Failed to create teacher. Please check your input."];
            }
        } catch (PDOException $e) {
            error_log("Error creating teacher: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function update($id, $data) {
        $query = "UPDATE tbl_teacher 
                  SET teacher_name = :teacher_name
                  WHERE teacher_id = :teacher_id AND isDeleted = 0";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':teacher_id', $id);
        $stmt->bindParam(':teacher_name', $data['teacher_name']);

        return $stmt->execute();
    }

    public function delete($id) {
        $query = "UPDATE tbl_teacher SET isDeleted = 1 WHERE teacher_id = :teacher_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':teacher_id', $id);
        return $stmt->execute();
    }

    public function fetchById($id) {
        $query = "SELECT 
                    t.teacher_id,
                    t.teacher_name
                  FROM tbl_teacher t
                  WHERE t.teacher_id = :teacher_id AND t.isDeleted = 0";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':teacher_id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching teacher: " . $e->getMessage());
            throw $e;
        }
    }

    public function getCount() {
        try {
            $query = "SELECT COUNT(*) as count 
                     FROM tbl_teacher 
                     WHERE isDeleted = 0";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        } catch (PDOException $e) {
            error_log("Error in getCount: " . $e->getMessage());
            throw $e;
        }
    }
}