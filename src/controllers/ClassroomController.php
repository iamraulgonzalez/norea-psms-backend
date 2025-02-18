<?php

require_once __DIR__ . '/../models/Classroom.php';
require_once __DIR__ . '/../utils/response.php';    

class ClassroomController {

    public function getAllClassrooms() {
        $classroom = new Classroom();
        $classrooms = $classroom->fetchAll();
        echo jsonResponse(200, $classrooms);
    }

    public function getClassesByGrade($gradeId) {
        $classroom = new Classroom();
        $classes = $classroom->getClassesByGrade($gradeId);
        echo jsonResponse(200, $classes);
    }
    
    public function addClassroom($data) {
        $classroom = new Classroom();
        $result = $classroom->create($data);
        
        if ($result['status'] === 'success') {
            jsonResponse(201, $result);
        } else {
            jsonResponse(400, $result);
        }
    }

    public function updateClassroom($id, $data) {
        $classroom = new Classroom();
        $result = $classroom->update($id, $data);
        
        if ($result['status'] === 'success') {
            jsonResponse(200, $result);
        } else {
            jsonResponse(400, $result);
        }
    }

    public function deleteClassroom($id) {
        $classroom = new Classroom();
        $result = $classroom->delete($id);
        echo jsonResponse(200, ['message' => 'Classroom deleted successfully']);
    }
    
    public function getClassroomById($id) {
        $classroom = new Classroom();
        $classroom = $classroom->fetchById($id);
        echo jsonResponse(200, $classroom);
    }

    public function getClassroomCount() {
        $classroom = new Classroom();
        try {
            $count = $classroom->getCount();
            jsonResponse(200, [

                'status' => 'success',
                'count' => (int)$count

            ]);
        } catch (Exception $e) {
            errorResponse(500, 'Failed to get classroom count');
        }
    }

    public function getClassesByGradeAndSession($gradeId, $sessionId) {
        $classroom = new Classroom();
        $classes = $classroom->getClassesByGradeAndSession($gradeId, $sessionId);
        echo jsonResponse(200, $classes);
    }
}