<?php
require_once __DIR__ . '/../models/StudentSemesterScore.php';

class StudentSemesterScoreController {
    private $model;
    
    public function __construct() {
        $this->model = new StudentSemesterScore();
    }

    public function getAllStudentSemesterScores() {
        $result = $this->model->fetchAllSemesterBasedScores();
        echo jsonResponse(200, $result);
    }
    
    public function addStudentSemesterScore($req, $res) {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
        if ($data === null) {
            $res->status(400)->json([
                'status' => 'error',
                'message' => 'Invalid JSON input'
            ]);
            return;
        }

        $requiredFields = ['student_id', 'semester_exam_subject_id', 'score'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                $res->status(400)->json([
                    'status' => 'error',
                    'message' => "Missing required field: {$field}"
                ]);
                return;
            }
        }

        if (!is_numeric($data['student_id']) || !is_numeric($data['semester_exam_subject_id']) || !is_numeric($data['score'])) {
            $res->status(400)->json([
                'status' => 'error',
                'message' => 'Invalid parameter types: student_id, semester_exam_subject_id, and score must be numeric'
            ]);
            return;
        }

        if ($data['score'] < 0 || $data['score'] > 10) {
            $res->status(400)->json([
                'status' => 'error',
                'message' => 'Score must be between 0 and 10'
            ]);
            return;
        }
        
        $result = $this->model->create($data);
        
        if ($result['status'] === 'success') {
            $res->status(201)->json($result);
        } else {
                $res->status(400)->json($result);
            }
        } catch (Exception $e) {
            error_log("Error in addStudentSemesterScore: " . $e->getMessage());
            $res->status(500)->json([
                'status' => 'error',
                'message' => 'Server error occurred'
            ]);
        }
    }

    public function updateStudentSemesterScore($id, $data) {
        try {
            $result = $this->model->update($id, $data);
            
            echo jsonResponse(
                $result['status'] === 'success' ? 200 : 400,
                $result
            );
        } catch (Exception $e) {
            error_log("Error in updateStudentSemesterScore: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => 'Server error occurred'
            ]);
        }
    }
    
    public function deleteStudentSemesterScore($req, $res) {
        $id = $req->params['id'];
        $result = $this->model->delete($id);
        
        if ($result['status'] === 'success') {
            $res->status(200)->json($result);
        } else {
            $res->status(400)->json($result);
        }
    }
    
    public function getStudentSemesterScores($req, $res) {
        // Try to get parameters from both query string and request body
        $json_data = json_decode(file_get_contents('php://input'), true);
        
        $student_id = isset($_GET['student_id']) ? $_GET['student_id'] : (isset($json_data['student_id']) ? $json_data['student_id'] : null);
        $semester_id = isset($_GET['semester_id']) ? $_GET['semester_id'] : (isset($json_data['semester_id']) ? $json_data['semester_id'] : null);
        $class_id = isset($_GET['class_id']) ? $_GET['class_id'] : (isset($json_data['class_id']) ? $json_data['class_id'] : null);

        // Validate required parameters
        if (!$student_id || !$semester_id || !$class_id) {
            $res->status(400)->json([
                'status' => 'error',
                'message' => 'Missing required parameters: student_id, semester_id, class_id'
            ]);
            return;
        }

        // Validate data types
        if (!is_numeric($student_id) || !is_numeric($semester_id) || !is_numeric($class_id)) {
            $res->status(400)->json([
                'status' => 'error',
                'message' => 'Invalid parameter types: student_id, semester_id, and class_id must be numeric'
            ]);
            return;
        }
        
        $result = $this->model->getStudentSemesterScores(
            (int)$student_id,
            (int)$semester_id,
            (int)$class_id
        );
        
        if ($result['status'] === 'success') {
            $res->status(200)->json($result);
        } else {
            $res->status(400)->json($result);
        }
    }
    public function CalculateFinalSemesterAverage($req, $res) {
        // Decode body
        $json_data = json_decode(file_get_contents('php://input'), true);
    
        // Check for parse failure
        if ($json_data === null) {
            return $res->status(400)->json([
                'status' => 'error',
                'message' => 'Invalid JSON input'
            ]);
        }
    
        // Get params
        $student_id = $json_data['student_id'] ?? null;
        $semester_id = $json_data['semester_id'] ?? null;
        $class_id = $json_data['class_id'] ?? null;
        $monthly_ids = $json_data['monthly_ids'] ?? null;
    
        // Validate
        if (!$student_id || !$semester_id || !$class_id || !$monthly_ids) {
            return $res->status(400)->json([
                'status' => 'error',
                'message' => 'Missing required parameters: student_id(s), semester_id, class_id, monthly_ids'
            ]);
        }
    
        // Make sure student_id is an array
        $student_ids = is_array($student_id) ? $student_id : [$student_id];
    
        $results = [];
    
        foreach ($student_ids as $sid) {
            $result = $this->model->calculateFinalSemesterAverage(
                (int)$sid,
                (int)$semester_id,
                (int)$class_id,
                implode(',', array_map('intval', $monthly_ids))
            );
            
            // Only add successful results
            if ($result['status'] === 'success' && isset($result['data'])) {
                $results[] = $result;
            }
        }
    
        // If we have results, return them
        if (!empty($results)) {
            // Ensure clean JSON response
            header('Content-Type: application/json');
            echo json_encode($results);
            exit;
        }
        
        // If no results, return an error
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => 'No results found'
        ]);
        exit;
    }
        
    public function getStudentScoresByClassAndSemester() {
        try {
            $class_id = $_GET['class_id'] ?? null;
            $semester_id = $_GET['semester_id'] ?? null;

            if (!$class_id || !$semester_id) {
                echo jsonResponse(400, [
                    'status' => 'error',
                    'message' => 'Missing required parameters: class_id and semester_id'
                ]);
                return;
            }

            $result = $this->model->getStudentScoresByClassAndSemester($class_id, $semester_id);
            
            echo jsonResponse(
                $result['status'] === 'success' ? 200 : 404,
                $result
            );
        } catch (Exception $e) {
            error_log("Error in getStudentScoresByClassAndSemester: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => 'Server error occurred'
            ]);
        }
    }

    public function calculateYearlyAverage() {
        try {
            $data = json_decode(file_get_contents("php://input"), true);
            
            if (!isset($data['student_id']) || !isset($data['class_id'])) {
                http_response_code(400);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Missing required parameters: student_id and class_id'
                ]);
                return;
            }
            
            $student_id = $data['student_id'];
            $class_id = $data['class_id'];
            
            // Log the input parameters
            error_log("Calculating yearly average for student_id: $student_id, class_id: $class_id");
            
            $result = $this->model->calculateYearlyAverage($student_id, $class_id);
            
            if ($result['status'] === 'success') {
                http_response_code(200);
                echo json_encode($result);
            } else {
                http_response_code(404);
                echo json_encode([
                    'status' => 'error',
                    'message' => $result['message'],
                    'debug_info' => [
                        'student_id' => $student_id,
                        'class_id' => $class_id
                    ]
                ]);
            }
            
        } catch (Exception $e) {
            error_log("Error in calculateYearlyAverage controller: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'Internal server error',
                'debug_info' => [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]
            ]);
        }
    }

    public function getStudentSemesterScoreReport($class_id, $semester_id) {
        try {
            $result = $this->model->getStudentSemesterScoreReport($class_id, $semester_id);
            echo jsonResponse(
                $result['status'] === 'success' ? 200 : 404,
                $result
            );
        } catch (Exception $e) {
            error_log("Error in getStudentSemesterScoreReport: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => 'Server error occurred'
            ]);
        }
    }

    public function getYearlyAverage($class_id) {
        try {
            $result = $this->model->getYearlyAverage($class_id);
            echo jsonResponse(
                $result['status'] === 'success' ? 200 : 404,
                $result
            );
        } catch (Exception $e) {
            error_log("Error in getYearlyAverage: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => 'Server error occurred'
            ]);
        }
    }

    public function getYearlyAverageByGrade($grade_id) {
        try {
            $result = $this->model->getYearlyAverageByGrade($grade_id);
            echo jsonResponse(
                $result['status'] === 'success' ? 200 : 404,
                $result
            );
        } catch (Exception $e) {
            error_log("Error in getYearlyAverageByGrade: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => 'Server error occurred'
            ]);
        }
    }
}