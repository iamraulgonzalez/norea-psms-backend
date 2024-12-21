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
        var_dump($data);  // For debugging
        
        $stmt = $this->conn->prepare("SELECT student_id FROM tbl_student_info ORDER BY student_id DESC LIMIT 1");
        $stmt->execute();
        $last_student_id = $stmt->fetchColumn();
        
        if ($last_student_id) {
            $last_number = (int)$last_student_id;
        } else {
            $last_number = 1000;
        }
        $new_student_number = $last_number + 1;
        
        do {
            $student_id = $new_student_number;
            
            // Prepare the query to check if the student_id exists
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM tbl_student_info WHERE student_id = :student_id");
            $stmt->bindParam(':student_id', $student_id);
            $stmt->execute();
            $existing_id_count = $stmt->fetchColumn();
            
            if ($existing_id_count > 0) {
                $new_student_number++;
            }
        } while ($existing_id_count > 0);
        
        $student_name = isset($data['student_name']) ? $data['student_name'] : null;
        $gender = isset($data['gender']) ? $data['gender'] : null;
        $dob = isset($data['dob']) ? $data['dob'] : null;
        $pob_village = isset($data['pob_village']) ? $data['pob_village'] : null;
        $pob_commune = isset($data['pob_commune']) ? $data['pob_commune'] : null;
        $pob_province = isset($data['pob_province']) ? $data['pob_province'] : null;
        $pob_district = isset($data['pob_district']) ? $data['pob_district'] : null;
        $current_village = isset($data['current_village']) ? $data['current_village'] : null;
        $current_commune = isset($data['current_commune']) ? $data['current_commune'] : null;
        $current_province = isset($data['current_province']) ? $data['current_province'] : null;
        $current_district = isset($data['current_district']) ? $data['current_district'] : null;
        $father_name = isset($data['father_name']) ? $data['father_name'] : null;
        $father_job = isset($data['father_job']) ? $data['father_job'] : null;
        $father_phone = isset($data['father_phone']) ? $data['father_phone'] : null;
        $mother_name = isset($data['mother_name']) ? $data['mother_name'] : null;
        $mother_job = isset($data['mother_job']) ? $data['mother_job'] : null;
        $mother_phone = isset($data['mother_phone']) ? $data['mother_phone'] : null;
        $family_status = isset($data['family_status']) ? $data['family_status'] : null;
        
        // Prepare and execute the INSERT statement
        $stmt = $this->conn->prepare("INSERT INTO tbl_student_info 
                                      (student_id, student_name, gender, dob, pob_village, pob_commune, pob_province, pob_district, 
                                       current_village, current_commune, current_province, current_district, father_name, 
                                       father_job, father_phone, mother_name, mother_job, mother_phone, family_status) 
                                      VALUES 
                                      (:student_id, :student_name, :gender, :dob, :pob_village, :pob_commune, :pob_province, 
                                       :pob_district, :current_village, :current_commune, :current_province, :current_district, 
                                       :father_name, :father_job, :father_phone, :mother_name, :mother_job, :mother_phone, :family_status)");
        
        // Bind values
        $stmt->bindParam(':student_id', $student_id);
        $stmt->bindParam(':student_name', $student_name);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':dob', $dob);
        $stmt->bindParam(':pob_village', $pob_village);
        $stmt->bindParam(':pob_commune', $pob_commune);
        $stmt->bindParam(':pob_province', $pob_province);
        $stmt->bindParam(':pob_district', $pob_district);
        $stmt->bindParam(':current_village', $current_village);
        $stmt->bindParam(':current_commune', $current_commune);
        $stmt->bindParam(':current_province', $current_province);
        $stmt->bindParam(':current_district', $current_district);
        $stmt->bindParam(':father_name', $father_name);
        $stmt->bindParam(':father_job', $father_job);
        $stmt->bindParam(':father_phone', $father_phone);
        $stmt->bindParam(':mother_name', $mother_name);
        $stmt->bindParam(':mother_job', $mother_job);
        $stmt->bindParam(':mother_phone', $mother_phone);
        $stmt->bindParam(':family_status', $family_status);
        
        return $stmt->execute();
    }        
    // Update an existing student
    public function update($id, $data) {
        // First, check if the student_id exists in the database
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM tbl_student_info WHERE student_id = :student_id");
        $stmt->bindParam(':student_id', $id);
        $stmt->execute();
        $exists = $stmt->fetchColumn();
    
        // If student_id doesn't exist, return false or handle as needed
        if ($exists == 0) {
            return false;
        }
    
        // Prepare the UPDATE query
        $query = "UPDATE tbl_student_info 
                  SET student_name = :student_name, 
                      gender = :gender, 
                      dob = :dob, 
                      pob_village = :pob_village, 
                      pob_commune = :pob_commune, 
                      pob_province = :pob_province, 
                      pob_district = :pob_district, 
                      current_village = :current_village, 
                      current_commune = :current_commune, 
                      current_province = :current_province, 
                      current_district = :current_district, 
                      father_name = :father_name, 
                      father_job = :father_job, 
                      father_phone = :father_phone, 
                      mother_name = :mother_name, 
                      mother_job = :mother_job, 
                      mother_phone = :mother_phone, 
                      family_status = :family_status
                  WHERE student_id = :student_id";
        
        // Prepare the statement for execution
        $stmt = $this->conn->prepare($query);
        
        // Bind the parameters
        $stmt->bindParam(':student_id', $id);
        $stmt->bindParam(':student_name', $data['student_name']);
        $stmt->bindParam(':gender', $data['gender']);
        $stmt->bindParam(':dob', $data['dob']);
        $stmt->bindParam(':pob_village', $data['pob_village']);
        $stmt->bindParam(':pob_commune', $data['pob_commune']);
        $stmt->bindParam(':pob_province', $data['pob_province']);
        $stmt->bindParam(':pob_district', $data['pob_district']);
        $stmt->bindParam(':current_village', $data['current_village']);
        $stmt->bindParam(':current_commune', $data['current_commune']);
        $stmt->bindParam(':current_province', $data['current_province']);
        $stmt->bindParam(':current_district', $data['current_district']);
        $stmt->bindParam(':father_name', $data['father_name']);
        $stmt->bindParam(':father_job', $data['father_job']);
        $stmt->bindParam(':father_phone', $data['father_phone']);
        $stmt->bindParam(':mother_name', $data['mother_name']);
        $stmt->bindParam(':mother_job', $data['mother_job']);
        $stmt->bindParam(':mother_phone', $data['mother_phone']);
        $stmt->bindParam(':family_status', $data['family_status']);
        
        // Execute the query and return the result
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