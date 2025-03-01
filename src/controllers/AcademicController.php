<?php

require_once __DIR__ . '/../models/Academic.php';
require_once __DIR__ . '/../utils/response.php';    

class AcademicController {

    public function getAllAcademics() {
        $academic = new Academic();
        $academics = $academic->fetchAll();
        echo jsonResponse(200, $academics);
    }
    
    public function AddAcademic($data) {
        $academic = new Academic();
        $result = $academic->create($data);
        echo jsonResponse(201, ['message' => 'Academic created successfully']);
    }

    public function updateAcademic($id, $data) {
        $academic = new Academic();
        $result = $academic->update($id, $data);
        echo jsonResponse(200, ['message' => 'Academic updated successfully']);
    }

    public function deleteAcademic($id) {
        $academic = new Academic();
        $result = $academic->delete($id);
        echo jsonResponse(200, ['message' => 'Academic deleted successfully']);
    }
    
    public function getAcademicById($id) {
        $academic = new Academic();
        $academics = $academic->fetchById($id);
        echo jsonResponse(200, $academics);
    }
}