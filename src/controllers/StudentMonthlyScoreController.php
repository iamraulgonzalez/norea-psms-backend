<?php
require_once __DIR__ . '/../models/StudentMonthlyScore.php';
require_once __DIR__ . '/../utils/response.php';

class StudentMonthlyScoreController {
    private $monthlyScore;

    public function __construct() {
        $this->monthlyScore = new StudentMonthlyScore();
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

    public function addMonthlyScore($data) {
        try {
            // Validate if the classroom_subject exists and check its type
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

    public function updateMonthlyScore($id, $data) {
        try {
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

    public function getStudentMonthlyScores($student_id) {
        try {
            $monthly_id = $_GET['monthly_id'] ?? null;
            $class_id = $_GET['class_id'] ?? null;

            if (!$monthly_id || !$class_id) {
                echo jsonResponse(400, [
                    'status' => 'error',
                    'message' => 'Missing required parameters'
                ]);
                return;
            }

            $result = $this->monthlyScore->getStudentMonthlyScores(
                $student_id,
                $monthly_id,
                $class_id
            );

            echo jsonResponse(
                $result['status'] === 'success' ? 200 : 404,
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

    public function getClassMonthlyScores($class_id) {
        try {
            $monthly_id = $_GET['monthly_id'] ?? null;

            if (!$monthly_id) {
                echo jsonResponse(400, [
                    'status' => 'error',
                    'message' => 'Missing required parameters'
                ]);
                return;
            }

            $result = $this->monthlyScore->getClassMonthlyScores(
                $class_id,
                $monthly_id
            );

            echo jsonResponse(
                $result['status'] === 'success' ? 200 : 404,
                $result
            );
        } catch (Exception $e) {
            error_log("Error in getClassMonthlyScores: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => 'Server error occurred'
            ]);
        }
    }

    public function getAllScoresGrouped() {
        try {
            $result = $this->monthlyScore->getAllScoresGrouped();
            
            echo jsonResponse(
                $result['status'] === 'success' ? 200 : 404,
                $result
            );
        } catch (Exception $e) {
            error_log("Error in getAllScoresGrouped: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => 'Server error occurred'
            ]);
        }
    }

    public function getStudentScoresOrdered($student_id) {
        try {
            $result = $this->monthlyScore->getStudentScoresOrdered($student_id);
            
            echo jsonResponse(
                $result['status'] === 'success' ? 200 : 404,
                $result
            );
        } catch (Exception $e) {
            error_log("Error in getStudentScoresOrdered: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => 'Server error occurred'
            ]);
        }
    }
}