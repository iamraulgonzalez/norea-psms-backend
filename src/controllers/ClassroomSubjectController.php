<?php
require_once __DIR__ . '/../models/ClassroomSubject.php';
require_once __DIR__ . '/../utils/response.php';

class ClassroomSubjectController {
    private $classroomSubject;

    public function __construct() {
        $this->classroomSubject = new ClassroomSubject();
    }

    public function addSubjectsToClass($data) {
        $result = $this->classroomSubject->addSubjectsToClass($data['class_id'], $data['sub_codes']);
        echo jsonResponse(200, $result);
    }

    public function getClassSubjects($classId) {
        $result = $this->classroomSubject->getSubjectsByClassId($classId);
        echo jsonResponse(200, $result);
    }

    public function deleteSubjectFromClass($classId, $subCode) {
        if (!$classId || !$subCode) {
            echo jsonResponse(400, [
                'status' => 'error',
                'message' => 'Class ID and Subject Code are required'
            ]);
            return;
        }

        $result = $this->classroomSubject->deleteSubjectFromClass($classId, $subCode);
        
        if ($result['status'] === 'success') {
            echo jsonResponse(200, $result);
        } else {
            echo jsonResponse(400, $result);
        }
    }
} 