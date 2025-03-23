<?php
require_once dirname(__DIR__) . '/models/Study.php';
require_once dirname(__DIR__) . '/utils/response.php';

class StudyController {
    private $studyModel;
    
    public function __construct() {
        $this->studyModel = new StudyModel();
    }
    
    public function getAllStudies() {
        try {
            $stmt = $this->studyModel->getAllStudies();
            $studies = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo jsonResponse(200, [
                'status' => 'success',
                'data' => $studies
            ]);
        } catch (Exception $e) {
            error_log("Error getting all studies: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => 'Failed to retrieve studies'
            ]);
        }
    }
    
    public function getStudiesByStudentId($studentId) {
        try {
            $stmt = $this->studyModel->getStudiesByStudentId($studentId);
            $studies = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo jsonResponse(200, [
                'status' => 'success',
                'data' => $studies
            ]);
        } catch (Exception $e) {
            error_log("Error getting studies by student ID: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => 'Failed to retrieve studies for student'
            ]);
        }
    }
    
    public function getStudiesByClassId($classId) {
        try {
            $stmt = $this->studyModel->getStudiesByClassId($classId);
            $studies = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo jsonResponse(200, [
                'status' => 'success',
                'data' => $studies
            ]);
        } catch (Exception $e) {
            error_log("Error getting studies by class ID: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => 'Failed to retrieve studies for class'
            ]);
        }
    }
    
    public function getStudiesByYearId($yearId) {
        try {
            $stmt = $this->studyModel->getStudiesByYearId($yearId);
            $studies = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo jsonResponse(200, [
                'status' => 'success',
                'data' => $studies
            ]);
        } catch (Exception $e) {
            error_log("Error getting studies by year ID: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => 'Failed to retrieve studies for academic year'
            ]);
        }
    }
    
    public function addStudy() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['student_id']) || !isset($data['class_id']) || !isset($data['year_study_id'])) {
                echo jsonResponse(400, [
                    'status' => 'error',
                    'message' => 'Required fields missing'
                ]);
                return;
            }
            
            // Check if student is already enrolled in an active class for the same academic year
            $stmt = $this->studyModel->getStudiesByStudentId($data['student_id']);
            $existingStudies = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($existingStudies as $study) {
                if ($study['year_study_id'] == $data['year_study_id'] && $study['status'] == 'active') {
                    echo jsonResponse(400, [
                        'status' => 'error',
                        'message' => 'Student is already enrolled in an active class for this academic year'
                    ]);
                    return;
                }
            }
            
            $studyId = $this->studyModel->addStudy($data);
            
            if ($studyId) {
                echo jsonResponse(201, [
                    'status' => 'success',
                    'message' => 'Study record created successfully',
                    'study_id' => $studyId
                ]);
            } else {
                echo jsonResponse(500, [
                    'status' => 'error',
                    'message' => 'Failed to create study record'
                ]);
            }
        } catch (Exception $e) {
            error_log("Error adding study: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => 'Failed to create study record'
            ]);
        }
    }
    
    public function updateStudy($id) {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['student_id']) || !isset($data['class_id']) || !isset($data['year_study_id'])) {
                echo jsonResponse(400, [
                    'status' => 'error',
                    'message' => 'Required fields missing'
                ]);
                return;
            }
            
            // Check if student is already enrolled in another active class for the same academic year
            $stmt = $this->studyModel->getStudiesByStudentId($data['student_id']);
            $existingStudies = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($existingStudies as $study) {
                // Skip the current study being updated
                if ($study['study_id'] == $id) {
                    continue;
                }
                
                // Check if there's another active enrollment in the same year
                if ($study['year_study_id'] == $data['year_study_id'] && 
                    $study['status'] == 'active' && 
                    isset($data['status']) && 
                    $data['status'] == 'active') {
                    echo jsonResponse(400, [
                        'status' => 'error',
                        'message' => 'Student is already enrolled in another active class for this academic year'
                    ]);
                    return;
                }
            }
            
            if ($this->studyModel->updateStudy($id, $data)) {
                echo jsonResponse(200, [
                    'status' => 'success',
                    'message' => 'Study record updated successfully'
                ]);
            } else {
                echo jsonResponse(500, [
                    'status' => 'error',
                    'message' => 'Failed to update study record'
                ]);
            }
        } catch (Exception $e) {
            error_log("Error updating study: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => 'Failed to update study record'
            ]);
        }
    }
    
    public function deleteStudy($id) {
        try {
            if ($this->studyModel->deleteStudy($id)) {
                echo jsonResponse(200, [
                    'status' => 'success',
                    'message' => 'Study record deleted successfully'
                ]);
            } else {
                echo jsonResponse(500, [
                    'status' => 'error',
                    'message' => 'Failed to delete study record'
                ]);
            }
        } catch (Exception $e) {
            error_log("Error deleting study: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => 'Failed to delete study record'
            ]);
        }
    }
    
    public function getStudyById($id) {
        try {
            $stmt = $this->studyModel->getStudyById($id);
            $study = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($study) {
                echo jsonResponse(200, [
                    'status' => 'success',
                    'data' => $study
                ]);
            } else {
                echo jsonResponse(404, [
                    'status' => 'error',
                    'message' => 'Study record not found'
                ]);
            }
        } catch (Exception $e) {
            error_log("Error getting study by ID: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => 'Failed to retrieve study record'
            ]);
        }
    }
    
    public function getCurrentClassForStudent($studentId) {
        try {
            $stmt = $this->studyModel->getCurrentClassForStudent($studentId);
            $currentClass = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($currentClass) {
                echo jsonResponse(200, [
                    'status' => 'success',
                    'data' => $currentClass
                ]);
            } else {
                echo jsonResponse(404, [
                    'status' => 'error',
                    'message' => 'No active class found for student'
                ]);
            }
        } catch (Exception $e) {
            error_log("Error getting current class for student: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => 'Failed to retrieve current class'
            ]);
        }
    }
    
    public function promoteByClass() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['current_class_id']) || !isset($data['new_class_id']) || !isset($data['year_study_id'])) {
                echo jsonResponse(400, [
                    'status' => 'error',
                    'message' => 'Required fields missing'
                ]);
                return;
            }
            
            // Check if any active students exist in the class before promotion
            $studiesResult = $this->studyModel->getStudiesByClassAndYear(
                $data['current_class_id'],
                $data['year_study_id'],
                'active'
            );
            
            $students = $studiesResult->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($students)) {
                echo jsonResponse(404, [
                    'status' => 'error',
                    'message' => 'No active students found in this class'
                ]);
                return;
            }
            
            $count = 0;
            foreach ($students as $student) {
                // Set current study to inactive and create new study
                $success = $this->studyModel->promoteStudent(
                    $student['student_id'],
                    $data['current_class_id'],
                    $data['new_class_id'],
                    $data['year_study_id']
                );
                
                if ($success) {
                    $count++;
                }
            }
            
            if ($count > 0) {
                echo jsonResponse(200, [
                    'status' => 'success',
                    'message' => 'Students promoted successfully',
                    'count' => $count
                ]);
            } else {
                echo jsonResponse(500, [
                    'status' => 'error',
                    'message' => 'Failed to promote students'
                ]);
            }
            
        } catch (Exception $e) {
            error_log("Error promoting students by class: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => 'Failed to promote students'
            ]);
        }
    }
    
    public function promoteStudent() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['student_id']) || !isset($data['current_class_id']) || !isset($data['new_class_id']) || !isset($data['year_study_id'])) {
                echo jsonResponse(400, [
                    'status' => 'error',
                    'message' => 'Required fields missing'
                ]);
                return;
            }
            
            $success = $this->studyModel->promoteStudent(
                $data['student_id'],
                $data['current_class_id'],
                $data['new_class_id'],
                $data['year_study_id']
            );
            
            if ($success) {
                echo jsonResponse(200, [
                    'status' => 'success',
                    'message' => 'Student promoted successfully'
                ]);
            } else {
                echo jsonResponse(500, [
                    'status' => 'error',
                    'message' => 'Failed to promote student'
                ]);
            }
            
        } catch (Exception $e) {
            error_log("Error promoting student: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => 'Failed to promote student'
            ]);
        }
    }

    public function getCurrentEnrollment($classId) {
        try {
            $stmt = $this->studyModel->getCurrentEnrollment($classId);
            $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo jsonResponse(200, [
                'status' => 'success',
                'data' => [
                    'class_id' => $classId,
                    'students' => $students,
                    'enrollment_count' => count($students)
                ]
            ]);
        } catch (Exception $e) {
            error_log("Error getting current enrollment: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => 'Failed to retrieve current enrollment'
            ]);
        }
    }
}

