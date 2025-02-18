<?php
require_once __DIR__ . '/../models/MonthlyScore.php';
require_once __DIR__ . '/../utils/response.php';

class MonthlyScoreController {
    private $monthlyScore;

    public function __construct() {
        $this->monthlyScore = new MonthlyScore();
    }

    public function getAllMonthlyScores() {
        try {
            if (ob_get_level()) ob_end_clean();
            
            header('Content-Type: application/json; charset=utf-8');
            
            $result = $this->monthlyScore->fetchAll();
            
            echo jsonResponse(
                $result['status'] === 'success' ? 200 : 500,
                $result
            );
            
        } catch (Exception $e) {
            error_log("Error in getAllMonthlyScores: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => 'Server error occurred'
            ]);
        }
    }

    public function getStudentMonthlyScores($student_id) {
        try {
            if (ob_get_level()) ob_end_clean();
            
            $monthly_id = $_GET['monthly_id'] ?? null;
            $class_id = $_GET['class_id'] ?? null;
            $year_study_id = $_GET['year_study_id'] ?? null;

            // Validate input parameters
            if (!$monthly_id || !$class_id || !$year_study_id || !$student_id) {
                echo jsonResponse(400, [
                    'status' => 'error',
                    'message' => 'Missing required parameters'
                ]);
                return;
            }

            // Validate parameter types
            if (!is_numeric($monthly_id) || !is_numeric($class_id) || 
                !is_numeric($year_study_id) || !is_numeric($student_id)) {
                echo jsonResponse(400, [
                    'status' => 'error',
                    'message' => 'Invalid parameter types'
                ]);
                return;
            }

            $result = $this->monthlyScore->getStudentMonthlyScores(
                (int)$student_id,
                (int)$monthly_id,
                (int)$class_id,
                (int)$year_study_id
            );

            echo jsonResponse(
                $result['status'] === 'success' ? 200 : 500,
                $result
            );
            
        } catch (Exception $e) {
            error_log("Error in getStudentMonthlyScores: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => 'Server error occurred'
            ]);
        }
    }

    public function addMonthlyScore() {
        try {
            $data = json_decode(file_get_contents("php://input"), true);
            
            if (!$data) {
                throw new Exception('Invalid input data');
            }

            $result = $this->monthlyScore->create($data);
            
            echo jsonResponse(
                $result['status'] === 'success' ? 201 : 400,
                $result
            );
        } catch (Exception $e) {
            error_log("Error in addMonthlyScore: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => 'Server error occurred'
            ]);
        }
    }

    public function updateMonthlyScore($id) {
        try {
            $data = json_decode(file_get_contents("php://input"), true);
            
            if (!$data) {
                throw new Exception('Invalid input data');
            }

            $result = $this->monthlyScore->update($id, $data);
            
            echo jsonResponse(
                $result['status'] === 'success' ? 200 : 400,
                $result
            );
        } catch (Exception $e) {
            error_log("Error in updateMonthlyScore: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => 'Server error occurred'
            ]);
        }
    }

    public function deleteMonthlyScore($id) {
        $result = $this->monthlyScore->delete($id);
        echo jsonResponse(
            $result['status'] === 'success' ? 200 : 400,
            $result
        );
    }

    public function getMonthlyScoreById($id) {
        $result = $this->monthlyScore->fetchById($id);
        echo jsonResponse(
            $result['status'] === 'success' ? 200 : 404,
            $result
        );
    }
}