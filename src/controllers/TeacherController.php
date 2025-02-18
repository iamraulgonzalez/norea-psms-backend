<?php
require_once __DIR__ . '/../models/Teacher.php';
require_once __DIR__ . '/../utils/response.php';

class TeacherController {
    public function getAllTeachers() {
        $teacher = new Teacher();
        $teachers = $teacher->fetchAll();
        echo jsonResponse(200, $teachers);
    }

    public function addTeacher($data) {
        $teacher = new Teacher();
        $result = $teacher->create($data);
        echo jsonResponse(201, ['message' => 'Teacher created successfully']);
    }
    
    public function updateTeacher($id, $data) {
        $teacher = new Teacher();
        $result = $teacher->update($id, $data);
        echo jsonResponse(200, ['message' => 'Teacher updated successfully']);
    }
    
    public function deleteTeacher($id) {
        $teacher = new Teacher();
        $result = $teacher->delete($id);
        echo jsonResponse(200, ['message' => 'Teacher deleted successfully']);
    }
    
    public function getTeacherById($id) {
        $teacher = new Teacher();
        $teacher = $teacher->fetchById($id);
        echo jsonResponse(200, $teacher);
    }

    public function getTeachersByClassId($class_id) {
        try {
            $teacher = new Teacher();
            if (ob_get_level()) ob_end_clean();
            
            header('Content-Type: application/json; charset=utf-8');
            
            $teachers = $teacher->getTeachersByClassId($class_id);
            
            echo json_encode($teachers, JSON_UNESCAPED_UNICODE);
            exit();

        } catch (Exception $e) {
            error_log("Error getting teacher by class: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to get teachers'
            ]);
            exit();

        }
    }

    public function getTeacherCount() {
        $teacher = new Teacher();

        try {
            $count = $teacher->getCount();
            jsonResponse(200, [
                'status' => 'success',
                'count' => (int)$count
            ]);
        } catch (Exception $e) {
            errorResponse(500, 'Failed to get teacher count');
        }
    }
}