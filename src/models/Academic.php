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
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        var_dump($data);
        $year_study = isset($data['year_study']) ? $data['year_study'] : null;
    
        if ($year_study === null) {
            return false;
        }
        $query = "INSERT INTO tbl_year_study (year_study) VALUES (:year_study)";
        $stmt = $this->conn->prepare($query);
    
        $stmt->bindParam(':year_study', $year_study);
    
        return $stmt->execute();
    }
   

    public function update($id, $data) {
        $query = "UPDATE tbl_year_study SET year_study = :year_study WHERE year_study_id = :year_study_id AND isDeleted = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':year_study_id', $id);
        $stmt->bindParam(':year_study', $data['year_study']);
        return $stmt->execute();
    }
    
    public function delete($id) {
        $query = "UPDATE tbl_year_study SET isDeleted = 1 WHERE year_study_id = :year_study_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':year_study_id', $id);
        return $stmt->execute();
    }
    
    public function fetchById($id) {
        $query = "SELECT * FROM tbl_year_study WHERE year_study_id = :year_study_id AND isDeleted = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':year_study_id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}