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
            // Clear any previous output
            if (ob_get_level()) ob_end_clean();
            
            // Set proper headers
            header('Content-Type: application/json; charset=utf-8');
            header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");
            
            $students = $this->studentModel->fetchAll();
            
            // Send single JSON response
            $response = [
                'status' => 'success',
                'data' => $students
            ];
            
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit();
            
        } catch (Exception $e) {
            error_log("Error getting students: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to get students'
            ]);
            exit();
        }
    }
    public function getStudentById($id) {
        if (!$id) {
            $this->sendError('Student ID not provided', 400);
            return;
        }
        try {
            $student = $this->studentModel->fetchById($id);
            echo json_encode([
                'status' => 'success',
                'data' => $student
            ]);
        } catch (Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    public function addStudent($data) {
        try {
            // Clean any existing output
            if (ob_get_level()) ob_end_clean();
            
            error_log("Received data in controller: " . json_encode($data));
            
            // Validate required fields
            if (!isset($data['student_name']) || empty($data['student_name'])) {
                throw new Exception('Student name is required');
            }

            // Attempt to create the student
            $result = $this->studentModel->create($data);
            error_log("Model create result: " . json_encode($result));
            
            if (isset($result['status'])) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode($result, JSON_UNESCAPED_UNICODE);
                exit();
            } else {
                throw new Exception('Invalid response from model');
            }
        } catch (Exception $e) {
            error_log("Error in addStudent: " . $e->getMessage());
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
            exit();
        }
    }

    public function updateStudent($id) {
        try {
            $data = json_decode(file_get_contents("php://input"), true);
            $student = new Student();
            
            $result = $student->update($id, $data);
            
            // Set proper headers for UTF-8 encoding
            header('Content-Type: application/json; charset=utf-8');
            
            if (isset($result['status'])) {
                http_response_code($result['status'] === 'error' ? 400 : 200);
                echo json_encode($result, JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(200);
                echo json_encode([
                    'status' => 'success',
                    'message' => 'សិស្សត្រូវបានធ្វើបច្ចុប្បន្នភាពដោយជោគជ័យ'
                ], JSON_UNESCAPED_UNICODE);
            }
            
        } catch (Exception $e) {
            error_log("Error in updateStudent: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'មានបញ្ហាក្នុងការធ្វើបច្ចុប្បន្នភាពសិស្ស'
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    public function getUnenrolledStudents() {
        try {
            $students = $this->studentModel->getUnenrolledStudents();
            echo json_encode($students, JSON_UNESCAPED_UNICODE);
            exit();
        } catch (Exception $e) {
            error_log("Error in getUnenrolledStudents: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to get unenrolled students'
            ]);
        }
    }

    public function deleteStudent($id) {
        try {
            // Clear any previous output
            if (ob_get_level()) ob_end_clean();
            
            error_log("Attempting to delete student ID: " . $id);
            
            if (!$id) {
                throw new Exception('Student ID is required');
            }
            
            $result = $this->studentModel->delete($id);

            // Set proper headers
            header('Content-Type: application/json; charset=utf-8');
            http_response_code($result['status'] === 'error' ? 400 : 200);
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
            exit();
            
        } catch (Exception $e) {
            error_log("Error in deleteStudent: " . $e->getMessage());
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'មានបញ្ហាក្នុងការលុបសិស្ស'
            ], JSON_UNESCAPED_UNICODE);
            exit();
        }
    }
    public function getStudentsByClassId($class_id) {
        try {
            if (ob_get_level()) ob_end_clean();
            
            header('Content-Type: application/json; charset=utf-8');
            
            $students = $this->studentModel->fetchByClassId($class_id);
            
            echo json_encode($students, JSON_UNESCAPED_UNICODE);
            exit();
            
        } catch (Exception $e) {
            error_log("Error getting students by class: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to get students'
            ]);
            exit();
        }
    }

    public function getStudentCount() {
        try {
            $count = $this->studentModel->getCount();
            jsonResponse(200, [
                'status' => 'success',
                'count' => (int)$count
            ]);
        } catch (Exception $e) {
            errorResponse(500, 'Failed to get student count');
        }
    }
    
    public function getStudentsByMonth() {
        try {
            if (ob_get_level()) ob_end_clean();
            
            header('Content-Type: application/json; charset=utf-8');
            
            $month = isset($_GET['month']) ? $_GET['month'] : date('m');
            $year = isset($_GET['year']) ? $_GET['year'] : date('Y');
            
            $students = $this->studentModel->fetchByMonth($month, $year);
            
            echo json_encode([
                'status' => 'success',
                'data' => $students
            ], JSON_UNESCAPED_UNICODE);
            exit();
            
        } catch (Exception $e) {
            error_log("Error getting students by month: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to get students by month'
            ]);
            exit();
        }
    }

    public function getEnrollmentCountsByMonth($year_study_id) {
        try {
            if (ob_get_level()) ob_end_clean();
            
            header('Content-Type: application/json; charset=utf-8');
            
            $counts = $this->studentModel->getEnrollmentCountsByMonth($year_study_id);
            
            echo json_encode([
                'status' => 'success',
                'data' => $counts
            ], JSON_UNESCAPED_UNICODE);
            exit();
            
        } catch (Exception $e) {
            error_log("Error getting enrollment counts by month: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to get enrollment counts'
            ]);
            exit();
        }
    }

}