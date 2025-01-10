<?php
require_once __DIR__ . '/../config/database.php';

class YearStudy {
    private $conn;
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function fetchAll() {
        $query = "SELECT * FROM tbl_year_study";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $year_study = isset($data['year_study']) ? $data['year_study'] : null;

        if ($year_study === null) {
            return false;
        }

        // Prepare the SQL query to insert a new classroom
        $query = "INSERT INTO tbl_year_study (year_study) VALUES (:year_study)";
        $stmt = $this->conn->prepare($query);

        // Bind the class_name parameter
        $stmt->bindParam(':year_study', $year_study);

        // Execute the query
        return $stmt->execute();
    }
   

    public function update($id, $data) {
        $query = "UPDATE tbl_year_study SET year_study = :year_study WHERE year_study_id = :year_study_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':year_study_id', $id);
        $stmt->bindParam(':year_study', $data['year_study']);
        return $stmt->execute();
    }
    
    public function delete($id) {
        $query = "DELETE FROM tbl_year_study WHERE year_study_id = :year_study_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':year_study_id', $id);
        return $stmt->execute();
    }
    
    public function fetchById($id) {
        $query = "SELECT * FROM tbl_year_study WHERE year_study_id = :year_study_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':year_study_id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}