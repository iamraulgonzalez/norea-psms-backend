<?php

require_once __DIR__ . '/../config/database.php';

class SubSubject{
    private $conn;
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function fetchAll() {
        $query = "SELECT * FROM tbl_sub_subject";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function create($data) {
        $subject_code = isset($data['subject_name']) ? $data['subject_name'] : null;
        $sub_subject_name = isset($data['sub_subject_name']) ? $data['sub_subject_name'] : null;

        if ($subject_code && $sub_subject_name === null) {
            return false;
        }

        $query = "INSERT INTO tbl_sub_subject (subject_code,sub_subject_name) VALUES (:subject_code, :sub_subject_name)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':subject_code', $subject_code);
        $stmt->bindParam(':sub_subject_name', $sub_subject_name);

        // Execute the query
        return $stmt->execute();
    }
   
    public function update($id, $data) {
        $query = "UPDATE tbl_sub_subject SET subject_code,sub_subject_name = :subject_code, :sub_subject_name WHERE sub_code = :sub_code";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':subject_code', $id);
        $stmt->bindParam(':subject_name', $data['subject_name']);
        return $stmt->execute();
    }
    
    public function delete($id) {
        $query = "DELETE FROM tbl_sub_subject WHERE sub_code = :sub_code";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':sub_code', $id);
        return $stmt->execute();
    }
    
    public function fetchById($id) {
        $query = "SELECT * FROM tbl_sub_subject WHERE sub_code = :sub_code";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':sub_code', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}