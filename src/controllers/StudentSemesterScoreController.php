<?php

require_once __DIR__ . '/../models/StudentSemesterScore.php';
require_once __DIR__ . '/../utils/response.php';    

class SemesterScoreController {
    private $semesterScore;
    private $studentSemesterExamScores;

    public function __construct() {
        $this->semesterScore = new StudentSemesterScore();
        require_once __DIR__ . '/../models/StudentSemesterExamScores.php';
        $this->studentSemesterExamScores = new StudentSemesterExamScores();
    }

    public function getAllStudentSemesterScore() {
        $semesters_score = $this->semesterScore->fetchAll();
        echo jsonResponse(200, $semesters_score);
    }
    
    public function AddSemesterScore($data) {
        $result = $this->semesterScore->create($data);
        echo jsonResponse(201, ['message' => 'SemesterScore created successfully']);
    }

    public function updateSemesterScore($id, $data) {
        $result = $this->semesterScore->update($id, $data);
        echo jsonResponse(200, ['message' => 'SemesterScore updated successfully']);
    }

    public function deleteSemesterScore($id) {
        $result = $this->semesterScore->delete($id);
        echo jsonResponse(200, ['message' => 'SemesterScore deleted successfully']);
    }
    
    public function getSemesterScoreById    ($id) {
        $semester_scores = $this->semesterScore->fetchById($id);
        echo jsonResponse(200, $semester_scores);
    }
    
    /**
     * Get all semester final scores for students in a specific class
     * 
     * @param int $class_id The class ID
     * @param int $semester_id The semester ID
     * @return void Outputs JSON response
     */
    public function getClassSemesterScores($class_id, $semester_id = null) {
        // Validate parameters
        if (empty($class_id)) {
            echo jsonResponse(400, ['status' => 'error', 'message' => 'Class ID is required']);
            return;
        }
        
        // If semester_id is not provided, handle appropriately (use current semester or return error)
        if (empty($semester_id)) {
            // Could implement logic to get current semester if needed
            echo jsonResponse(400, ['status' => 'error', 'message' => 'Semester ID is required']);
            return;
        }
        
        $result = $this->semesterScore->getClassSemesterScores($class_id, $semester_id);
        echo jsonResponse(200, $result);
    }

    public function getClassSemesterExamScoresWithMonthly($class_id) {
        try {
            $semester_id = $_GET['semester_id'] ?? null;
            
            if (!$semester_id) {
                echo jsonResponse(400, [
                    'status' => 'error',
                    'message' => 'Missing required parameter: semester_id'
                ]);
                return;
            }

            $result = $this->studentSemesterExamScores->getClassSemesterExamScoresWithMonthly(
                $class_id, 
                $semester_id
            );
            
            echo jsonResponse(
                $result['status'] === 'success' ? 200 : 404,
                $result
            );
        } catch (Exception $e) {
            error_log("Error in getClassSemesterExamScoresWithMonthly: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => 'Server error occurred'
            ]);
        }
    }
}