<?php
require_once __DIR__ . '/../models/StudentInfo.php';
require_once __DIR__ . '/../utils/response.php';

class StudentInfoController {

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

    public function updateStudent($id, $data) {
        $student = new Student();
        $result = $student->update($id, $data);
        echo jsonResponse(200, ['message' => 'Student updated successfully']);
    }

    public function deleteStudent($id) {
        $student = new Student();
        $result = $student->delete($id);
        echo jsonResponse(200, ['message' => 'Student deleted successfully']);
    }

    public function getStudentById($id) {
        $student = new Student();
        $student = $student->fetchById($id);
        echo jsonResponse(200, $student);
    }
}