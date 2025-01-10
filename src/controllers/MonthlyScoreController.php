<?php
require_once __DIR__ . '/../models/MonthlyScore.php';
require_once __DIR__ . '/../utils/response.php';

class MonthlyScoreController {
    public function getAllMonthlyScores() {
        $monthly_score = new MonthlyScore();
        $monthly_scores = $monthly_score->fetchAll();
        echo jsonResponse(200, $monthly_scores);
    }

    public function addMonthlyScore($data) {
        $monthly_score = new MonthlyScore();
        $result = $monthly_score->create($data);
        echo jsonResponse(201, ['message' => 'MonthlyScore created successfully']);
    }

    public function updateMonthlyScore($id, $data) {
        $monthly_score = new MonthlyScore();
        $result = $monthly_score->update($id, $data);
        echo jsonResponse(200, ['message' => 'MonthlyScore updated successfully']);
    }

    public function deleteMonthlyScore($id) {
        $monthly_score = new MonthlyScore();
        $result = $monthly_score->delete($id);
        echo jsonResponse(200, ['message' => 'MonthlyScore deleted successfully']);
    }

    public function getMonthlyScoreById($id) {
        $monthly_score = new MonthlyScore();
        $monthly_scores = $monthly_score->fetchById($id);
        echo jsonResponse(200, $monthly_scores);
    }
}