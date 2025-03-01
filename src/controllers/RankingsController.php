<?php

require_once __DIR__ . '/../models/Rankings.php';
require_once __DIR__ . '/../utils/response.php';

class RankingsController {
    private $rankings;

    public function __construct() {
        $this->rankings = new Rankings();
    }

    public function getMonthlyRankings() {
        $class_id = isset($_GET['class_id']) ? $_GET['class_id'] : null;
        $monthly_id = isset($_GET['monthly_id']) ? $_GET['monthly_id'] : null;
        
        $result = $this->rankings->getMonthlyRankings($class_id, $monthly_id);
        echo jsonResponse($result['status'] === 'success' ? 200 : 500, $result);
    }

    public function getMonthlySubjectScores() {
        $class_id = isset($_GET['class_id']) ? $_GET['class_id'] : null;
        $monthly_id = isset($_GET['monthly_id']) ? $_GET['monthly_id'] : null;
        $student_id = isset($_GET['student_id']) ? $_GET['student_id'] : null;
        
        $result = $this->rankings->getMonthlySubjectScores($class_id, $monthly_id, $student_id);
        echo jsonResponse($result['status'] === 'success' ? 200 : 500, $result);
    }

    public function getStudentRankingHistory($student_id) {
        if (!$student_id) {
            echo jsonResponse(400, [
                'status' => 'error',
                'message' => 'Student ID is required'
            ]);
            return;
        }

        $result = $this->rankings->getStudentRankingHistory($student_id);
        echo jsonResponse($result['status'] === 'success' ? 200 : 500, $result);
    }

    public function getTopStudents() {
        $class_id = isset($_GET['class_id']) ? $_GET['class_id'] : null;
        $monthly_id = isset($_GET['monthly_id']) ? $_GET['monthly_id'] : null;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;

        if (!$class_id || !$monthly_id) {
            echo jsonResponse(400, [
                'status' => 'error',
                'message' => 'Class ID and Monthly ID are required'
            ]);
            return;
        }

        $result = $this->rankings->getTopStudents($class_id, $monthly_id, $limit);
        echo jsonResponse($result['status'] === 'success' ? 200 : 500, $result);
    }
}
    
    


