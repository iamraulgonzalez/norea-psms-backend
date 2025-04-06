<?php

require_once __DIR__ . '/../models/Report.php';
require_once __DIR__ . '/../utils/response.php';

class ReportController {
    private $report;

    public function __construct() {
        $this->report = new Report();
    }

    public function getStudentMonthlyScoreReport() {
        try {
            // Get and validate input parameters
            $filters = [];
            
            if (isset($_GET['student_id']) && is_numeric($_GET['student_id'])) {
                $filters['student_id'] = (int)$_GET['student_id'];
            }
            
            if (isset($_GET['class_id']) && is_numeric($_GET['class_id'])) {
                $filters['class_id'] = (int)$_GET['class_id'];
            }
            
            if (isset($_GET['monthly_id']) && is_numeric($_GET['monthly_id'])) {
                $filters['monthly_id'] = (int)$_GET['monthly_id'];
            }

            // Get the report data
            $result = $this->report->getStudentMonthlyScoreReport($filters);

            if ($result === false) {
                echo jsonResponse(500, [
                    'message' => 'Failed to fetch student monthly score report',
                    'error' => 'Database error occurred'
                ]);
                return;
            }

            if (empty($result)) {
                echo jsonResponse(404, [
                    'message' => 'No data found for the specified criteria',
                    'data' => []
                ]);
                return;
            }

            echo jsonResponse(200, [
                'message' => 'Student monthly score report retrieved successfully',
                'data' => $result
            ]);

        } catch (Exception $e) {
            error_log("Error in getStudentMonthlyScoreReport: " . $e->getMessage());
            echo jsonResponse(500, [
                'message' => 'Internal server error',
                'error' => 'An unexpected error occurred'
            ]);
        }
    }
}


