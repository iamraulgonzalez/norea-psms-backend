<?php
require_once __DIR__ . '/../models/StudentSemesterExamScores.php';
require_once __DIR__ . '/../utils/response.php';

class StudentSemesterExamScoresController {
    private $studentSemesterExamScores;

    public function __construct() {
        $this->studentSemesterExamScores = new StudentSemesterExamScores();
    }

    public function getStudentSemesterExamScores($student_id) {
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

            $result = $this->studentSemesterExamScores->getStudentExamScores(
                $student_id, 
                $class_id, 
                $semester_id
            );
            
            echo jsonResponse(
                $result['status'] === 'success' ? 200 : 404,
                $result
            );
        } catch (Exception $e) {
            error_log("Error in getStudentSemesterExamScores: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => 'Server error occurred'
            ]);
        }
    }

    public function getClassSemesterExamScores($class_id) {
        try {
            $semester_id = $_GET['semester_id'] ?? null;
            
            if (!$semester_id) {
                echo jsonResponse(400, [
                    'status' => 'error',
                    'message' => 'Missing required parameter: semester_id'
                ]);
                return;
            }

            $result = $this->studentSemesterExamScores->getClassExamScores(
                $class_id, 
                $semester_id
            );
            
            echo jsonResponse(
                $result['status'] === 'success' ? 200 : 404,
                $result
            );
        } catch (Exception $e) {
            error_log("Error in getClassSemesterExamScores: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => 'Server error occurred'
            ]);
        }
    }

    public function addSemesterExamScore() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $result = $this->studentSemesterExamScores->create($data);
            
            echo jsonResponse(
                $result['status'] === 'success' ? 201 : 400,
                $result
            );
        } catch (Exception $e) {
            error_log("Error in addSemesterExamScore: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => 'Server error occurred'
            ]);
        }
    }

    public function updateSemesterExamScore($id) {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $result = $this->studentSemesterExamScores->update($id, $data);
            
            echo jsonResponse(
                $result['status'] === 'success' ? 200 : 400,
                $result
            );
        } catch (Exception $e) {
            error_log("Error in updateSemesterExamScore: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => 'Server error occurred'
            ]);
        }
    }

    public function deleteSemesterExamScore($id) {
        try {
            $result = $this->studentSemesterExamScores->delete($id);
            
            echo jsonResponse(
                $result['status'] === 'success' ? 200 : 400,
                $result
            );
        } catch (Exception $e) {
            error_log("Error in deleteSemesterExamScore: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => 'Server error occurred'
            ]);
        }
    }

    public function calculateSemesterScores() {
        try {
            // Call the recalculateSemesterScores method from the model
            $result = $this->studentSemesterExamScores->recalculateSemesterScores();
            
            if ($result) {
                echo jsonResponse(200, [
                    'status' => 'success',
                    'message' => 'Semester scores calculated successfully'
                ]);
            } else {
                echo jsonResponse(500, [
                    'status' => 'error',
                    'message' => 'Failed to calculate semester scores'
                ]);
            }
        } catch (Exception $e) {
            error_log("Error in calculateSemesterScores: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => 'Server error occurred'
            ]);
        }
    }

    public function recalculateSemesterScores() {
        try {
            $result = $this->studentSemesterExamScores->recalculateSemesterScores();
            
            if ($result) {
                echo jsonResponse(200, [
                    'status' => 'success',
                    'message' => 'Semester scores recalculated successfully'
                ]);
            } else {
                echo jsonResponse(500, [
                    'status' => 'error',
                    'message' => 'Failed to recalculate semester scores'
                ]);
            }
        } catch (Exception $e) {
            error_log("Error in recalculateSemesterScores: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => 'Server error occurred'
            ]);
        }
    }

    public function getAvailableMonthsForClass($class_id) {
        try {
            $result = $this->studentSemesterExamScores->getAvailableMonthsForClass($class_id);
            
            echo jsonResponse(
                $result['status'] === 'success' ? 200 : 404,
                $result
            );
        } catch (Exception $e) {
            error_log("Error in getAvailableMonthsForClass: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => 'Server error occurred'
            ]);
        }
    }

    public function getMonthlyScoresForSubject() {
        try {
            $student_id = $_GET['student_id'] ?? null;
            $class_id = $_GET['class_id'] ?? null;
            $assign_subject_grade_id = $_GET['assign_subject_grade_id'] ?? null;
            
            if (!$student_id || !$class_id || !$assign_subject_grade_id) {
                echo jsonResponse(400, [
                    'status' => 'error',
                    'message' => 'Missing required parameters: student_id, class_id, and assign_subject_grade_id'
                ]);
                return;
            }

            $result = $this->studentSemesterExamScores->getMonthlyScoresForSubject(
                $student_id, 
                $class_id, 
                $assign_subject_grade_id
            );
            
            echo jsonResponse(
                $result['status'] === 'success' ? 200 : 404,
                $result
            );
        } catch (Exception $e) {
            error_log("Error in getMonthlyScoresForSubject: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => 'Server error occurred'
            ]);
        }
    }
}
