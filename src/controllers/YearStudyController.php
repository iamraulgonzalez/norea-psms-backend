<?php

require_once __DIR__ . '/../models/Yearstudy.php';
require_once __DIR__ . '/../utils/response.php';    

class YearstudyController {

    public function getAllYearStudies() {
        $yearstudy = new YearStudy();
        $yearstudies = $yearstudy->fetchAll();
        echo jsonResponse(200, $yearstudies);
    }
    
    public function AddYearStudy($data) {
        $yearstudy = new YearStudy();
        $result = $yearstudy->create($data);
        echo jsonResponse(201, ['message' => 'YearStudy created successfully']);
    }

    public function updateYearStudy($id, $data) {
        $yearstudy = new YearStudy();
        $result = $yearstudy->update($id, $data);
        echo jsonResponse(200, ['message' => 'YearStudy updated successfully']);
    }

    public function deleteYearStudy($id) {
        $yearstudy = new YearStudy();
        $result = $yearstudy->delete($id);
        echo jsonResponse(200, ['message' => 'YearStudy deleted successfully']);
    }
    
    public function getYearStudyById($id) {
        $yearstudy = new YearStudy();
        $yearstudys = $yearstudy->fetchById($id);
        echo jsonResponse(200, $yearstudys);
    }
}