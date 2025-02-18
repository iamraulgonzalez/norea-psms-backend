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
                    t.teacher_name,
                    t.class_id,
                    c.class_name    
                  FROM tbl_teacher t
                  INNER JOIN tbl_classroom c ON t.class_id = c.class_id
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
            $class_id = isset($data['class_id']) ? $data['class_id'] : null;

            if (!$teacher_name || !$class_id) {
                return ["error" => "All fields (teacher_name, class_id) are required."];
            }

            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM tbl_classroom WHERE class_id = :class_id AND isDeleted = 0");
            $stmt->bindParam(':class_id', $class_id);
            $stmt->execute();
            $class_exists = $stmt->fetchColumn();
        
            if (!$class_exists) {
                return ["error" => "Invalid class_id: $class_id does not exist."];
            }

            $stmt = $this->conn->prepare("INSERT INTO tbl_teacher (teacher_id, teacher_name, class_id) 
                                        VALUES (:teacher_id, :teacher_name, :class_id)");
            $stmt->bindParam(':teacher_id', $new_teacher_id);
            $stmt->bindParam(':teacher_name', $teacher_name);
            $stmt->bindParam(':class_id', $class_id);
        
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
                  SET teacher_name = :teacher_name, 
                      class_id = :class_id
                  WHERE teacher_id = :teacher_id AND isDeleted = 0";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':teacher_id', $id);
        $stmt->bindParam(':teacher_name', $data['teacher_name']);
        $stmt->bindParam(':class_id', $data['class_id']);

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
                    t.teacher_name,
                    t.class_id,
                    c.class_name
                  FROM tbl_teacher t
                  LEFT JOIN tbl_classroom c ON t.class_id = c.class_id
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

    public function getTeachersByClassId($class_id) {
        try {
            $query = "SELECT 
                        t.teacher_id,
                        t.teacher_name
                     FROM tbl_teacher t
                     WHERE t.class_id = :class_id AND t.isDeleted = 0";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':class_id', $class_id);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching teachers by class_id: " . $e->getMessage());
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