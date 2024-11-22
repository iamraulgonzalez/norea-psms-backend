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
}