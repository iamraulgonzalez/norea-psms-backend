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
                      LEFT JOIN (
                          SELECT st.student_id, st.class_id, MAX(st.study_id) as latest_study
                          FROM tbl_study st
                          WHERE st.isDeleted = 0 AND st.status = 'active'
                          GROUP BY st.student_id
                      ) latest ON s.student_id = latest.student_id
                      LEFT JOIN tbl_classroom c ON latest.class_id = c.class_id
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

            // Get next student ID
            $stmt = $this->conn->prepare("SELECT COALESCE(MAX(student_id), 1000) FROM tbl_student_info");
            $stmt->execute();
            $student_id = (int)$stmt->fetchColumn() + 1;

            // Prepare the data
            $student_name = $data['student_name'] ?? null;
            $gender = $data['gender'] ?? null;
            $dob = $data['dob'] ?? null;

            // Validate required fields
            if (!$student_name || !$gender || !$dob) {
                $this->conn->rollBack();
                return [
                    'status' => 'error',
                    'message' => 'សូមបំពេញព័ត៌មានសំខាន់ៗរបស់សិស្ស',
                ];
            }

            $query = "INSERT INTO tbl_student_info (
                student_id, student_name, gender, dob,
                pob_address, current_address, father_name, father_job, 
                father_phone, mother_name, mother_job, mother_phone, 
                family_status, status
            ) VALUES (
                :student_id, :student_name, :gender, :dob,
                :pob_address, :current_address, :father_name, :father_job,
                :father_phone, :mother_name, :mother_job, :mother_phone,
                :family_status, :status
            )";

            $stmt = $this->conn->prepare($query);
            
            // Bind parameters
            $boundValues = [
                'student_id' => $student_id,
                'student_name' => $student_name,
                'gender' => $gender,
                'dob' => $dob,
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
            
            // Bind all parameters
            foreach ($boundValues as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }

            if (!$stmt->execute()) {
                $this->conn->rollBack();
                $error = $stmt->errorInfo();
                return [
                    'status' => 'error',
                    'message' => 'មានបញ្ហាក្នុងការបង្កើតសិស្ស',
                    'debug' => ['sql_error' => $error],
                ];
            }

            $this->conn->commit();
            return [
                'status' => 'success',
                'message' => 'សិស្សត្រូវបានបង្កើតដោយជោគជ័យ'
            ];

        } catch (PDOException $e) {
            $this->conn->rollBack();
            return [
                'status' => 'error',
                'message' => 'មានបញ្ហាក្នុងការបង្កើតសិស្ស',
            ];
        }
    }

    public function getUnenrolledStudents() {
        try {
            $query = "SELECT s.* 
                     FROM tbl_student_info s 
                     WHERE NOT EXISTS (
                         SELECT 1 
                         FROM tbl_study st 
                         WHERE st.student_id = s.student_id 
                         AND st.status = 'active'
                     )
                     AND s.status = 'active'
                     ORDER BY s.student_name ASC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return [
                'status' => 'success',
                'data' => $students
            ];
            
        } catch (PDOException $e) {
            error_log("Error in getUnenrolledStudents: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'មានបញ្ហាក្នុងការទទួលបានសិស្សមិនបានបង្កើតប្រើប្រាស់',
                'debug' => $e->getMessage()
            ];
        }
    }
    
    public function update($id, $data) {
        try {
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
            $student_name = $data['student_name'] ?? null;
            $gender = $data['gender'] ?? null;
            $dob = $data['dob'] ?? null;
            $pob_address = $data['pob_address'] ?? null;
            $current_address = $data['current_address'] ?? null;
            $father_name = $data['father_name'] ?? null;
            $father_job = $data['father_job'] ?? null;
            $father_phone = $data['father_phone'] ?? null;
            $mother_name = $data['mother_name'] ?? null;
            $mother_job = $data['mother_job'] ?? null;
            $mother_phone = $data['mother_phone'] ?? null;
            $family_status = $data['family_status'] ?? null;
            $status = $data['status'] ?? null;

            // Bind parameters
            $stmt->bindParam(':student_id', $id);
            $stmt->bindParam(':student_name', $student_name);
            $stmt->bindParam(':gender', $gender);
            $stmt->bindParam(':dob', $dob);
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
                c.class_name
            FROM tbl_student_info s
            JOIN tbl_study st ON s.student_id = st.student_id
            JOIN tbl_classroom c ON st.class_id = c.class_id
            WHERE st.class_id = :class_id
            AND st.status = 'active'
            AND s.isDeleted = 0
            AND st.isDeleted = 0";
            
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
    public function fetchByMonth($month_id, $year_study_id) {
        try {
            $sql = "SELECT DISTINCT s.student_id, s.student_name, st.enrollment_date, st.status,
                    c.class_name, g.grade_name
                    FROM tbl_student_info s
                    INNER JOIN tbl_study st ON s.student_id = st.student_id
                    LEFT JOIN tbl_classroom c ON st.class_id = c.class_id
                    LEFT JOIN tbl_grade g ON c.grade_id = g.grade_id
                    WHERE st.month_id = ?
                    AND st.year_study_id = ?
                    AND s.isDeleted = 0
                    AND st.isDeleted = 0
                    AND st.status = 'active'
                    ORDER BY st.enrollment_date DESC";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(1, $month, PDO::PARAM_INT);
            $stmt->bindParam(2, $year, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error in fetchByMonth: " . $e->getMessage());
            throw $e;
        }
    }

    public function getEnrollmentCountsByMonth($year_study_id) {
        try {
            $sql = "SELECT 
                    tm.monthly_id,
                    tm.month_name,
                    COUNT(DISTINCT s.student_id) as student_count
                FROM tbl_monthly tm
                LEFT JOIN (
                    SELECT student_id, MONTH(enrollment_date) as month_num
                    FROM tbl_study 
                    WHERE year_study_id = ?
                    AND isDeleted = 0
                    AND status = 'active'
                ) st ON tm.monthly_id = st.month_num
                LEFT JOIN tbl_student_info s ON st.student_id = s.student_id
                    AND s.isDeleted = 0
                GROUP BY tm.monthly_id, tm.month_name
                ORDER BY tm.monthly_id";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(1, $year_study_id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error in getEnrollmentCountsByMonth: " . $e->getMessage());
            throw $e;
        }
    }
}
