<?php
require_once __DIR__ . '/../models/ClassroomSubjectMonthlyScore.php';
require_once __DIR__ . '/../utils/response.php';

class ClassroomSubjectMonthlyScoreController {
    private $classroomSubjectMonthlyScore;

    public function __construct() {
        $this->classroomSubjectMonthlyScore = new ClassroomSubjectMonthlyScore();
    }

    public function getAllClassroomSubjectMonthlyScores() {
        $result = $this->classroomSubjectMonthlyScore->getAllClassroomSubjectMonthlyScores();
        if ($result['status'] === 'success') {
            echo jsonResponse(200, $result);
        } else {
            echo jsonResponse(500, $result);
        }
    }

    public function getClassroomSubjectMonthlyScoreById($id) {
        $result = $this->classroomSubjectMonthlyScore->getClassroomSubjectMonthlyScoreById($id);
        echo jsonResponse(200, $result);
    }

    public function addClassroomSubjectMonthlyScore($data) {
        $result = $this->classroomSubjectMonthlyScore->addClassroomSubjectMonthlyScore($data);
        echo jsonResponse(200, $result);
    }

    public function updateClassroomSubjectMonthlyScore($id, $data) {
        $result = $this->classroomSubjectMonthlyScore->updateClassroomSubjectMonthlyScore($id, $data);
        echo jsonResponse(200, $result);
    }
    
    public function deleteClassroomSubjectMonthlyScore($id) {
        $result = $this->classroomSubjectMonthlyScore->deleteClassroomSubjectMonthlyScore($id);
        echo jsonResponse(200, $result);
    }

    public function getClassroomSubjectMonthlyScoresByClassId($classId) {
        $result = $this->classroomSubjectMonthlyScore->getClassroomSubjectMonthlyScoresByClassId($classId);
        echo jsonResponse(200, $result);
    }

    public function getClassroomSubjectMonthlyScoresByClassAndMonth($classId, $monthlyId) {
        $result = $this->classroomSubjectMonthlyScore->getClassroomSubjectMonthlyScoresByClassAndMonth($classId, $monthlyId);
        echo jsonResponse(200, $result);
    }

    public function getClassroomSubjectMonthlyScoresbyMonthlyIdandClassId($monthlyId, $classId) {
        $result = $this->classroomSubjectMonthlyScore->getClassroomSubjectMonthlyScoresbyMonthlyIdandClassId($monthlyId, $classId);
        if ($result['status'] === 'success') {
            echo jsonResponse(200, $result);
        } else {
            echo jsonResponse(500, $result);
        }
    }

    public function getByClassSubjectMonthly() {
        try {
            $class_id = $_GET['class_id'] ?? null;
            $assign_subject_grade_id = $_GET['assign_subject_grade_id'] ?? null;
            $monthly_id = $_GET['monthly_id'] ?? null;

            if (!$class_id || !$assign_subject_grade_id || !$monthly_id) {
                echo jsonResponse(400, [
                    'status' => 'error',
                    'message' => 'Missing required parameters'
                ]);
                return;
            }

            $result = $this->classroomSubjectMonthlyScore->getByClassSubjectMonthly(
                $class_id,
                $assign_subject_grade_id,
                $monthly_id
            );

            echo jsonResponse(
                $result['status'] === 'success' ? 200 : 404,
                $result
            );
        } catch (Exception $e) {
            error_log("Error in getByClassSubjectMonthly: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => 'Server error occurred'
            ]);
        }
    }
}

