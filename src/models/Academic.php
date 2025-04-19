<?php
require_once __DIR__ . '/../config/database.php';

class Academic {   
    private $conn;
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function fetchAll() {
        $query = "SELECT * FROM tbl_year_study WHERE isDeleted = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return [
            'status' => 'success',
            'data' => $result
        ];
    }

    public function create($data) {
        $year_study = isset($data['year_study']) ? $data['year_study'] : null;

        //check exiting year_study
        $checkExiting = "SELECT * FROM tbl_year_study WHERE year_study = :year_study";
        $stmt = $this->conn->prepare($checkExiting);
        $stmt->bindParam(':year_study', $year_study);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            return [
                'status' => 'error',
                'message' => 'Year study already exists'
            ];
        }
    
        if ($year_study === null) {
            return [
                'status' => 'error',
                'message' => 'Year study is required'
            ];
        }
        $query = "INSERT INTO tbl_year_study (year_study) VALUES (:year_study)";
        $stmt = $this->conn->prepare($query);
    
        $stmt->bindParam(':year_study', $year_study);
    
        if ($stmt->execute()) {
            return [
                'status' => 'success',
                'message' => 'Academic year created successfully',
                'data' => [
                    'year_study_id' => $this->conn->lastInsertId(),
                    'year_study' => $year_study
                ]
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Failed to create academic year'
            ];
        }
    }
   

    public function update($id, $data) {
        if (!isset($data['year_study']) || empty($data['year_study'])) {
            return [
                'status' => 'error',
                'message' => 'Year study is required'
            ];
        }
        
        try {
            // Check if the new year_study already exists (excluding the current record)
            $checkStmt = $this->conn->prepare("SELECT COUNT(*) FROM tbl_year_study WHERE year_study = ? AND year_study_id != ?");
            $checkStmt->execute([$data['year_study'], $id]);
            $count = $checkStmt->fetchColumn();
            
            if ($count > 0) {
                return [
                    'status' => 'error',
                    'message' => 'Year study already exists'
                ];
            }
            
            $stmt = $this->conn->prepare("UPDATE tbl_year_study SET year_study = ? WHERE year_study_id = ?");
            $stmt->execute([$data['year_study'], $id]);
            
            return [
                'status' => 'success',
                'message' => 'Academic year updated successfully'
            ];
        } catch (PDOException $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
    
    public function delete($id) {
        $query = "UPDATE tbl_year_study SET isDeleted = 1 WHERE year_study_id = :year_study_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':year_study_id', $id);
        
        if ($stmt->execute()) {
            return [
                'status' => 'success',
                'message' => 'Academic year deleted successfully'
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Failed to delete academic year'
            ];
        }
    }
    
    public function fetchById($id) {
        $query = "SELECT * FROM tbl_year_study WHERE year_study_id = :year_study_id AND isDeleted = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':year_study_id', $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            return [
                'status' => 'success',
                'data' => $result
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Academic year not found'
            ];
        }
    }
}