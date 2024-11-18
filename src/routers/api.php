<?php
function route($uri, $method) {
    switch ($uri) {
        case '/students':
            require_once __DIR__ . '/../controllers/StudentController.php';
            $controller = new StudentController();
            if ($method === 'GET') $controller->getAllStudents();
            if ($method === 'POST') $controller->addStudent($_POST);
            break;

        case '/teachers':
            require_once __DIR__ . '/../controllers/TeacherController.php';
            $controller = new TeacherController();
            if ($method === 'GET') $controller->getAllTeachers();
            break;

        default:
            echo json_encode(['message' => 'Route not found']);
            break;
    }
}