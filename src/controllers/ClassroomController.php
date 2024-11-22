<?php

require_once __DIR__ . '/../models/Classroom.php';
require_once __DIR__ . '/../utils/response.php';    

class ClassroomController {

    public function getAllClassrooms() {
        $classroom = new Classroom();
        $classrooms = $classroom->fetchAll();
        echo jsonResponse(200, $classrooms);
    }
    
    public function addClassroom($data) {
        $classroom = new Classroom();
        $result = $classroom->create($data);
        echo jsonResponse(201, ['message' => 'Classroom created successfully']);
    }

    public function updateClassroom($id, $data) {
        $classroom = new Classroom();
        $result = $classroom->updateClassroom($id, $data);
        echo jsonResponse(200, ['message' => 'Classroom updated successfully']);
    }

    public function deleteClassroom($id) {
        $classroom = new Classroom();
        $result = $classroom->deleteClassroom($id);
        echo jsonResponse(200, ['message' => 'Classroom deleted successfully']);
    }
    
    public function getClassroomById($id) {
        $classroom = new Classroom();
        $classroom = $classroom->fetchClassroomById($id);
        echo jsonResponse(200, $classroom);
    }
}