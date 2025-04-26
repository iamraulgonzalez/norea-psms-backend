<?php
require_once __DIR__ . '/../models/SemesterExamSubjects.php';
require_once __DIR__ . '/../utils/response.php';

class SemesterExamSubjectsController {
    private $semesterExamSubjects;

    public function __construct() {
        $this->semesterExamSubjects = new SemesterExamSubjects();
    }

    public function getAllSemesterExamSubjects() {
        try {
            $result = $this->semesterExamSubjects->fetchAll();
            echo jsonResponse(200, $result);
        } catch (Exception $e) {
            error_log("Error in getAllSemesterExamSubjects: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => 'Server error occurred'
            ]);
        }
    }

    public function addSemesterExamSubject($data) {
        try {
            $result = $this->semesterExamSubjects->create($data);
            echo jsonResponse(200, $result);
        } catch (Exception $e) {
            error_log("Error in addSemesterExamSubject: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => 'Server error occurred'
            ]);
        }
    }
    
    public function updateSemesterExamSubject($id) {
        try {
            // Get data from request body
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data) {
                echo jsonResponse(400, [
                    'status' => 'error',
                    'message' => 'Invalid or missing request data'
                ]);
                return;
            }
            
            // If updating monthly_ids for all subjects in a class/semester
            if (isset($data['update_all']) && $data['update_all'] === true) {
                $class_id = $data['class_id'] ?? null;
                $semester_id = $data['semester_id'] ?? null;
                
                if (!$class_id || !$semester_id) {
                    echo jsonResponse(400, [
                        'status' => 'error',
                        'message' => 'Missing class_id or semester_id for batch update'
                    ]);
                    return;
                }
                
                $result = $this->semesterExamSubjects->updateAllForClassAndSemester(
                    $class_id, 
                    $semester_id, 
                    $data
                );
            } else {
                // Regular single record update
                $result = $this->semesterExamSubjects->updatee($id, $data);
            }
            
            echo jsonResponse(200, $result);
        } catch (Exception $e) {
            error_log("Error in updateSemesterExamSubject: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => 'Server error occurred'
            ]);
        }
    }
    
    public function deleteSemesterExamSubject($id) {
        try {
            $result = $this->semesterExamSubjects->delete($id);
            
            echo jsonResponse(
                $result['status'] === 'success' ? 200 : 400,
                $result
            );
        } catch (Exception $e) {
            error_log("Error in deleteSemesterExamSubject: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => 'Server error occurred'
            ]);
        }
    }

    public function getSemesterExamSubjectByClassId($classId) {
        $result = $this->semesterExamSubjects->getSemesterExamSubjectsByClassId($classId);
        echo jsonResponse(200, $result);
    }
    
    public function getAvailableMonthlyScores($classId) {
        try {
            $result = $this->semesterExamSubjects->getAvailableMonthlyScores($classId);
            echo jsonResponse(
                $result['status'] === 'success' ? 200 : 404,
                $result
            );
        } catch (Exception $e) {
            error_log("Error in getAvailableMonthlyScores: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => 'Server error occurred'
            ]);
        }
    }
    
    public function getByClassSubjectSemester() {
        try {
            $class_id = $_GET['class_id'] ?? null;
            $assign_subject_grade_id = $_GET['assign_subject_grade_id'] ?? null;
            $semester_id = $_GET['semester_id'] ?? null;

            if (!$class_id || !$assign_subject_grade_id || !$semester_id) {
                echo jsonResponse(400, [
                    'status' => 'error',
                    'message' => 'Missing required parameters'
                ]);
                return;
            }

            $result = $this->semesterExamSubjects->getByClassSubjectSemester(
                $class_id,
                $assign_subject_grade_id,
                $semester_id
            );

            echo jsonResponse(
                $result['status'] === 'success' ? 200 : 404,
                $result
            );
        } catch (Exception $e) {
            error_log("Error in getByClassSubjectSemester: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => 'Server error occurred'
            ]);
        }
    }
}

