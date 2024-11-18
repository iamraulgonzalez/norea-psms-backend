<?php
require_once __DIR__ . '/../models/Teacher.php';
require_once __DIR__ . '/../utils/response.php';

class TeacherController {
    public function getAllTeachers() {
        $teacher = new Teacher();
        $teachers = $teacher->fetchAll();
        echo jsonResponse(200, $teachers);
    }
}