<?php

require_once __DIR__ . '/../models/SubSubject.php';
require_once __DIR__ . '/../utils/response.php';    

class SubSubjectController {
   
    public function getAllSubSubjects() {
        $subSubject = new SubSubject();
        $subSubjects = $subSubject->fetchAll();
        echo jsonResponse(200, $subSubjects);
    }

    public function getSubSubjectsBySubjectCode($subjectCode) {
        $subSubject = new SubSubject();
        $subSubjects = $subSubject->fetchBySubjectCode($subjectCode);
        echo jsonResponse(200, $subSubjects);
    }

    public function addSubSubject($data) {
        $subSubject = new SubSubject();
        $result = $subSubject->create($data);
        echo jsonResponse(201, ['message' => 'Sub-Subject created successfully']);
    }

    public function updateSubSubject($id, $data) {
        $subSubject = new SubSubject();
        $result = $subSubject->update($id, $data);
        echo jsonResponse(200, ['message' => 'Sub-Subject updated successfully']);
    }

    public function deleteSubSubject($id) {
        $subSubject = new SubSubject();
        $result = $subSubject->delete($id);
        echo jsonResponse(200, ['message' => 'Sub-Subject deleted successfully']);
    }

    public function getSubSubjectById($id) {
        $subSubject = new SubSubject();
        $subSubject = $subSubject->fetchById($id);
        echo jsonResponse(200, $subSubject);
    }
    
}