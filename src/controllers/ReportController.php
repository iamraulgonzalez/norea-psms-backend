<?php

require_once dirname(__DIR__) . '/models/Report.php';
require_once dirname(__DIR__) . '/utils/response.php';

class ReportController {
    private $reportModel;
    
    public function __construct() {
        $this->reportModel = new Report();
    }

    public function getStudentMonthlyScoreReport() {
        try {
            // Get parameters from request
            $class_id = $_GET['class_id'] ?? null;
            $monthly_id = $_GET['monthly_id'] ?? null;
            $year_study_id = $_GET['year_study_id'] ?? null;

            // Validate required parameters
            if (!$class_id || !$monthly_id || !$year_study_id) {
                echo jsonResponse(400, [
                    'status' => 'error',
                    'message' => 'Class ID, Monthly ID, and Year Study are required'
                ]);
                return;
            }

            // Get report data
            $result = $this->reportModel->getStudentMonthlyScoreReport($class_id, $monthly_id, $year_study_id);
            
            // Check if result is valid
            if ($result === false) {
                echo jsonResponse(404, [
                    'status' => 'error',
                    'message' => 'No data found for the specified parameters'
                ]);
                return;
            }

            // If result is an array with status key
            if (is_array($result) && isset($result['status'])) {
                if ($result['status'] === 'error') {
                    echo jsonResponse(500, $result);
                    return;
                }
            }

            // Return successful response
            echo jsonResponse(200, [
                'status' => 'success',
                'data' => $result
            ]);

        } catch (Exception $e) {
            error_log("Error in getStudentMonthlyScoreReport: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => 'Failed to generate student monthly score report: ' . $e->getMessage()
            ]);
        }
    }

    public function getStudentSemesterScoreReport() {
        try {
            // Get parameters from request
            $class_id = $_GET['class_id'] ?? null;
            $semester_id = $_GET['semester_id'] ?? null;
            $year_study_id = $_GET['year_study_id'] ?? null;

            // Validate required parameters
            if (!$class_id || !$semester_id || !$year_study_id) {
                echo jsonResponse(400, [
                    'status' => 'error',
                    'message' => 'Class ID, Semester ID, and Year Study are required'
                ]);
                return;
            }

            // Get report data
            $result = $this->reportModel->getStudentSemesterScoreReport($class_id, $semester_id, $year_study_id);

            // Check if result is valid
            if ($result === false) {
                echo jsonResponse(404, [
                    'status' => 'error',
                    'message' => 'No data found for the specified parameters'
                ]);
                return;
            }

            // If result is an array with status key
            if (is_array($result) && isset($result['status'])) {
                if ($result['status'] === 'error') {
                    echo jsonResponse(500, $result);
                    return;
                }
            }

            // Return successful response
            echo jsonResponse(200, [
                'status' => 'success',
                'data' => $result
            ]);

        } catch (Exception $e) {
            error_log("Error in getStudentSemesterScoreReport: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => 'Failed to generate student semester score report: ' . $e->getMessage()
            ]);
        }
    }

    public function getAllStudentbyGrade() {
        try {
            $grade_id = $_GET['grade_id'] ?? null;

            if (!$grade_id) {
                echo jsonResponse(400, [
                    'status' => 'error',
                    'message' => 'Grade ID is required'
                ]);
                return;
            }

            $result = $this->reportModel->getAllStudentbyGrade($grade_id);

            echo jsonResponse(200, [
                'status' => 'success',
                'data' => $result
            ]);
        } catch (Exception $e) {
            error_log("Error in getAllStudentbyGrade: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => 'Failed to get all students by grade: ' . $e->getMessage()
            ]);
        }
    }

    public function getAllStudentbyYearStudy($year_study_id) {
        try {
            $result = $this->reportModel->getAllStudentbyYearStudy($year_study_id);

            echo jsonResponse(200, [
                'status' => 'success',
                'data' => $result
            ]);
        } catch (Exception $e) {
            error_log("Error in getAllStudentbyYearStudy: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => 'Failed to get all students by year study: ' . $e->getMessage()
            ]);
        }
    }

    public function getAllStudentbyYearStudyGraduate($year_study_id){
        try{
            $result = $this->reportModel->getAllStudentbyYearStudyGraduate($year_study_id);

            echo jsonResponse(200, [
                'status' => 'success',
                'data' => $result
            ]);
        } catch (Exception $e) {
            error_log("Error in getAllStudentbyYearStudy: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => 'Failed to get all students by year study: ' . $e->getMessage()
            ]);
        }
    }

    public function getStudentByGrade($grade_id) {
        try {
            $result = $this->reportModel->getStudentByGrade($grade_id);

            echo jsonResponse(200, [
                'status' => 'success',
                'data' => $result
            ]);
        } catch (Exception $e) {
            error_log("Error in getStudentByGrade: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => 'Failed to get student by grade: ' . $e->getMessage()
            ]);
        }
    }
}


