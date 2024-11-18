<?php
require_once __DIR__ . '/../config/database.php';

class Student {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Fetch all students
    public function fetchAll() {
        $query = "SELECT * FROM tbl_student_info";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Create a new student
    public function create($data) {
        $query = "INSERT INTO tbl_student_info 
                    (student_name, gender, dob, pob_province, pob_district, current_province, current_district, father_name, mother_name) 
                  VALUES 
                    (:student_name, :gender, :dob, :pob_province, :pob_district, :current_province, :current_district, :father_name, :mother_name)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':student_name', $data['student_name']);
        $stmt->bindParam(':gender', $data['gender']);
        $stmt->bindParam(':dob', $data['dob']);
        $stmt->bindParam(':pob_province', $data['pob_province']);
        $stmt->bindParam(':pob_district', $data['pob_district']);
        $stmt->bindParam(':current_province', $data['current_province']);
        $stmt->bindParam(':current_district', $data['current_district']);
        $stmt->bindParam(':father_name', $data['father_name']);
        $stmt->bindParam(':mother_name', $data['mother_name']);
        return $stmt->execute();
    }

    // Update an existing student
    public function update($id, $data) {
        $query = "UPDATE tbl_student_info 
                  SET student_name = :student_name, 
                      gender = :gender, 
                      dob = :dob, 
                      pob_province = :pob_province, 
                      pob_district = :pob_district, 
                      current_province = :current_province, 
                      current_district = :current_district, 
                      father_name = :father_name, 
                      mother_name = :mother_name 
                  WHERE student_id = :student_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':student_id', $id);
        $stmt->bindParam(':student_name', $data['student_name']);
        $stmt->bindParam(':gender', $data['gender']);
        $stmt->bindParam(':dob', $data['dob']);
        $stmt->bindParam(':pob_province', $data['pob_province']);
        $stmt->bindParam(':pob_district', $data['pob_district']);
        $stmt->bindParam(':current_province', $data['current_province']);
        $stmt->bindParam(':current_district', $data['current_district']);
        $stmt->bindParam(':father_name', $data['father_name']);
        $stmt->bindParam(':mother_name', $data['mother_name']);
        return $stmt->execute();
    }

    // Delete a student
    public function delete($id) {
        $query = "DELETE FROM tbl_student_info WHERE student_id = :student_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':student_id', $id);
        return $stmt->execute();
    }

    // Fetch a single student by ID
    public function fetchById($id) {
        $query = "SELECT * FROM tbl_student_info WHERE student_id = :student_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':student_id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}