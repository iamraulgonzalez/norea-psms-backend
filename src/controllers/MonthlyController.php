<?php
require_once __DIR__ . '/../models/Monthly.php';
require_once __DIR__ . '/../utils/response.php';

class MonthlyController {

    public function getAllMonthlies() {
        $monthly = new Monthly();
        $monthlies = $monthly->fetchAll();
        echo jsonResponse(200, $monthlies);
    }

    public function addMonthly($data) {
        $monthly = new Monthly();
        $result = $monthly->create($data);
        echo jsonResponse(201, ['message' => 'Monthly created successfully']);
    }

    public function updateMonthly($id, $data) {
        $monthly = new Monthly();
        $result = $monthly->update($id, $data);
        echo jsonResponse(200, ['message' => 'Monthly updated successfully']);
    }

    public function deleteMonthly($id) {
        $monthly = new Monthly();
        $result = $monthly->delete($id);
        echo jsonResponse(200, ['message' => 'Monthly deleted successfully']);
    }

    public function getMonthlyById($id) {
        $monthly = new Monthly();
        $monthlies = $monthly->fetchById($id);
        echo jsonResponse(200, $monthlies);
    }
}
