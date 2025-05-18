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
        echo jsonResponse(201, ['message' => 'បង្កើតខែបានជោគជ័យ']);
    }

    public function updateMonthly($id, $data) {
        try {
            $monthly = new Monthly();
            $result = $monthly->update($id, $data);
            if($result['status'] == 'success'){
                echo jsonResponse(200, ['message' => $result['message']]);
            }else{
                echo jsonResponse(400, ['message' => $result['message']]);
            }
        } catch (PDOException $e) {
            error_log("Error in updateMonthly: " . $e->getMessage());
            echo jsonResponse(500, ['message' => 'Failed to update monthly']);
        }
    }

    public function deleteMonthly($id) {
        try {
            $monthly = new Monthly();
            $result = $monthly->delete($id);
            if($result['status'] == 'success'){
                echo jsonResponse(200, ['message' => $result['message']]);
            }else{
                echo jsonResponse(400, ['message' => $result['message']]);
            }
        } catch (PDOException $e) {
            error_log("Error in deleteMonthly: " . $e->getMessage());
            echo jsonResponse(500, ['message' => 'Failed to delete monthly']);
        }
    }

    public function getMonthlyById($id) {
        $monthly = new Monthly();
        $monthlies = $monthly->fetchById($id);
        echo jsonResponse(200, $monthlies);
    }
}
