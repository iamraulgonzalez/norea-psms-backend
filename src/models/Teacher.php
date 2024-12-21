<?php
require_once __DIR__ . '/../config/database.php';

class Teacher {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Fetch all teachers
    public function fetchAll() {
        $query = "SELECT * FROM tbl_teacher";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Create a new teacher
    public function create($data) {
        var_dump($data);

        $stmt = $this->conn->prepare("SELECT teacher_id FROM tbl_teacher ORDER BY teacher_id DESC LIMIT 1");
        $stmt->execute();
        $last_teacher_id = $stmt->fetchColumn();
        $new_teacher_id = $last_teacher_id ? (int)$last_teacher_id + 1 : 1001;
    
        // Extract input fields
        $teacher_name = isset($data['teacher_name']) ? $data['teacher_name'] : null;
        $class_id = isset($data['class_id']) ? $data['class_id'] : null;
        $year_study_id = isset($data['year_study_id']) ? $data['year_study_id'] : null;
    
        // Validate input
        if (!$teacher_name || !$class_id || !$year_study_id) {
            return ["error" => "All fields (teacher_name, class_id, year_study_id) are required."];
        }
    
        // Validate foreign key references
        // Check if class_id exists in tbl_classroom
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM tbl_classroom WHERE class_id = :class_id");
        $stmt->bindParam(':class_id', $class_id);
        $stmt->execute();
        $class_exists = $stmt->fetchColumn();
    
        if (!$class_exists) {
            return ["error" => "Invalid class_id: $class_id does not exist."];
        }
    
        // Check if year_study_id exists in tbl_year_study
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM tbl_year_study WHERE year_study_id = :year_study_id");
        $stmt->bindParam(':year_study_id', $year_study_id);
        $stmt->execute();
        $year_study_exists = $stmt->fetchColumn();
    
        if (!$year_study_exists) {
            return ["error" => "Invalid year_study_id: $year_study_id does not exist."];
        }
    
        // Insert teacher record
        $stmt = $this->conn->prepare("INSERT INTO tbl_teacher (teacher_id, teacher_name, class_id, year_study_id) 
                                      VALUES (:teacher_id, :teacher_name, :class_id, :year_study_id)");
        $stmt->bindParam(':teacher_id', $new_teacher_id);
        $stmt->bindParam(':teacher_name', $teacher_name);
        $stmt->bindParam(':class_id', $class_id);
        $stmt->bindParam(':year_study_id', $year_study_id);
    
        if ($stmt->execute()) {
            return ["message" => "Teacher created successfully"];
        } else {
            return ["error" => "Failed to create teacher. Please check your input."];
        }
    }
    
    // Update an existing teacher
    public function update($id, $data) {
        $query = "UPDATE tbl_teacher 
                  SET teacher_name = :teacher_name, 
                      class_id = :class_id, 
                      year_study_id = :year_study_id
                  WHERE teacher_id = :teacher_id";
        $stmt = $this->conn->prepare($query);

        // Bind values
        $stmt->bindParam(':teacher_id', $id);
        $stmt->bindParam(':teacher_name', $data['teacher_name']);
        $stmt->bindParam(':class_id', $data['class_id']);
        $stmt->bindParam(':year_study_id', $data['year_study_id']);

        return $stmt->execute();
    }

    // Delete a teacher
    public function delete($id) {
        $query = "DELETE FROM tbl_teacher WHERE teacher_id = :teacher_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':teacher_id', $id);
        return $stmt->execute();
    }

    // Fetch a single teacher by ID
    public function fetchById($id) {
        $query = "SELECT * FROM tbl_teacher WHERE teacher_id = :teacher_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':teacher_id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}