<?php

require_once __DIR__ . '/../models/Academic.php';
require_once __DIR__ . '/../utils/response.php';    

class AcademicController {

    public function getAllAcademics() {
        $academic = new Academic();
        $result = $academic->fetchAll();
        echo jsonResponse(200, $result);
    }
    
    public function AddAcademic($data) {
        try {
            $academic = new Academic();
            $result = $academic->create($data);
            
        if ($result['status'] === 'success') {
            echo jsonResponse(201, $result);
        } else {
                echo jsonResponse(400, $result);
            }
        } catch (PDOException $e) {
            echo jsonResponse(500, ['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
        }
    }

    public function updateAcademic($id, $data) {
        try {
            $academic = new Academic();
            $result = $academic->update($id, $data);
            
        if ($result['status'] === 'success') {
            echo jsonResponse(200, $result);
        } else {
                echo jsonResponse(400, $result);
            }
        } catch (PDOException $e) {
            echo jsonResponse(500, ['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
        }
    }

    public function deleteAcademic($id) {
        try {
            $academic = new Academic();
            $result = $academic->delete($id);
        if ($result['status'] === 'success') {
            echo jsonResponse(200, $result);
        } else {
            echo jsonResponse(400, $result);
        }
        } catch (PDOException $e) {
            echo jsonResponse(500, ['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
        }
    }
    
    public function getAcademicById($id) {
        $academic = new Academic();
        $result = $academic->fetchById($id);
        
        if ($result['status'] === 'success') {
            echo jsonResponse(200, $result);
        } else {
            echo jsonResponse(404, $result);
        }
    }
}