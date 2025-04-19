<?php
require_once __DIR__ . '/../models/Semster.php';
require_once __DIR__ . '/../utils/response.php';

class SemesterController {
    public function getAllSemesters() {
        $semester = new Semster();
        $semesters = $semester->fetchAll();
        echo jsonResponse(200, $semesters);
    }

    public function addSemester($data) {
        $semester = new Semster();
        $result = $semester->create($data);
        if (isset($result['status']) && $result['status'] === 'error') {
            return jsonResponse(400, $result);
        }
        echo jsonResponse(201, ['message' => 'Semester created successfully']);
    }

    public function updateSemester($id, $data) {
        $semester = new Semster();
        $result = $semester->update($id, $data);
        if (isset($result['status']) && $result['status'] === 'error') {
            return jsonResponse(400, $result);
        }
        echo jsonResponse(200, ['message' => 'Semester updated successfully']);
    }

    public function deleteSemester($id) {
        $semester = new Semster();
        $result = $semester->delete($id);
        if (isset($result['status']) && $result['status'] === 'error') {
            return jsonResponse(400, $result);
        }
        echo jsonResponse(200, ['message' => 'Semester deleted successfully']);
    }

    public function getSemesterById($id) {
        $semester = new Semster();
        $semesters = $semester->fetchById($id);
        echo jsonResponse(200, $semesters);
    }
}