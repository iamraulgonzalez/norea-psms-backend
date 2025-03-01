<?php
require_once dirname(__DIR__) . '/middleware/cors.php';
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/controllers/UserController.php';
require_once dirname(__DIR__) . '/utils/response.php';
require_once dirname(__DIR__) . '/middleware/AuthMiddleware.php';

// Get the request URI and remove any query strings
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

function route($uri, $method) {
    $uri = str_replace('/api', '', $uri);
    $uriParts = explode('/', trim($uri, '/'));

    // Public routes
    if ($method === 'POST' && $uri === '/users/login') {
        try {
            $controller = new UserController();
            return $controller->login();
        } catch (Exception $e) {
            error_log("Login route error: " . $e->getMessage());
            return jsonResponse(500, ['error' => $e->getMessage()]);
        }
    }

    // Verify token route
    if ($method === 'GET' && $uri === '/users/verify-token') {
        AuthMiddleware::authenticate();
        $controller = new UserController();
        return $controller->verifyToken();
    }

    // Protected routes - require authentication
    if (!AuthMiddleware::verifyAuth()) {
        return jsonResponse(401, ['error' => 'Unauthorized']);
    }

    if (count($uriParts) > 1) {
        $resource = $uriParts[0];
        $action = $uriParts[1];
        
        switch ($resource) {
            case 'students':
                require_once dirname(__DIR__) . '/controllers/StudentInfoController.php';
                $controller = new StudentInfoController();
                
                if ($method === 'GET' && $action === 'getAllStudents') {
                    $controller->getAllStudents();
                    return;
                }
                if ($method === 'GET' && $action === 'getStudentsByClassId') {
                    if (isset($uriParts[2])) {
                        $controller->getStudentsByClassId($uriParts[2]);
                    } else {
                        echo json_encode(['message' => 'Class ID not provided']);
                    }
                }
                if ($method === 'POST' && $action === 'addStudent') {
                    $data = json_decode(file_get_contents('php://input'), true);
                    if ($data) {
                        $controller->addStudent($data);
                    } else {
                        echo json_encode(['message' => 'Invalid input data']);
                    }
                }

                if ($method === 'POST' && $action === 'updateStudent') {
                    if (isset($uriParts[2])) {
                        $id = $uriParts[2];
                        $data = json_decode(file_get_contents('php://input'), true);
                        if ($data) {
                            $controller->updateStudent($id, $data);
                        } else {
                            echo json_encode(['message' => 'Invalid input data']);
                        }
                    } else {
                        echo json_encode(['message' => 'Student ID not provided']);
                    }
                }

                if ($method === 'POST' && $action === 'deleteStudent') {
                    if (isset($uriParts[2])) {
                        $controller->deleteStudent($uriParts[2]);
                    } else {
                        echo json_encode(['message' => 'Student ID not provided']);
                    }
                }

                if ($method === 'GET' && $action === 'getStudentById') {
                    if (isset($uriParts[2])) {
                        $controller->getStudentById($uriParts[2]);
                    } else {
                        echo json_encode(['message' => 'Student ID not provided']);
                    }
                }

                if ($method === 'GET' && $action === 'getStudentsByGrade' && isset($uriParts[2])) {
                    $controller->getStudentsByGradeId($uriParts[2]);
                }

                if ($method === 'GET' && $action === 'count') {
                    $controller->getStudentCount();
                    return;
                }

                if ($method === "POST" && $action === "promote" && isset($uriParts[2])) {
                    $controller->promoteStudent($uriParts[2]);
                }

                // Add route for promoting students by grade
                if ($method === "POST" && $action === "promoteByGrade") {
                    $data = json_decode(file_get_contents('php://input'), true);
                    if ($data && isset($data['current_grade_id']) && isset($data['new_grade_id'])) {
                        $controller->promoteStudentsByGrade($data['current_grade_id'], $data['new_grade_id']);
                    } else {
                        echo json_encode([
                            'status' => 'error',
                            'message' => 'Missing required grade IDs'
                        ]);
                    }
                }
                break;

            case 'teachers':
                require_once dirname(__DIR__) . '/controllers/TeacherController.php';
                $controller = new TeacherController();
                
                if ($method === 'GET' && $action === 'getAllTeachers') {
                    $controller->getAllTeachers();
                }
                if ($method === 'GET' && $action === 'getTeachersByClassId') {
                    if (isset($uriParts[2])) {
                        $controller->getTeachersByClassId($uriParts[2]);
                    } else {
                        echo json_encode(['message' => 'Class ID not provided']);
                    }
                }
                if ($method === 'POST' && $action === 'AddTeacher') {
                    $data = json_decode(file_get_contents('php://input'), true);
                    if ($data) {
                        $controller->addTeacher($data);
                    } else {
                        echo json_encode(['message' => 'Invalid input data']);
                    }
                }
                if ($method === 'POST' && $action === 'updateTeacher') {
                    if (isset($uriParts[2])) {
                        $id = $uriParts[2];
                        $data = json_decode(file_get_contents('php://input'), true);
                        if ($data) {
                            $controller->updateTeacher($id, $data);
                        } else {
                            echo json_encode(['message' => 'Invalid input data']);
                        }
                    } else {
                        echo json_encode(['message' => 'Teacher ID not provided']);
                    }
                }
                if ($method === 'DELETE' && $action === 'deleteTeacher') {
                    if (isset($uriParts[2])) {
                        $controller->deleteTeacher($uriParts[2]);
                    } else {
                        echo json_encode(['message' => 'Teacher ID not provided']);
                    }
                }
                if ($method === 'GET' && $action === 'getTeacherById') {
                    if (isset($uriParts[2])) {
                        $controller->getTeacherById($uriParts[2]);
                    } else {
                        echo json_encode(['message' => 'Teacher ID not provided']);
                    }
                }

                if ($method === 'GET' && $action === 'count') {
                    $controller->getTeacherCount();
                    return;
                }
                break;
            
            case 'classrooms':
                require_once dirname(__DIR__) . '/controllers/ClassroomController.php';
                $controller = new ClassroomController();
                
                if ($method === 'GET' && $action === 'getAllClassrooms') {
                    $controller->getAllClassrooms();
                }
                if ($method === 'GET' && $action === 'getClassesByGrade') {
                    if (isset($uriParts[2])) {
                        $controller->getClassesByGrade($uriParts[2]);
                    } else {
                        echo json_encode(['message' => 'Grade ID not provided']);
                    }
                }
                if ($method === 'POST' && $action === 'addClassroom') {
                    $data = json_decode(file_get_contents('php://input'), true);
                    if ($data) {
                        $controller->addClassroom($data);
                    } else {
                        echo json_encode(['message' => 'Invalid input data']);
                    }
                }
                if ($method === 'PUT' && $action === 'updateClassroom') {
                    if (isset($uriParts[2])) {
                        $id = $uriParts[2];
                        $data = json_decode(file_get_contents('php://input'), true);
                        if ($data) {
                            $controller->updateClassroom($id, $data);
                        } else {
                            echo json_encode(['message' => 'Invalid input data']);
                        }
                    } else {
                        echo json_encode(['message' => 'Classroom ID not provided']);
                    }
                }
                if ($method === 'DELETE' && $action === 'deleteClassroom') {
                    if (isset($uriParts[2])) {
                        $controller->deleteClassroom($uriParts[2]);
                    } else {
                        echo json_encode(['message' => 'Classroom ID not provided']);
                    }
                
                }
                if ($method === 'GET' && $action === 'getClassroomById') {
                    if (isset($uriParts[2])) {
                        $controller->getClassroomById($uriParts[2]);
                    } else {
                        echo json_encode(['message' => 'Classroom ID not provided']);
                    }
                }

                if ($method === 'GET' && $action === 'count') {
                    $controller->getClassroomCount();
                }
                break;

                // Assign Subject Grade Routes
                case 'assign_subject_grade':
                    require_once dirname(__DIR__) . '/controllers/AssignSubjectGradeController.php';
                    $controller = new AssignSubjectGradeController();
                    
                    // Get all assign subject grades (already assigned)
                    if($method === "GET" && $action === "getAllAssignSubjectGrades"){
                        $controller->getAllAssignSubjectGrades();
                    }

                    // Add subjects and/or sub-subjects to grade
                    if($method === "POST" && $action === "assignSubjectsToGrade"){
                        $data = json_decode(file_get_contents('php://input'), true);
                        if($data){
                            $controller->addSubjectsToGrade();
                        } else {
                            echo jsonResponse(400, ['message' => 'Invalid input data']);
                        }
                    }

                    // Get subjects and sub-subjects by grade ID
                    if($method === "GET" && $action === "getSubjectsByGradeId"){
                        if(isset($uriParts[2])){
                            $controller->getSubjectsByGradeId($uriParts[2]);
                        } else {
                            echo jsonResponse(400, ['message' => 'Grade ID not provided']);
                        }
                    }

                    // Delete subject or sub-subject from grade
                    if($method === "DELETE" && $action === "deleteSubjectFromGrade"){
                        if(isset($uriParts[2])){
                            $controller->deleteSubjectFromGrade($uriParts[2]);
                        } else {
                            echo jsonResponse(400, ['message' => 'Assign Subject Grade ID not provided']);
                        }
                    }
                    break;

                // Academic Routes
                case 'academics':
                    require_once dirname(__DIR__) . '/controllers/AcademicController.php';
                    $controller = new AcademicController();
                    
                    if ($method === 'GET' && $action === 'getAllAcademics') {
                        $controller->getAllAcademics();
                    }
                    if ($method === 'POST' && $action === 'addAcademic') {
                        $data = json_decode(file_get_contents('php://input'), true);
                        if ($data) {
                            $controller->AddAcademic($data);
                        } else {
                            echo json_encode(['message' => 'Invalid input data']);
                        }
                    }
                    if ($method === 'POST' && $action === 'updateAcademic') {
                        if (isset($uriParts[2])) {
                            $id = $uriParts[2];
                            $data = json_decode(file_get_contents('php://input'), true);
                            if ($data) {
                                $controller->updateAcademic($id, $data);
                            } else {
                                echo json_encode(['message' => 'Invalid input data']);
                            }
                        } else {
                            echo json_encode(['message' => 'Classroom ID not provided']);
                        }
                    }
                    if ($method === 'DELETE' && $action === 'deleteAcademic') {
                        if (isset($uriParts[2])) {
                            $controller->deleteAcademic($uriParts[2]);
                        } else {
                            echo json_encode(['message' => 'Academic ID not provided']);
                        }
                    
                    }
                    if ($method === 'GET' && $action === 'getAcademicById') {
                        if (isset($uriParts[2])) {
                            $controller->getAcademicById($uriParts[2]);
                        } else {
                            echo json_encode(['message' => 'Academic ID not provided']);
                        }
                    }
                    break;
                    
                    // Subject Routes
                    case "subjects" :
                        require_once dirname(__DIR__) . '/controllers/SubjectController.php';
                        $controller = new SubjectController();
                        if($method === "GET" && $action === "getAllSubjects"){
                            $controller->getAllSubject();
                        }
                        if($method === "POST" && $action === "addSubject"){
                            $data = json_decode(file_get_contents('php://input'), true);
                            if($data){
                                $controller->AddSubject($data);
                            }else{
                                echo json_encode(['message' => 'Invalid input data']);
                            }
                        }
                        if($method === "POST" && $action === "updateSubject"){
                            if(isset($uriParts[2])){
                                $id = $uriParts[2];
                                $data = json_decode(file_get_contents('php://input'), true);
                                if($data){
                                    $controller->updateSubject($id, $data);
                                }else{
                                    echo json_encode(['message' => 'Invalid input data']);
                                }
                            }else{
                                echo json_encode(['message' => 'Subject ID not provided']);
                            }
                        }
                        if($method === "DELETE" && $action === "deleteSubject"){
                            if(isset($uriParts[2])){
                                $controller->deleteSubject($uriParts[2]);
                            }else{
                                echo json_encode(['message' => 'Subject ID not provided']);
                            }
                        
                        }
                        if($method === "GET" && $action === "getSubjectById"){
                            if(isset($uriParts[2])){
                                $controller->getSubjectById($uriParts[2]);
                            }else{
                                echo json_encode(['message' => 'Subject ID not provided']);
                            }
                        }

                        if ($method === 'GET' && $action === 'count') {
                            $controller->getSubjectCount();
                            return;
                        }
                    break;

                     // SubSubject Routes
                    case 'subsubjects':
                        require_once dirname(__DIR__) . '/controllers/SubSubjectController.php';
                        $controller = new SubSubjectController();
                        if ($method === 'GET' && $action === 'getAllSubSubjects') {
                            $controller->getAllSubSubjects();
                        }
                        if ($method === 'POST' && $action === 'addSubSubject') {
                            $data = json_decode(file_get_contents('php://input'), true);
                            if ($data) {
                                $controller->addSubSubject($data);
                            } else {
                                echo json_encode(['message' => 'Invalid input data']);
                            }
                        }
                        if ($method === 'POST' && $action === 'updateSubSubject') {
                            if (isset($uriParts[2])) {
                                $id = $uriParts[2];
                                $data = json_decode(file_get_contents('php://input'), true);
                                if ($data) {
                                    $controller->updateSubSubject($id, $data);
                                } else {
                                    echo json_encode(['message' => 'Invalid input data']);
                                }
                            } else {
                                echo json_encode(['message' => 'SubSubject ID not provided']);
                            }
                        }
                        if ($method === 'DELETE' && $action === 'deleteSubSubject') {
                            if (isset($uriParts[2])) {
                                $controller->deleteSubSubject($uriParts[2]);
                            } else {
                                echo json_encode(['message' => 'SubSubject ID not provided']);
                            }
                        }
                        if ($method === 'GET' && $action === 'getSubSubjectById') {
                            if (isset($uriParts[2])) {
                                $controller->getSubSubjectById($uriParts[2]);
                            } else {
                                echo json_encode(['message' => 'SubSubject ID not provided']);
                            }
                        }
                        if ($method === 'GET' && $action === 'getBySubjectCode') {
                            if (isset($uriParts[2])) {
                                $controller->getSubSubjectsBySubjectCode($uriParts[2]);
                            } else {
                                echo jsonResponse(400, ['message' => 'Subject code not provided']);
                            }
                        }
                        if ($method === "GET" && $action === "getSubSubjectsBySubject") {
                            if (isset($uriParts[2])) {
                                $controller->getSubSubjectsBySubjectCode($uriParts[2]);
                            }
                        }
                        break;

                        // Semester Routes
                        case 'semesters':
                            require_once dirname(__DIR__) . '/controllers/SemesterController.php';
                            $controller = new SemesterController();
                            if($method === "GET" && $action === "getAllSemesters"){
                                $controller->getAllSemesters();
                            }
                            if($method === "POST" && $action === "addSemester"){
                                $data = json_decode(file_get_contents('php://input'), true);
                                if($data){
                                    $controller->addSemester($data);
                                }else{
                                    echo json_encode(['message' => 'Invalid input data']);
                                }
                            }
                            if($method === "POST" && $action === "updateSemester"){
                                if(isset($uriParts[2])){
                                    $id = $uriParts[2];
                                    $data = json_decode(file_get_contents('php://input'), true);
                                    if($data){
                                        $controller->updateSemester($id, $data);
                                    }else{
                                        echo json_encode(['message' => 'Invalid input data']);
                                    }
                                }
                            }
                            if($method === "DELETE" && $action === "deleteSemester"){
                                if(isset($uriParts[2])){
                                    $controller->deleteSemester($uriParts[2]);
                                }else{
                                    echo json_encode(['message' => 'Semester ID not provided']);
                                }
                            }
                            if($method === "GET" && $action === "getSemesterById"){
                                if(isset($uriParts[2])){
                                    $controller->getSemesterById($uriParts[2]);
                                }else{
                                    echo json_encode(['message' => 'Semester ID not provided']);
                                }
                            }
                        break;

                        // Monthly Score Routes
                        case 'monthly_score':
                            require_once dirname(__DIR__) . '/controllers/StudentMonthlyScoreController.php';
                            $controller = new StudentMonthlyScoreController();
                            
                            if ($method === "GET" && $action === "getAllMonthlyScores") {
                                $controller->getAllMonthlyScores();
                            }
                            
                            if ($method === "POST" && $action === "addMonthlyScore") {
                                $data = json_decode(file_get_contents('php://input'), true);
                                if ($data) {
                                    $controller->addMonthlyScore($data);
                                }
                            }
                            
                            if ($method === "PUT" && $action === "updateMonthlyScore") {
                                if (isset($uriParts[2])) {
                                    $data = json_decode(file_get_contents('php://input'), true);
                                    if ($data) {
                                        $controller->updateMonthlyScore($uriParts[2], $data);
                                    }
                                }
                            }
                            
                            if ($method === "DELETE" && $action === "deleteMonthlyScore") {
                                if (isset($uriParts[2])) {
                                    $controller->deleteMonthlyScore($uriParts[2]);
                                }
                            }
                            
                            if ($method === "GET" && $action === "getStudentMonthlyScores") {
                                if (isset($uriParts[2])) {
                                    $controller->getStudentMonthlyScores($uriParts[2]);
                                }
                            }
                            
                            if ($method === "GET" && $action === "getClassMonthlyScores") {
                                if (isset($uriParts[2])) {
                                    $controller->getClassMonthlyScores($uriParts[2]);
                                }
                            }

                            if ($method === "GET" && $action === "getMonthlyScoreById") {
                                if (isset($uriParts[2])) {
                                    $controller->getMonthlyScoreById($uriParts[2]);
                                }
                            }

                            if ($method === "GET" && $action === "getAllScoresGrouped") {
                                $controller->getAllScoresGrouped();
                            }

                            if ($method === "GET" && $action === "getStudentScoresOrdered") {
                                if (isset($uriParts[2])) {
                                    $controller->getStudentScoresOrdered($uriParts[2]);
                                } else {
                                    echo jsonResponse(400, [
                                        'status' => 'error',
                                        'message' => 'Student ID is required'
                                    ]);
                                }
                            }
                        break;

                        // Monthly Routes
                        case 'monthly':
                            require_once dirname(__DIR__) . '/controllers/MonthlyController.php';
                            $controller = new MonthlyController();
                            if($method === "GET" && $action === "getAllMonthly"){
                                $controller->getAllMonthlies();
                            }
                            if($method === "POST" && $action === "addMonthly"){
                                $data = json_decode(file_get_contents('php://input'), true);
                                if($data){
                                    $controller->addMonthly($data);
                                }else{
                                    echo json_encode(['message' => 'Invalid input data']);
                                }
                            }
                            if($method === "POST" && $action === "updateMonthly"){
                                if(isset($uriParts[2])){
                                    $id = $uriParts[2];
                                    $data = json_decode(file_get_contents('php://input'), true);
                                    if($data){
                                        $controller->updateMonthly($id, $data);
                                    }else{
                                        echo json_encode(['message' => 'Invalid input data']);
                                    }
                                }
                            }
                            if($method === "DELETE" && $action === "deleteMonthly"){
                                if(isset($uriParts[2])){
                                    $controller->deleteMonthly($uriParts[2]);
                                }else{
                                    echo json_encode(['message' => 'Monthly ID not provided']);
                                }
                            }
                            if($method === "GET" && $action === "getMonthlyById"){
                                if(isset($uriParts[2])){
                                    $controller->getMonthlyById($uriParts[2]);
                                }else{
                                    echo json_encode(['message' => 'Monthly ID not provided']);
                                }
                            }
                        break;

                        // Grade Routes
                        case 'grade':
                            require_once dirname(__DIR__) . '/controllers/GradeController.php';
                            $controller = new GradeController();
                            if($method === "GET" && $action === "getAllGrades"){
                                $controller->getAllGrades();
                            }
                            if($method === "POST" && $action === "addGrade"){
                                $data = json_decode(file_get_contents('php://input'), true);
                                if($data){
                                    $controller->addGrade($data);
                                }else{
                                    echo json_encode(['message' => 'Invalid input data']);
                                }
                            }
                            if($method === "POST" && $action === "updateGrade"){
                                if(isset($uriParts[2])){
                                    $id = $uriParts[2];
                                    $data = json_decode(file_get_contents('php://input'), true);
                                    if($data){
                                        $controller->updateGrade($id, $data);
                                    }else{
                                        echo json_encode(['message' => 'Invalid input data']);
                                    }
                                }
                            }
                            if($method === "DELETE" && $action === "deleteGrade"){
                                if(isset($uriParts[2])){
                                    $controller->deleteGrade($uriParts[2]);
                                }else{
                                    echo json_encode(['message' => 'Grade ID not provided']);
                                }
                            }
                            if($method === "GET" && $action === "getGradeById"){
                                if(isset($uriParts[2])){
                                    $controller->getGradeById($uriParts[2]);
                                }else{
                                    echo json_encode(['message' => 'Grade ID not provided']);
                                }
                            }
                        break;

                        // User Routes
                        case 'users':
                            require_once dirname(__DIR__) . '/controllers/UserController.php';
                            require_once dirname(__DIR__) . '/middleware/AuthMiddleware.php';
                            $controller = new UserController();
                            
                            // Public routes
                            if ($method === 'POST' && $action === 'login') {
                                return $controller->login();
                            }
                            
                            if ($method === 'POST' && $action === 'register') {
                                return $controller->register();
                            }
                            
                            // Protected routes - require authentication
                            if (!AuthMiddleware::verifyAuth()) {
                                return jsonResponse(401, ['error' => 'Unauthorized']);
                            }
                            
                            switch ($action) {
                                case 'getAllUsers':
                                    return $controller->getAllUsers();
                                case 'update':
                                    if (isset($uriParts[2])) {
                                        return $controller->update($uriParts[2]);
                                    }
                                    break;
                                case 'logout':
                                    return $controller->logout();
                                case 'current':
                                    return $controller->getCurrentUser();
                            }
                            break;
                        case 'school_session':
                            require_once dirname(__DIR__) . '/controllers/SchoolSessionController.php';
                            $controller = new SchoolSessionController();
                            if($method === "GET" && $action === "getAllSchoolSessions"){
                                $controller->getAllSchoolSessions();
                            }


                            if($method === "GET" && $action === "getSchoolSessionById"){
                                if(isset($uriParts[2])){
                                    $controller->getSchoolSessionById($uriParts[2]);
                                }else{
                                    echo json_encode(['message' => 'SchoolSession ID not provided']);
                                }
                            }
                        break;
                            
                        case 'rankings':
                            require_once dirname(__DIR__) . '/controllers/RankingsController.php';
                            $controller = new RankingsController();

                            if ($method === "GET" && $action === "monthly") {
                                $controller->getMonthlyRankings();
                            }

                            if ($method === "GET" && $action === "subjects") {
                                $controller->getMonthlySubjectScores();
                            }

                            if ($method === "GET" && $action === "studentHistory") {
                                if (isset($uriParts[2])) {
                                    $controller->getStudentRankingHistory($uriParts[2]);
                                } else {
                                    echo jsonResponse(400, [
                                        'status' => 'error',
                                        'message' => 'Student ID is required'
                                    ]);
                                }
                            }

                            if ($method === "GET" && $action === "topStudents") {
                                $controller->getTopStudents();
                            }
                            break;

                        default:
                            echo json_encode(['message' => 'Route not found']);


                            break;
                    }
        
    } else {
        echo json_encode(['message' => 'Route not found']);
    }
}

// Execute routing
route($uri, $_SERVER['REQUEST_METHOD']);