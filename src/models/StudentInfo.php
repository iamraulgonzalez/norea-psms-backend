<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/database.php';
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
                        s.year_study_id,
                        y.year_study,
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
                      LEFT JOIN tbl_year_study y ON s.year_study_id = y.year_study_id
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
            $this->conn->beginTransaction();
            error_log("Transaction started");

            // Debug log the incoming data
            error_log("Creating student with data: " . json_encode($data));

            // Get next student ID
            $stmt = $this->conn->prepare("SELECT COALESCE(MAX(student_id), 1000) FROM tbl_student_info");
            $stmt->execute();
            $student_id = (int)$stmt->fetchColumn() + 1;
            error_log("Generated student_id: " . $student_id);

            // Check classroom capacity
            $class_id = $data['class_id'] ?? null;
            if ($class_id) {
                $checkCapacityQuery = "SELECT 
                    (SELECT COUNT(*) FROM tbl_student_info 
                     WHERE class_id = :class_id AND isDeleted = 0) as current_count,
                    c.num_students_in_class as max_capacity,
                    c.class_name,
                    g.grade_name,
                    y.year_study
                    FROM tbl_classroom c 
                    INNER JOIN tbl_grade g ON c.grade_id = g.grade_id
                    LEFT JOIN tbl_year_study y ON y.year_study_id = :year_study_id
                    WHERE c.class_id = :class_id";
                
                $stmt = $this->conn->prepare($checkCapacityQuery);
                $stmt->bindValue(':class_id', $class_id);
                $stmt->bindValue(':year_study_id', $data['year_study_id']);
                $stmt->execute();
                $capacityInfo = $stmt->fetch(PDO::FETCH_ASSOC);
                error_log("Capacity check result: " . json_encode($capacityInfo));
                
                if ($capacityInfo && $capacityInfo['current_count'] >= $capacityInfo['max_capacity']) {
                    $this->conn->rollBack();
                    return [
                        'status' => 'error',
                        'message' => "ថ្នាក់ទី " . $capacityInfo['grade_name'] . " " . $capacityInfo['class_name'] . 
                                    " ពេញហើយ (" . $capacityInfo['current_count'] . "/" . $capacityInfo['max_capacity'] . ")"
                    ];
                }
            }

            // Prepare the data
            $student_name = $data['student_name'] ?? null;
            $gender = $data['gender'] ?? null;
            $dob = $data['dob'] ?? null;
            $year_study_id = $data['year_study_id'] ?? null;

            error_log("Validating required fields:");
            error_log("student_name: " . ($student_name ?? 'null'));
            error_log("gender: " . ($gender ?? 'null'));
            error_log("dob: " . ($dob ?? 'null'));
            error_log("class_id: " . ($class_id ?? 'null'));
            error_log("year_study_id: " . ($year_study_id ?? 'null'));

            // Validate required fields
            if (!$student_name || !$gender || !$dob || !$class_id || !$year_study_id) {
                $this->conn->rollBack();
                error_log("Validation failed - missing required fields");
                return [
                    'status' => 'error',
                    'message' => 'សូមបំពេញព័ត៌មានសំខាន់ៗរបស់សិស្ស',
                    'debug' => [
                        'missing_fields' => [
                            'student_name' => !$student_name,
                            'gender' => !$gender,
                            'dob' => !$dob,
                            'class_id' => !$class_id,
                            'year_study_id' => !$year_study_id
                        ]
                    ]
                ];
            }

            $query = "INSERT INTO tbl_student_info (
                student_id, student_name, gender, dob, class_id, year_study_id,
                pob_address, current_address, father_name, father_job, 
                father_phone, mother_name, mother_job, mother_phone, 
                family_status, status
            ) VALUES (
                :student_id, :student_name, :gender, :dob, :class_id, :year_study_id,
                :pob_address, :current_address, :father_name, :father_job,
                :father_phone, :mother_name, :mother_job, :mother_phone,
                :family_status, :status
            )";

            $stmt = $this->conn->prepare($query);
            
            // Debug log for all bound values
            $boundValues = [
                'student_id' => $student_id,
                'student_name' => $student_name,
                'gender' => $gender,
                'dob' => $dob,
                'class_id' => $class_id,
                'year_study_id' => $year_study_id,
                'pob_address' => $data['pob_address'] ?? null,
                'current_address' => $data['current_address'] ?? null,
                'father_name' => $data['father_name'] ?? null,
                'father_job' => $data['father_job'] ?? null,
                'father_phone' => $data['father_phone'] ?? null,
                'mother_name' => $data['mother_name'] ?? null,
                'mother_job' => $data['mother_job'] ?? null,
                'mother_phone' => $data['mother_phone'] ?? null,
                'family_status' => $data['family_status'] ?? null,
                'status' => $data['status'] ?? 'active'
            ];
            error_log("Binding values: " . json_encode($boundValues));
            
            // Bind all parameters
            foreach ($boundValues as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }

            if (!$stmt->execute()) {
                $this->conn->rollBack();
                $error = $stmt->errorInfo();
                error_log("SQL Error during insert: " . json_encode($error));
                error_log("SQL Query: " . $query);
                error_log("Bound values: " . json_encode($boundValues));
                return [
                    'status' => 'error',
                    'message' => 'មានបញ្ហាក្នុងការបង្កើតសិស្ស',
                    'debug' => [
                        'sql_error' => $error,
                        'query' => $query,
                        'values' => $boundValues
                    ]
                ];
            }

            $this->conn->commit();
            error_log("Transaction committed successfully");
            return [
                'status' => 'success',
                'message' => 'សិស្សត្រូវបានបង្កើតដោយជោគជ័យ'
            ];

        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Database Error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return [
                'status' => 'error',
                'message' => 'មានបញ្ហាក្នុងការបង្កើតសិស្ស',
                'debug' => [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]
            ];
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
                        g.grade_name,
                        y.year_study
                        FROM tbl_classroom c 
                        INNER JOIN tbl_grade g ON c.grade_id = g.grade_id
                        LEFT JOIN tbl_year_study y ON c.year_study_id = y.year_study_id
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
                         year_study_id = :year_study_id,
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
            $year_study_id = isset($data['year_study_id']) ? $data['year_study_id'] : null;
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
            $stmt->bindParam(':class_id', $class_id);
            $stmt->bindParam(':year_study_id', $year_study_id);
            $stmt->bindParam(':pob_address', $pob_address);
            $stmt->bindParam(':current_address', $current_address);
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
                s.year_study_id,
                y.year_study,
                c.class_name 
            FROM tbl_student_info s
            LEFT JOIN tbl_classroom c ON s.class_id = c.class_id
            LEFT JOIN tbl_year_study y ON s.year_study_id = y.year_study_id
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

    public function promoteStudent($student_id, $new_class_id) {
        try {
            // First verify if student exists
            $stmt = $this->conn->prepare("SELECT class_id FROM tbl_student_info WHERE student_id = :student_id AND isDeleted = 0");
            $stmt->bindParam(':student_id', $student_id);
            $stmt->execute();
            
            if (!$stmt->fetch()) {
                return [
                    'status' => 'error',
                    'message' => 'សិស្សមិនឃើញទេ'
                ];
            }

            // Verify if new class exists
            $stmt = $this->conn->prepare("SELECT class_id FROM tbl_classroom WHERE class_id = :class_id AND isDeleted = 0");
            $stmt->bindParam(':class_id', $new_class_id);
            $stmt->execute();
            
            if (!$stmt->fetch()) {
                return [
                    'status' => 'error',
                    'message' => 'ថ្នាក់មិនឃើញទេ'
                ];
            }

            // Update student's class
            $query = "UPDATE tbl_student_info 
                     SET class_id = :new_class_id 
                     WHERE student_id = :student_id 
                     AND isDeleted = 0";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':student_id', $student_id);
            $stmt->bindParam(':new_class_id', $new_class_id);
            
            if ($stmt->execute()) {
                return [
                    'status' => 'success',
                    'message' => 'Student promoted successfully'
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Failed to promote student'
                ];
            }
        } catch (PDOException $e) {
            error_log("Error in promoteStudent: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Database error occurred'
            ];
        }
    }

}
