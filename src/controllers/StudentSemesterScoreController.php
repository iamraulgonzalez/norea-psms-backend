<?php

require_once __DIR__ . '/../models/StudentSemesterScore.php';
require_once __DIR__ . '/../utils/response.php';    

class SemesterScoreController {

    public function getAllStudentSemesterScore() {
        $semester_score = new StudentSemesterScore();
        $semesters_score = $semester_score->fetchAll();
        echo jsonResponse(200, $semesters_score);
    }
    
    public function AddSemesterScore($data) {
        $semester_score = new StudentSemesterScore();
        $result = $semester_score->create($data);
        echo jsonResponse(201, ['message' => 'SemesterScore created successfully']);
    }

    public function updateSemesterScore($id, $data) {
        $semester_score = new StudentSemesterScore();
        $result = $semester_score->update($id, $data);
        echo jsonResponse(200, ['message' => 'SemesterScore updated successfully']);
    }

    public function deleteSemesterScore($id) {
        $semester_score = new StudentSemesterScore();
        $result = $semester_score->delete($id);
        echo jsonResponse(200, ['message' => 'SemesterScore deleted successfully']);
    }
    
    public function getSemesterScoreById    ($id) {
        $semester_score = new StudentSemesterScore();
        $semester_scores = $semester_score->fetchById($id);
        echo jsonResponse(200, $semester_scores);
    }
}