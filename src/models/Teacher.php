<?php
require_once __DIR__ . '/../config/database.php';

class Teacher {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function fetchAll() {
        $query = "SELECT * FROM tbl_teacher_info";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $query = "INSERT INTO tbl_teacher_info (name, department, email, phone) VALUES (:name, :department, :email, :phone)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':department', $data['department']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':phone', $data['phone']);
        return $stmt->execute();
    }

    public function update($id, $data) {
        $query = "UPDATE tbl_teacher_info SET name = :name, department = :department, email = :email, phone = :phone WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':department', $data['department']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':phone', $data['phone']);
        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM tbl_teacher_info WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function fetchById($id) {
        $query = "SELECT * FROM tbl_teacher_info WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}