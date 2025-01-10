<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/StudentInfo.php';
require_once __DIR__ . '/../utils/response.php';

class StudentInfoController extends BaseController {
    private $studentModel;

    public function __construct() {
        $this->studentModel = new Student();
    }

    public function getAllStudents() {
        try {
            error_log("Fetching all students...");
            $students = $this->studentModel->fetchAll();
            error_log("Found " . count($students) . " students");
            
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'success',
                'data' => $students
            ]);
        } catch (Exception $e) {
            error_log("Error in getAllStudents: " . $e->getMessage());
            $this->sendError($e->getMessage());
        }
    }

    public function getStudentById($id) {
        if (!$id) {
            $this->sendError('Student ID not provided');
            return;
        }
        try {
            $student = $this->studentModel->fetchById($id);
            echo json_encode([
                'status' => 'success',
                'data' => $student
            ]);
        } catch (Exception $e) {
            $this->sendError($e->getMessage());
        }
    }

    public function addStudent($data) {
        // Implementation
    }

    public function updateStudent($id, $data) {
        // Implementation
    }

    public function deleteStudent($id) {
        // Implementation
    }
}