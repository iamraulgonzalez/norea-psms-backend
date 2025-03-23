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

    public function getSemesterExamSubjectById($id) {
        try {
            $result = $this->semesterExamSubjects->getById($id);
            echo jsonResponse(
                $result['status'] === 'success' ? 200 : 404,
                $result
            );
        } catch (Exception $e) {
            error_log("Error in getSemesterExamSubjectById: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => 'Server error occurred'
            ]);
        }
    }

    public function getSemesterExamSubjectsByClassAndSemester($class_id, $semester_id) {
        try {
            $result = $this->semesterExamSubjects->getByClassAndSemester($class_id, $semester_id);
            echo jsonResponse(
                $result['status'] === 'success' ? 200 : 404,
                $result
            );
        } catch (Exception $e) {
            error_log("Error in getSemesterExamSubjectsByClassAndSemester: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => 'Server error occurred'
            ]);
        }
    }

    public function addSemesterExamSubject() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $result = $this->semesterExamSubjects->create($data);
            
            echo jsonResponse(
                $result['status'] === 'success' ? 201 : 400,
                $result
            );
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
            $data = json_decode(file_get_contents('php://input'), true);
            $result = $this->semesterExamSubjects->update($id, $data);
            
            echo jsonResponse(
                $result['status'] === 'success' ? 200 : 400,
                $result
            );
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
}
