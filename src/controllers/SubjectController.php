<?php

require_once __DIR__ . '/../models/Subject.php';
require_once __DIR__ . '/../utils/response.php';    

class SubjectController {

    public function getAllSubject() {
        $subject = new Subject();
        $subjects = $subject->fetchAll();
        echo jsonResponse(200, $subjects);
    }
    
    public function AddSubject($data) {
        $subject = new Subject();
        $result = $subject->create($data);
        echo jsonResponse(201, ['message' => 'Subject created successfully']);
    }

    public function updateSubject($id, $data) {
        $subject = new Subject();
        $result = $subject->updateSubject($id, $data);
        echo jsonResponse(200, ['message' => 'Subject updated successfully']);
    }

    public function deleteSubject($id) {
        $subject = new Subject();
        $result = $subject->deleteSubject($id);
        echo jsonResponse(200, ['message' => 'Subject deleted successfully']);
    }
    
    public function getSubjectById($id) {
        $subject = new Subject();
        $subjects = $subject->fetchSubjectById($id);
        echo jsonResponse(200, $subjects);
    }
}