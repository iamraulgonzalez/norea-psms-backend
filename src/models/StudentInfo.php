<?php
require_once __DIR__ . '/../config/Database.php';

class Student {
    private $conn;
    private $table = 'tbl_student_info';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function fetchAll() {
        try {
            $query = "SELECT 
                        s.student_id, 
                        s.student_name, 
                        s.gender, 
                        s.dob, 
                        s.pob_address, 
                        s.current_address, 
                        s.class_id, 
                        s.father_name,
                        s.father_job,
                        s.father_phone,
                        s.mother_name,
                        s.mother_job,
                        s.mother_phone,
                        s.family_status,
                        s.status,
                        c.class_name
                      FROM tbl_student_info s
                      LEFT JOIN tbl_classroom c ON s.class_id = c.class_id
                      WHERE s.isDeleted = 0
                      ORDER BY s.student_id";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database Error in fetchAll: " . $e->getMessage());
            throw $e;
        }
    }

    public function create($data) {
        try {
            // Check classroom capacity before creating student
            $class_id = isset($data['class_id']) ? $data['class_id'] : null;
            if ($class_id) {
                $checkCapacityQuery = "SELECT 
                    (SELECT COUNT(*) FROM tbl_student_info 
                     WHERE class_id = :class_id AND isDeleted = 0) as current_count,
                    c.num_students_in_class as max_capacity,
                    c.class_name,
                    g.grade_name
                    FROM tbl_classroom c 
                    INNER JOIN tbl_grade g ON c.grade_id = g.grade_id
                    WHERE c.class_id = :class_id";
                
                $stmt = $this->conn->prepare($checkCapacityQuery);
                $stmt->bindParam(':class_id', $class_id);
                $stmt->execute();
                $capacityInfo = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($capacityInfo && $capacityInfo['current_count'] >= $capacityInfo['max_capacity']) {
                    return [
                        'status' => 'error',
                        'message' => "ថ្នាក់ទី " . $capacityInfo['grade_name'] . " " . $capacityInfo['class_name'] . 
                                    " ពេញហើយ (" . $capacityInfo['current_count'] . "/" . $capacityInfo['max_capacity'] . ")"
                    ];
                }
            }

            error_log("Starting student creation with data: " . json_encode($data));
            
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
            $class_id = isset($data['class_id']) ? $data['class_id'] : null;
            $pob_address = isset($data['pob_address']) ? $data['pob_address'] : null;
            $current_address = isset($data['current_address']) ? $data['current_address'] : null;
            $father_name = isset($data['father_name']) ? $data['father_name'] : null;
            $father_job = isset($data['father_job']) ? $data['father_job'] : null;
            $father_phone = isset($data['father_phone']) ? $data['father_phone'] : null;
            $mother_name = isset($data['mother_name']) ? $data['mother_name'] : null;
            $mother_job = isset($data['mother_job']) ? $data['mother_job'] : null;
            $mother_phone = isset($data['mother_phone']) ? $data['mother_phone'] : null;
            $family_status = isset($data['family_status']) ? $data['family_status'] : null;
            $status = isset($data['status']) ? $data['status'] : null;
            
            $stmt = $this->conn->prepare("INSERT INTO tbl_student_info 
                                          (student_id, student_name, gender, dob, pob_address, current_address, class_id, 
                                           father_name, father_job, father_phone, mother_name, mother_job, mother_phone, 
                                           family_status, status) 
                                          VALUES 
                                          (:student_id, :student_name, :gender, :dob, :pob_address, :current_address, :class_id, 
                                           :father_name, :father_job, :father_phone, :mother_name, :mother_job, :mother_phone, 
                                           :family_status, :status)");
            
            $stmt->bindParam(':student_id', $student_id);
            $stmt->bindParam(':student_name', $student_name);
            $stmt->bindParam(':gender', $gender);
            $stmt->bindParam(':dob', $dob);
            $stmt->bindParam(':pob_address', $pob_address);
            $stmt->bindParam(':current_address', $current_address);
            $stmt->bindParam(':class_id', $class_id);
            $stmt->bindParam(':father_name', $father_name);
            $stmt->bindParam(':father_job', $father_job);
            $stmt->bindParam(':father_phone', $father_phone);
            $stmt->bindParam(':mother_name', $mother_name);
            $stmt->bindParam(':mother_job', $mother_job);
            $stmt->bindParam(':mother_phone', $mother_phone);
            $stmt->bindParam(':family_status', $family_status);
            $stmt->bindParam(':status', $status);

            $result = $stmt->execute();
            
            if (!$result) {
                error_log("SQL Error: " . json_encode($stmt->errorInfo()));
            }
            
            if ($result) {
                return [
                    'status' => 'success',
                    'message' => 'សិស្សត្រូវបានបង្កើតដោយជោគជ័យ'
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'មានបញ្ហាក្នុងការបង្កើតសិស្ស'
                ];
            }
        } catch (PDOException $e) {
            error_log("Database Error: " . $e->getMessage());
            throw $e;
        } catch (Exception $e) {
            error_log("General Error: " . $e->getMessage());
            throw $e;
        }
    }        

    public function update($id, $data) {
        try {
            // Check classroom capacity if class_id is being updated
            if (isset($data['class_id'])) {
                $class_id = $data['class_id'];
                
                error_log("Checking capacity for class_id: " . $class_id);
                
                // Get current class_id of the student
                $currentClassQuery = "SELECT class_id FROM " . $this->table . " WHERE student_id = :student_id AND isDeleted = 0";
                $stmt = $this->conn->prepare($currentClassQuery);
                $stmt->bindParam(':student_id', $id);
                $stmt->execute();
                $currentClassId = $stmt->fetchColumn();
                
                error_log("Current class_id: " . $currentClassId . ", New class_id: " . $class_id);
                
                // Only check capacity if moving to a different class
                if ($currentClassId != $class_id) {
                    $checkCapacityQuery = "SELECT 
                        (SELECT COUNT(*) FROM tbl_student_info 
                         WHERE class_id = :class_id AND isDeleted = 0) as current_count,
                        c.num_students_in_class as max_capacity,
                        c.class_name,
                        g.grade_name
                        FROM tbl_classroom c 
                        INNER JOIN tbl_grade g ON c.grade_id = g.grade_id
                        WHERE c.class_id = :class_id";
                    
                    $stmt = $this->conn->prepare($checkCapacityQuery);
                    $stmt->bindParam(':class_id', $class_id);
                    $stmt->execute();
                    $capacityInfo = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    error_log("Capacity check results: " . json_encode($capacityInfo));
                    
                    if ($capacityInfo && intval($capacityInfo['current_count']) >= intval($capacityInfo['max_capacity'])) {
                        $message = "ថ្នាក់ទី " . $capacityInfo['grade_name'] . " " . $capacityInfo['class_name'] . 
                                  " ពេញហើយ (" . $capacityInfo['current_count'] . "/" . $capacityInfo['max_capacity'] . ")";
                        error_log("Capacity exceeded: " . $message);
                        return [
                            'status' => 'error',
                            'message' => $message,
                            'debug' => [
                                'current_count' => intval($capacityInfo['current_count']),
                                'max_capacity' => intval($capacityInfo['max_capacity'])
                            ]
                        ];
                    }
                }
            }

            // Check if student exists
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM tbl_student_info WHERE student_id = :student_id AND isDeleted = 0");
            $stmt->bindParam(':student_id', $id);
            $stmt->execute();
            $exists = $stmt->fetchColumn();
        
            if ($exists == 0) {
                return [
                    'status' => 'error',
                    'message' => 'រកមិនឃើញលេខសម្គាល់សិស្ស: ' . $id
                ];
            }
        
            $query = "UPDATE tbl_student_info 
                     SET student_name = :student_name, 
                         gender = :gender, 
                         dob = :dob, 
                         class_id = :class_id,
                         pob_address = :pob_address, 
                         current_address = :current_address, 
                         father_name = :father_name, 
                         father_job = :father_job, 
                         father_phone = :father_phone,
                         mother_name = :mother_name, 
                         mother_job = :mother_job, 
                         mother_phone = :mother_phone,
                         family_status = :family_status,
                         status = :status
                     WHERE student_id = :student_id";

            $stmt = $this->conn->prepare($query);
            
            // Validate and set default values if needed
            $student_name = isset($data['student_name']) ? $data['student_name'] : null;
            $gender = isset($data['gender']) ? $data['gender'] : null;
            $dob = isset($data['dob']) ? $data['dob'] : null;
            $pob_address = isset($data['pob_address']) ? $data['pob_address'] : null;
            $current_address = isset($data['current_address']) ? $data['current_address'] : null;
            $class_id = isset($data['class_id']) ? $data['class_id'] : null;
            $father_name = isset($data['father_name']) ? $data['father_name'] : null;
            $father_job = isset($data['father_job']) ? $data['father_job'] : null;
            $father_phone = isset($data['father_phone']) ? $data['father_phone'] : null;
            $mother_name = isset($data['mother_name']) ? $data['mother_name'] : null;
            $mother_job = isset($data['mother_job']) ? $data['mother_job'] : null;
            $mother_phone = isset($data['mother_phone']) ? $data['mother_phone'] : null;
            $family_status = isset($data['family_status']) ? $data['family_status'] : null;
            $status = isset($data['status']) ? $data['status'] : null;

            // Bind parameters
            $stmt->bindParam(':student_id', $id);
            $stmt->bindParam(':student_name', $student_name);
            $stmt->bindParam(':gender', $gender);
            $stmt->bindParam(':dob', $dob);
            $stmt->bindParam(':pob_address', $pob_address);
            $stmt->bindParam(':current_address', $current_address);
            $stmt->bindParam(':class_id', $class_id);
            $stmt->bindParam(':father_name', $father_name);
            $stmt->bindParam(':father_job', $father_job);
            $stmt->bindParam(':father_phone', $father_phone);
            $stmt->bindParam(':mother_name', $mother_name);
            $stmt->bindParam(':mother_job', $mother_job);
            $stmt->bindParam(':mother_phone', $mother_phone);
            $stmt->bindParam(':family_status', $family_status);
            $stmt->bindParam(':status', $status);
            
            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    return [
                        'status' => 'success',
                        'message' => 'សិស្សត្រូវបានធ្វើបច្ចុប្បន្នភាពដោយជោគជ័យ'
                    ];
                } else {
                    return [
                        'status' => 'info',
                        'message' => 'គ្មានការផ្លាស់ប្តូរទិន្នន័យទេ'
                    ];
                }
            }

            return [
                'status' => 'error',
                'message' => 'មានបញ្ហាក្នុងការធ្វើបច្ចុប្បន្នភាពសិស្ស'
            ];
            
        } catch (PDOException $e) {
            error_log("Database Error in update: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'មានបញ្ហាក្នុងការភ្ជាប់ទៅកាន់ទិន្នន័យ'
            ];
        }
    }
    
    // Delete a student
    public function delete($id) {
        try {
            error_log("Attempting to soft delete student ID: " . $id);
            
            // First check if student exists and isn't already deleted
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM tbl_student_info WHERE student_id = :student_id AND isDeleted = 0");
            $stmt->bindParam(':student_id', $id);
            $stmt->execute();
            $exists = $stmt->fetchColumn();
            
            if ($exists == 0) {
                error_log("Student ID not found or already deleted: " . $id);
                return false;
            }
            
            // Perform soft delete by updating isDeleted field
            $query = "UPDATE tbl_student_info SET isDeleted = 1 WHERE student_id = :student_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':student_id', $id);
            
            $result = $stmt->execute();
            
            if (!$result) {
                error_log("Soft delete failed. SQL Error: " . json_encode($stmt->errorInfo()));
                return false;
            }
            
            error_log("Student soft deleted successfully. ID: " . $id);
            return true;
            
        } catch (PDOException $e) {
            error_log("Database Error in delete: " . $e->getMessage());
            throw $e;
        }
    }

    public function fetchById($id) {
        try {
            $query = "SELECT * FROM " . $this->table . " WHERE student_id = :student_id AND isDeleted = 0";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':student_id', $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database Error in fetchById: " . $e->getMessage());
            throw $e;
        }
    }

    public function fetchByClassId($class_id) {
        try {
            $query = "SELECT 
                s.student_id, 
                s.student_name, 
                s.gender, 
                s.dob, 
                s.status,
                c.class_name 
            FROM tbl_student_info s
            LEFT JOIN tbl_classroom c ON s.class_id = c.class_id
            WHERE s.class_id = :class_id 
            AND s.isDeleted = 0";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':class_id', $class_id);
            $stmt->execute();
            
            $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'status' => 'success',
                'data' => $students
            ];
            
        } catch (PDOException $e) {
            error_log("Database Error in fetchByClassId: " . $e->getMessage());
            throw $e;
        }
    }

    public function getCount() {
        try {
            $query = "SELECT COUNT(*) as count 
                     FROM tbl_student_info 
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
