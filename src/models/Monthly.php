<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../utils/response.php';

class Monthly {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();  // Get the PDO connection
    }

    public function fetchAll() {
        $query = "SELECT * FROM tbl_monthly WHERE isDeleted = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $query = "INSERT INTO tbl_monthly (month_name) VALUES (:month_name)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':month_name', $data['month_name']);
        $stmt->execute();
        return [
            'status' => 'success',
            'message' => 'បង្កើតខែបានជោគជ័យ'
        ];
    }

    public function update($id, $data) {
        try {
            //check if the monthly is used in any class
            $query = "SELECT c.class_name 
                     FROM classroom_subject_monthly_score csms
                     JOIN tbl_classroom c ON csms.class_id = c.class_id
                     WHERE csms.monthly_id = :monthly_id
                     AND csms.isDeleted = 0
                     LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':monthly_id', $id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if($result){
                return [
                    'status' => 'error',
                    'message' => 'ខែនេះត្រូវបានដាក់ពិន្ទុនៅក្នុងថ្នាក់មិនអាចកែប្រែបានទេ!'
                ];
            }
            $query = "UPDATE tbl_monthly SET month_name = :month_name WHERE monthly_id = :monthly_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':monthly_id', $id);
            $stmt->bindParam(':month_name', $data['month_name']);
            
            if($stmt->execute()) {
                return [
                    'status' => 'success',
                    'message' => 'កែប្រែខែបានជោគជ័យ'
                ];
            }

            return [
                'status' => 'error',
                'message' => 'មានបញ្ហាក្នុងការកែប្រែខែនេះ'
            ];

        } catch (PDOException $e) {
            error_log("Error in update: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'មានបញ្ហាក្នុងការកែប្រែខែនេះ'
            ];
        }
    }

    public function delete($id) {
        try {
            //check if the monthly is used in any class
            $query = "SELECT c.class_name 
                     FROM classroom_subject_monthly_score csms
                     JOIN tbl_classroom c ON csms.class_id = c.class_id
                     WHERE csms.monthly_id = :monthly_id
                     AND csms.isDeleted = 0
                     LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':monthly_id', $id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($result){
                return [
                    'status' => 'error',
                    'message' => 'ខែនេះត្រូវបានដាក់ពិន្ទុនៅក្នុងថ្នាក់មិនអាចលុបបានទេ!'
                ];
            }

            // If not used, proceed with deletion
            $query = "UPDATE tbl_monthly SET isDeleted = 1 WHERE monthly_id = :monthly_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':monthly_id', $id);
            
            if($stmt->execute()) {
                return [
                    'status' => 'success',
                    'message' => 'លុបខែនេះបានជោគជ័យ'
                ];
            }

            return [
                'status' => 'error',
                'message' => 'មានបញ្ហាក្នុងការលុបខែនេះ'
            ];

        } catch (PDOException $e) {
            error_log("Error in delete: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'មានបញ្ហាក្នុងការលុបខែនេះ'
            ];
        }
    }

    public function fetchById($id) {
        $query = "SELECT * FROM tbl_monthly WHERE monthly_id = :monthly_id AND isDeleted = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':monthly_id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

