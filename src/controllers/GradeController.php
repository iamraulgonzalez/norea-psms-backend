<?php
require_once __DIR__ . '/../models/Grade.php';
require_once __DIR__ . '/../utils/response.php';

class GradeController {
    public function getAllGrades() {
        $grade = new Grade();
        $grades = $grade->fetchAll();
        echo jsonResponse(200, $grades);
    }

    public function addGrade($data) {
        $grade = new Grade();
        $result = $grade->create($data);
        echo jsonResponse(201, ['message' => 'Grade created successfully']);
    }

    public function updateGrade($id, $data) {
        $grade = new Grade();
        $result = $grade->update($id, $data);
        echo jsonResponse(200, ['message' => 'Grade updated successfully']);
    }

    public function deleteGrade($id) {
        $grade = new Grade();
        $result = $grade->delete($id);
        echo jsonResponse(200, ['message' => 'Grade deleted successfully']);
    }

    public function getGradeById($id) {
        $grade = new Grade();
        $grades = $grade->fetchById($id);
        echo jsonResponse(200, $grades);
    }
}