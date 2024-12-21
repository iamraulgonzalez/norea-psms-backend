<?php

require_once __DIR__ . '/../models/SubSubject.php';
require_once __DIR__ . '/../utils/response.php';    

class SubSubjectController {

    public function getAllSubSubject() {
        $sub_subject = new SubSubject();
        $sub_subjects = $sub_subject->fetchAll();
        echo jsonResponse(200, $sub_subjects);
    }
    
    public function AddSubSubject($data) {
        $sub_subject = new SubSubject();
        $result = $sub_subject->create($data);
        echo jsonResponse(201, ['message' => 'Sub Subject created successfully']);
    }

    public function updateSubSubject($id, $data) {
        $sub_subject = new SubSubject();
        $result = $sub_subject->updateSubSubject($id, $data);
        echo jsonResponse(200, ['message' => 'Sub Subject updated successfully']);
    }

    public function deleteSubSubject($id) {
        $sub_subject = new SubSubject();
        $result = $sub_subject->deleteSubSubject($id);
        echo jsonResponse(200, ['message' => 'Sub Subject deleted successfully']);
    }
    
    public function getSubSubjectById($id) {
        $sub_subject = new SubSubject();
        $sub_subjects = $sub_subject->fetchSubSubjectById($id);
        echo jsonResponse(200, $sub_subjects);
    }
}