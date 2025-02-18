<?php
require_once __DIR__ . '/../models/SchoolSession.php';
require_once __DIR__ . '/../utils/response.php';

class SchoolSessionController {
    public function getAllSchoolSessions() {
        $schoolSession = new SchoolSession();
        $schoolSessions = $schoolSession->fetchAll();
        echo jsonResponse(200, $schoolSessions);
    }

    public function getSchoolSessionById($id) {
        $schoolSession = new SchoolSession();
        $schoolSessions = $schoolSession->fetchById($id);
        echo jsonResponse(200, $schoolSessions);
    }

}