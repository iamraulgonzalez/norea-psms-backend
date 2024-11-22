<?php
require_once __DIR__ . '/../config/database.php';

class Classroom {
    private $conn;
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function fetchAll() {
        $query = "SELECT * FROM tbl_classroom";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
// Classroom.php (Model)

    public function create($data) {
        $class_name = isset($data['class_name']) ? $data['class_name'] : null;

        if ($class_name === null) {
            return false;
        }

        // Prepare the SQL query to insert a new classroom
        $query = "INSERT INTO tbl_classroom (class_name) VALUES (:class_name)";
        $stmt = $this->conn->prepare($query);

        // Bind the class_name parameter
        $stmt->bindParam(':class_name', $class_name);

        // Execute the query
        return $stmt->execute();
    }
   

    public function updateClassroom($id, $data) {
        $query = "UPDATE tbl_classroom SET class_name = :class_name WHERE class_id = :class_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':class_id', $id);
        $stmt->bindParam(':class_name', $data['class_name']);
        return $stmt->execute();
    }
    
    public function deleteClassroom($id) {
        $query = "DELETE FROM tbl_classroom WHERE class_id = :class_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':class_id', $id);
        return $stmt->execute();
    }
    
    public function fetchClassroomById($id) {
        $query = "SELECT * FROM tbl_classroom WHERE class_id = :class_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':class_id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}