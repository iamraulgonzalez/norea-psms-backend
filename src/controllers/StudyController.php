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
            $data = json_decode(file_get_contents("php://input"), true);
            
            if (empty($data['student_id']) || empty($data['class_id']) || empty($data['year_study_id'])) {
                echo jsonResponse(400, [
                    'status' => 'error',
                    'message' => 'Missing required fields'
                ]);
                return;
            }
            
            $studyId = $this->studyModel->addStudy($data);
            
            if ($studyId) {
                echo jsonResponse(201, [
                    'status' => 'success',
                    'message' => 'Study added successfully',
                    'data' => ['study_id' => $studyId]
                ]);
            } else {
                throw new Exception("Failed to add study");
            }
        } catch (Exception $e) {
            if ($e->getMessage() === "Student not found") {
                echo jsonResponse(404, [
                    'status' => 'error',
                    'message' => 'សិស្សមិនត្រូវបានរកឃើញ'
                ]);
            } else {
                error_log("Error adding study: " . $e->getMessage());
                echo jsonResponse(500, [
                    'status' => 'error',
                    'message' => $e->getMessage()
                ]);
            }
        }
    }
    
    public function addMultipleStudies() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!$data || !isset($data['student_ids']) || !isset($data['class_id']) || !isset($data['year_study_id'])) {
                echo jsonResponse(400, [
                    'status' => 'error',
                    'message' => 'Missing required fields'
                ]);
                return;
            }
            
            $result = $this->studyModel->addMultipleStudies($data);
            
            echo jsonResponse(201, [
                'status' => 'success',
                'message' => $result['message'],
                'data' => [
                    'success_count' => $result['success_count'],
                    'failed_students' => $result['failed_students']
                ]
            ]);
            
        } catch (Exception $e) {
            error_log("Error adding multiple studies: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
    
    public function updateStudy($id, $data) {
        try {
            // Validate required fields
            if (empty($data['student_id']) || empty($data['class_id']) || empty($data['year_study_id'])) {
                echo jsonResponse(400, [
                    'status' => 'error',
                    'message' => 'សូមបំពេញព័ត៌មានដែលត្រូវការទាំងអស់'
                ]);
                return;
            }

            // Validate enrollment date if provided
            if (!empty($data['enrollment_date'])) {
                $date = DateTime::createFromFormat('Y-m-d', $data['enrollment_date']);
                if (!$date || $date->format('Y-m-d') !== $data['enrollment_date']) {
                    echo jsonResponse(400, [
                        'status' => 'error',
                        'message' => 'ទម្រង់កាលបរិច្ឆេទមិនត្រឹមត្រូវទេ'
                    ]);
                    return;
                }
            }

            // Validate status if provided
            if (!empty($data['status']) && !in_array($data['status'], ['active', 'inactive'])) {
                echo jsonResponse(400, [
                    'status' => 'error',
                    'message' => 'ស្ថានភាពមិនត្រឹមត្រូវទេ'
                ]);
                return;
            }

            try {
                $result = $this->studyModel->updateStudy($id, $data);
                
                if ($result) {
                    echo jsonResponse(200, [
                        'status' => 'success',
                        'message' => 'កែប្រែថ្នាក់រៀនសម្រាប់សិស្សជោគជ័យ'
                    ]);
                } else {
                    throw new Exception("កែប្រែប្រវត្តិសិស្សមិនជោគជ័យ");
                }
            } catch (Exception $e) {
                // Handle specific error messages from the model
                $errorMessage = $e->getMessage();
                
                // Map specific error messages to appropriate HTTP status codes
                $statusCode = 500;
                if (strpos($errorMessage, 'មិនត្រូវបានរកឃើញ') !== false) {
                    $statusCode = 404;
                } elseif (strpos($errorMessage, 'មានសិស្សពេញហើយ') !== false) {
                    $statusCode = 400;
                } elseif (strpos($errorMessage, 'កំពុងរៀននៅថ្នាក់ផ្សេង') !== false) {
                    $statusCode = 400;
                } elseif (strpos($errorMessage, 'មានពិន្ទុប្រចាំខែរួចហើយ') !== false) {
                    $statusCode = 400;
                } elseif (strpos($errorMessage, 'មានពិន្ទុប្រចាំឆមាសរួចហើយ') !== false) {
                    $statusCode = 400;
                }

                echo jsonResponse($statusCode, [
                    'status' => 'error',
                    'message' => $errorMessage
                ]);
            }
        } catch (Exception $e) {
            error_log("Error updating study: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => 'កែប្រែប្រវត្តិសិស្សមិនជោគជ័យ'
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

    private function sendJsonResponse($success, $message, $data = null, $statusCode = 200) {
        http_response_code($statusCode);
        echo json_encode([
            'status' => $success ? 'success' : 'error',
            'message' => $message,
            'data' => $data
        ]);
    }

    //can this get student by grade id and final semester average score > 8.0
    public function getStudentsByGradeId($grade_id) {
        try {
            if (!$grade_id) {
                echo jsonResponse(400, [
                    'status' => 'error',
                    'message' => 'Grade ID is required'
                ]);
                return;
            }

            $result = $this->studyModel->fetchStudentByGradeId($grade_id);
            
            if ($result['status'] === 'error') {
                echo jsonResponse(500, $result);
                return;
            }

            echo jsonResponse(200, $result);
            
        } catch (Exception $e) {
            error_log("Error in getStudentsByGradeId: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => 'Failed to fetch students by grade: ' . $e->getMessage()
            ]);
        }
    }

    public function getStudentRankingsByGradeId($grade_id) {
        try {
            if (!$grade_id) {
                echo jsonResponse(400, [
                    'status' => 'error',
                    'message' => 'Grade ID is required'
                ]);
                return;
            }
    
            $result = $this->studyModel->fetchStudentRankingAndAverages($grade_id); // ✅ right method now
    
            if ($result['status'] === 'error') {
                echo jsonResponse(500, $result);
                return;
            }
    
            echo jsonResponse(200, $result);
    
        } catch (Exception $e) {
            error_log("Error in getStudentRankingsByGradeId: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => 'Failed to fetch student rankings: ' . $e->getMessage()
            ]);
        }
    }

    public function getTopFiveMonthlyStudent($classId, $monthly_id) {
        try {
            $result = $this->studyModel->getTopFiveMonthlyStudent($classId, $monthly_id);
            
            if ($result['status'] === 'error') {
                echo jsonResponse(500, $result);
                return;
            }

            echo jsonResponse(200, $result);
        } catch (Exception $e) {
            error_log("Error in getTopFiveMonthlyStudent: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => 'Failed to fetch top 5 monthly students: ' . $e->getMessage()
            ]);
        }
    }

    public function getTopFiveSemesterStudent($classId, $semester_id) {
        try {
            $result = $this->studyModel->getTopFiveSemesterStudent($classId, $semester_id);
            
            if ($result['status'] === 'error') {
                echo jsonResponse(500, $result);
                return;
            }

            echo jsonResponse(200, $result);
        } catch (Exception $e) {
            error_log("Error in getTopFiveSemesterStudent: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => 'Failed to fetch top 5 semester students: ' . $e->getMessage()
            ]);
        }
    }

    public function getTopFiveYearlyStudent($classId, $year_study_id) {
        try {
            $result = $this->studyModel->getTopFiveYearlyStudent($classId, $year_study_id);
            
            if ($result['status'] === 'error') {
                echo jsonResponse(500, $result);
                return;
            }

            echo jsonResponse(200, $result);
        } catch (Exception $e) {
            error_log("Error in getTopFiveYearlyStudent: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => 'Failed to fetch top 5 yearly students: ' . $e->getMessage()
            ]);
        }
    }

    public function promoteStudentToNextClass($studentId, $classId, $yearStudyId) {
        try {
            $result = $this->studyModel->promoteStudentToNextClass($studentId, $classId, $yearStudyId);
            
            if ($result['status'] === 'error') {
                echo jsonResponse(500, $result);
                return;
            }

            echo jsonResponse(200, $result);
        } catch (Exception $e) {
            error_log("Error in promoteStudentToNextClass: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => 'Failed to promote student to next class: ' . $e->getMessage()
            ]);
        }
    }

    public function promoteStudentsByGrade($gradeId, $yearStudyId) {
        try {
            if (!$gradeId || !$yearStudyId) {
                echo jsonResponse(400, [
                    'status' => 'error',
                    'message' => 'Grade ID and Year Study ID are required'
                ]);
                return;
            }

            $result = $this->studyModel->promoteStudentsByGrade($gradeId, $yearStudyId);
            
            if ($result['status'] === 'error') {
                echo jsonResponse(500, $result);
                return;
            }

            echo jsonResponse(200, $result);
        } catch (Exception $e) {
            error_log("Error in promoteStudentsByGrade: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => 'Failed to promote students: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteStudentFromStudy($studentId, $classId, $yearStudyId) {
        try {
            $result = $this->studyModel->deleteStudentFromStudy($studentId, $classId, $yearStudyId);
            
            if ($result['status'] === 'error') {
                echo jsonResponse(500, $result);
                return;
            }

            echo jsonResponse(200, $result);
        } catch (Exception $e) {
            error_log("Error in deleteStudentFromStudy: " . $e->getMessage());
            echo jsonResponse(500, [
                'status' => 'error',
                'message' => 'Failed to delete student from study: ' . $e->getMessage()
            ]);
        }
    }
}


