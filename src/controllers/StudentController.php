<?php
require_once __DIR__ . '/../models/Student.php';
require_once __DIR__ . '/../utils/response.php';

class StudentController {
    public function getAllStudents() {
        $student = new Student();
        $students = $student->fetchAll();
        echo jsonResponse(200, $students);
    }

    public function addStudent($data) {
        $student = new Student();
        $result = $student->create($data);
        echo jsonResponse(201, ['message' => 'Student created successfully']);
    }
}