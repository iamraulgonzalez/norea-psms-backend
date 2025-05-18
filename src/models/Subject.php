<?php

require_once __DIR__ . '/../config/database.php';

class Subject{
    private $conn;
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function fetchAll() {
        $query = "SELECT * FROM tbl_subject WHERE isDeleted = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function create($data) {
        $subject_name = isset($data['subject_name']) ? $data['subject_name'] : null;

        if ($subject_name === null) {
            return false;
        }

        $query = "INSERT INTO tbl_subject (subject_name) VALUES (:subject_name)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':subject_name', $subject_name);

        return $stmt->execute();
    }
   

    public function update($id, $data) {
        $query = "UPDATE tbl_subject SET subject_name = :subject_name WHERE subject_code = :subject_code AND isDeleted = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':subject_code', $id);
        $stmt->bindParam(':subject_name', $data['subject_name']);
        return $stmt->execute();
    }
    
    public function delete($id) {
        try {
            //check if the subject is used in any class
            $query = "SELECT * FROM tbl_assign_subject_grade WHERE subject_code = :subject_code";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':subject_code', $id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            return [
                'status' => 'error',
                'message' => 'មុខវិជ្ជានេះមាននៅក្នុងថ្នាក់ហើយ មិនអាចលុបបានទេ!'
            ];
        }
        $query = "UPDATE tbl_subject SET isDeleted = 1 WHERE subject_code = :subject_code";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':subject_code', $id);
        $result = $stmt->execute();
        if ($result) {
            return [
                'status' => 'success',
                'message' => 'លុបមុខវិជ្ជានេះបានជោគជ័យ'
            ];
        }
        return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error in delete: " . $e->getMessage());
            throw $e;
        }
    }

    
    public function fetchById($id) {
        $query = "SELECT * FROM tbl_subject WHERE subject_code = :subject_code AND isDeleted = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':subject_code', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getCount() {
        try {
            $query = "SELECT COUNT(*) as count 
                     FROM tbl_subject 
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