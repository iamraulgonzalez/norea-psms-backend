<?php
require_once __DIR__ . '/../models/AssignSubjectGrade.php';
require_once __DIR__ . '/../utils/response.php';

class AssignSubjectGradeController {
    private $assignSubjectGrade;

    public function __construct() {
        $this->assignSubjectGrade = new AssignSubjectGrade();
    }

    public function getAllAssignSubjectGrades() {
        $result = $this->assignSubjectGrade->getAllAssignSubjectGrades();
        echo jsonResponse(200, $result);
    }

    public function getSubjectsByGradeId($gradeId) {
        $result = $this->assignSubjectGrade->getSubjectsByGradeId($gradeId);
        echo jsonResponse(200, $result);
    }

    public function addSubjectsToGrade() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['grade_id']) || !isset($data['codes']) || 
            !is_array($data['codes'])) {
            echo jsonResponse(400, [
                'status' => 'error',
                'message' => 'Invalid input data'
            ]);
            return;
        }

        $result = $this->assignSubjectGrade->addSubjectsToGrade(
            $data['grade_id'],
            $data['codes']
        );
        
        echo jsonResponse(200, $result);
    }

    public function deleteSubjectFromGrade($assignSubjectGradeId) {
        $result = $this->assignSubjectGrade->deleteSubjectFromGrade($assignSubjectGradeId);
        echo jsonResponse(200, $result);
    }
} 